<?php

namespace Tests\Feature\Http\Controllers\Api\VideoController;

use App\Http\Resources\VideoGenreResource;
use App\Http\Resources\VideoResource;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Video;
use Illuminate\Support\Arr;
use Tests\Traits\TestDatabase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class VideoControllerCrudTest extends BaseVideoControllerTestCase
{

    use TestDatabase, TestSaves, TestValidations, TestResources;

    public function testIndex()
    {
        $response = $this->get(route('videos.index'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'meta' => ['per_page' => 15]
            ])
            ->assertJsonStructure([
                'data' => [
                    '*' => $this->serializedFields
                ],
                'links' => [],
                'meta' => [],
            ]);

        $resource = VideoResource::collection(collect([$this->video]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->serializedFields]);

        $id = $response->json('data.id');
        $resource = new VideoResource(Video::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationDataRequired()
    {
        $data = [
            'title' => '',
            'description' => '',
            'year_lauched' => '',
            'rating' => '',
            'duration' => '',
            'categories_id' => '',
            'genres_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationDataMax()
    {
        $data = [
            'title' => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationDataInteger()
    {
        $data = [
            'duration' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'integer');
        $this->assertInvalidationInUpdateAction($data, 'integer');
    }

    public function testInvalidationBoolean()
    {
        $data = [
            'opened' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
    }

    public function testInvalidationDataYearLauchedField()
    {
        $data = [
            'year_lauched' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'date_format', ['format' => 'Y']);
        $this->assertInvalidationInUpdateAction($data, 'date_format', ['format' => 'Y']);
    }

    public function testInvalidationRatingField()
    {
        $data = [
            'rating' => 0
        ];
        $this->assertInvalidationInStoreAction($data, 'in');
        $this->assertInvalidationInUpdateAction($data, 'in');
    }

    public function testInvalidationCategoriesIdField()
    {
        $data = [
            'categories_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'categories_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $category = factory(Category::class)->create();
        $category->delete();
        $data = [
            'categories_id' => [$category->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');
    }

    public function testInvalidationGenresIdField()
    {
        $data = [
            'genres_id' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'array');
        $this->assertInvalidationInUpdateAction($data, 'array');

        $data = [
            'genres_id' => [100]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
        $this->assertInvalidationInUpdateAction($data, 'exists');

        $genre = factory(Genre::class)->create();
        $genre->delete();
        $data = [
            'genres_id' => [$genre->id]
        ];
        $this->assertInvalidationInStoreAction($data, 'exists');
    }


    public function testStoreAndUpdateWithoutFiles()
    {

        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);
        $data = [
            [
                'send_data' => $this->sendData,
                'test_data' => $testData + ['opened' => false],
            ],
            [
                'send_data' => $this->sendData + [
                        'opened' => true,
                    ],
                'test_data' => $testData + ['opened' => true],
            ],
            [
                'send_data' => $this->sendData + [
                        'rating' => Video::RATING_LIST[1],
                    ],
                'test_data' => $testData + ['rating' => Video::RATING_LIST[1]],
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure(['data' => $this->serializedFields]);
            $id = $response->json('data.id');
            $this->assertHasCategory($id, $value['send_data']['categories_id'][0]);
            $this->assertHasGenre($id, $value['send_data']['genres_id'][0]);
            $resource = new VideoResource(Video::find($id));
            $this->assertResource($response, $resource);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure(['data' => $this->serializedFields]);
            $id = $response->json('data.id');
            $this->assertHasCategory($id, $value['send_data']['categories_id'][0]);
            $this->assertHasGenre($id, $value['send_data']['genres_id'][0]);
            $resource = new VideoResource(Video::find($id));
            $this->assertResource($response, $resource);
        }
    }

    protected function assertHasCategory($videoId, $categoryId)
    {
        $this->assertDatabaseHas('category_video', [
            'video_id' => $videoId,
            'category_id' => $categoryId
        ]);
    }

    protected function assertHasGenre($videoId, $genreId)
    {
        $this->assertDatabaseHas('genre_video', [
            'video_id' => $videoId,
            'genre_id' => $genreId
        ]);
    }

    public function testDestroy()
    {
        $response = $this->get(route('videos.show', ['video' => $this->video->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->serializedFields]);

        $response = $this->delete(route('videos.destroy', ['video' => $this->video->id]));
        $response->assertStatus(204);

        $this->assertNull(Video::find($this->video->id));
        $this->assertNotNull(Video::withTrashed()->find($this->video->id));
    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();
        $genre = factory(Genre::class)->create();
        $genre->categories()->sync($categoriesId);
        $genreId = $genre->id;

        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);
        $data = $testData + [
                'genres_id' => [$genreId],
                'categories_id' => [$categoriesId[0]]
            ];
        $this->assertStoreDatabaseHas($data, 'category_video', 'category_id', $categoriesId[0], 'video_id');

        $data = $testData + [
                'genres_id' => [$genreId],
                'categories_id' => [$categoriesId[1], $categoriesId[2]]
            ];
        $this->assertUpdateDatabaseMissing($data, 'category_video', 'category_id', $categoriesId[0], 'video_id');
        $this->assertUpdateDatabaseHas($data, 'category_video', 'category_id', $categoriesId[1], 'video_id');
        $this->assertUpdateDatabaseHas($data, 'category_video', 'category_id', $categoriesId[2], 'video_id');
    }

    public function testSyncGenres()
    {
        $genres = factory(Genre::class, 3)->create();
        $genresId = $genres->pluck('id')->toArray();
        $categoryId = factory(Category::class)->create()->id;
        $genres->each(function ($genre) use ($categoryId) {
            $genre->categories()->sync($categoryId);
        });

        $testData = Arr::except($this->sendData, ['categories_id', 'genres_id']);
        $data = $testData + [
                'categories_id' => [$categoryId],
                'genres_id' => [$genresId[0]]
            ];
        $this->assertStoreDatabaseHas($data, 'genre_video', 'genre_id', $genresId[0], 'video_id');

        $data = $testData + [
                'categories_id' => [$categoryId],
                'genres_id' => [$genresId[1], $genresId[2]]
            ];
        $this->assertUpdateDatabaseMissing($data, 'genre_video', 'genre_id', $genresId[0], 'video_id');
        $this->assertUpdateDatabaseHas($data, 'genre_video', 'genre_id', $genresId[1], 'video_id');
        $this->assertUpdateDatabaseHas($data, 'genre_video', 'genre_id', $genresId[2], 'video_id');
    }

    protected function routeStore()
    {
        return route('videos.store');
    }

    protected function routeUpdate()
    {
        return route('videos.update', ['video' => $this->video->id]);
    }

    protected function model()
    {
        return Video::class;
    }

}
