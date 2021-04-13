<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\GenreController;
use App\Http\Resources\GenreResource;
use App\Models\Category;
use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Http\Request as HttpRequest;
use Tests\Exceptions\TestException;
use Tests\TestCase;
use Tests\Traits\TestDatabase;
use Tests\Traits\TestResources;
use Tests\Traits\TestSaves;
use Tests\Traits\TestValidations;

class GenreControllerTest extends TestCase
{

    use DatabaseMigrations, TestValidations, TestSaves, TestDatabase, TestResources;

    private $genre;
    private $sendData;
    private $serializedFields = [
        'id', 'name', 'is_active', 'categories',
        'created_at', 'updated_at', 'deleted_at'
    ];

    protected function setUp(): void
    {
        parent::setUp();
        $this->genre = factory(Genre::class)->create();
        $this->sendData = [
            'name' => 'test'
        ];
    }

    public function testIndex()
    {
        $response = $this->get(route('genres.index'));

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

        $resource = GenreResource::collection(collect([$this->genre]));
        $this->assertResource($response, $resource);
    }

    public function testShow()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));

        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->serializedFields]);

        $id = $response->json('data.id');
        $resource = new GenreResource(Genre::find($id));
        $this->assertResource($response, $resource);
    }

    public function testInvalidationData()
    {
        $data = [
            'name' => '',
            'categories_id' => ''
        ];
        $this->assertInvalidationInStoreAction($data, 'required');
        $this->assertInvalidationInUpdateAction($data, 'required');
    }

    public function testInvalidationDataMax()
    {
        $data = [
            'name' => str_repeat('a', 256)
        ];
        $this->assertInvalidationInStoreAction($data, 'max.string', ['max' => 255]);
        $this->assertInvalidationInUpdateAction($data, 'max.string', ['max' => 255]);
    }

    public function testInvalidationBoolean()
    {

        $data = [
            'is_active' => 'a'
        ];
        $this->assertInvalidationInStoreAction($data, 'boolean');
        $this->assertInvalidationInUpdateAction($data, 'boolean');
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

    public function testStoreAndUpdate()
    {
        $categoryId = factory(Category::class)->create()->id;

        $data = [
            [
                'send_data' => $this->sendData + [
                        'categories_id' => [$categoryId]
                    ],
                'test_data' => $this->sendData + ['is_active' => true]
            ],
            [
                'send_data' => $this->sendData + [
                        'is_active' => false,
                        'categories_id' => [$categoryId]
                    ],
                'test_data' => $this->sendData + ['is_active' => false]
            ]
        ];

        foreach ($data as $key => $value) {
            $response = $this->assertStore(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure(['data' => $this->serializedFields]);
            $id = $response->json('data.id');
            $this->assertHasCategory($id, $categoryId);
            $resource = new GenreResource(Genre::find($id));
            $this->assertResource($response, $resource);

            $response = $this->assertUpdate(
                $value['send_data'],
                $value['test_data'] + ['deleted_at' => null]
            );
            $response->assertJsonStructure(['data' => $this->serializedFields]);
            $id = $response->json('data.id');
            $this->assertHasCategory($id, $categoryId);
            $resource = new GenreResource(Genre::find($id));
            $this->assertResource($response, $resource);

        }
    }

    public function testRollbackStore()
    {
        $controller = \Mockery::mock(GenreController::class);
        $controller
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData);

        $controller
            ->shouldReceive('rulesStore')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(HttpRequest::class);

        $hasError = false;
        try {
            $controller->store($request);
        } catch (TestException $exception) {
            $this->assertCount(1, Genre::all());
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testRollbackUpdate()
    {
        $controller = \Mockery::mock(GenreController::class);
        $controller
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();

        $controller
            ->shouldReceive('findOrFail')
            ->withAnyArgs()
            ->andReturn($this->genre);

        $controller
            ->shouldReceive('validate')
            ->withAnyArgs()
            ->andReturn($this->sendData + ['is_active' => false]);

        $controller
            ->shouldReceive('rulesUpdate')
            ->withAnyArgs()
            ->andReturn([]);

        $controller->shouldReceive('handleRelations')
            ->once()
            ->andThrow(new TestException());

        $request = \Mockery::mock(HttpRequest::class);

        $hasError = false;
        try {
            $controller->update($request, $this->genre->id);
        } catch (TestException $exception) {
            $this->assertNotNull(Genre::find($this->genre->id));
            $hasError = true;
        }
        $this->assertTrue($hasError);
    }

    public function testDestroy()
    {
        $response = $this->get(route('genres.show', ['genre' => $this->genre->id]));
        $response
            ->assertStatus(200)
            ->assertJsonStructure(['data' => $this->serializedFields]);

        $response = $this->delete(route('genres.destroy', ['genre' => $this->genre->id]));
        $response->assertStatus(204);

    }

    public function testSyncCategories()
    {
        $categoriesId = factory(Category::class, 3)->create()->pluck('id')->toArray();

        $data = $this->sendData + ['categories_id' => [$categoriesId[0]]];
        $this->assertStoreDatabaseHas($data, 'category_genre', 'category_id', $categoriesId[0], 'genre_id');

        $data = $this->sendData + ['categories_id' => [$categoriesId[1], $categoriesId[2]]];
        $this->assertUpdateDatabaseMissing($data, 'category_genre', 'category_id', $categoriesId[0], 'genre_id');
        $this->assertUpdateDatabaseHas($data, 'category_genre', 'category_id', $categoriesId[1], 'genre_id');
        $this->assertUpdateDatabaseHas($data, 'category_genre', 'category_id', $categoriesId[2], 'genre_id');
    }

    protected function assertHasCategory($genreId, $categoryId)
    {
        $this->assertDatabaseHas('category_genre', [
            'genre_id' => $genreId,
            'category_id' => $categoryId
        ]);
    }

    protected function routeStore()
    {
        return route('genres.store');
    }

    protected function routeUpdate()
    {
        return route('genres.update', ['genre' => $this->genre->id]);
    }

    protected function model()
    {
        return Genre::class;
    }
}
