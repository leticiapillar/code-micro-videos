<?php

namespace Tests\Feature\Models;

use App\Models\Video;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class VideoTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Video::class, 1)->create();

        $videos = Video::all();
        $this->assertCount(1, $videos);

        $videosKeys = array_keys($videos->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id', 'title', 'description', 'year_lauched', 'opened', 'rating', 'duration', 'created_at', 'updated_at', 'deleted_at'
            ],
            $videosKeys
        );
    }

    public function testCreateAttributeId()
    {
        /** @var Video $video */
        $video = factory(Video::class)->create()->first();

        $this->assertNotNull($video->id);
        $this->assertIsString($video->id);
        $this->assertEquals(36, strlen($video->id));
    }

    public function testCreateAttributes()
    {
        $video = Video::create([
            'title' => 'title test',
            'description' => 'description test',
            'year_lauched' => 2010,
            'opened' => true,
            'rating' => 'L',
            'duration' => 90
        ]);
        $video->refresh();

        $this->assertEquals('title test', $video->title);
        $this->assertEquals('description test', $video->description);
        $this->assertEquals(2010, $video->year_lauched);
        $this->assertTrue($video->opened);
        $this->assertEquals('L', $video->rating);
        $this->assertEquals(90, $video->duration);
    }

    public function testUpdate()
    {
        /** @var Video $video */
        $video = factory(Video::class)->create([
            'opened' => false,
        ]);

        $data = [
            'description' => 'description test updated',
            'opened' => true
        ];
        $video->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $video->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Vide $video */
        $video = factory(Video::class)->create()->first();
        $this->assertNull($video->deleted_at);

        $video->delete();
        $this->assertNotNull($video->deleted_at);
        $this->assertNull(Video::find($video->id));

        $video->restore();
        $this->assertNull($video->deleted_at);
        $this->assertNotNull(Video::find($video->id));
    }
}
