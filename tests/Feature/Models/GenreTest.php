<?php

namespace Tests\Feature\Models;

use App\Models\Genre;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class GenreTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Genre::class, 1)->create();
        
        $genres = Genre::all();
        $this->assertCount(1, $genres);

        $genresKeys = array_keys($genres->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id', 'name', 'is_active', 'created_at', 'updated_at', 'deleted_at'
            ],
            $genresKeys
        );
    }

    public function testCreateAttributeId()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create()->first();

        $this->assertNotNull($genre->id);
        $this->assertIsString($genre->id);
        $this->assertEquals(36, strlen($genre->id));
    }

    public function testCreateAttributeName()
    {
        $genre = Genre::create([
            'name' => 'teste1'
        ]);
        $genre->refresh();

        $this->assertEquals('teste1', $genre->name);
        $this->assertTrue($genre->is_active);
    }

    public function testCreateAttributeNameAndIsActive()
    {
        $genre = Genre::create([
            'name' => 'teste1',
            'is_active' => false
        ]);
        $genre->refresh();

        $this->assertFalse($genre->is_active);

        $genre = Genre::create([
            'name' => 'teste1',
            'is_active' => true
        ]);
        $genre->refresh();

        $this->assertTrue($genre->is_active);
    }

    public function testUpdate()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create([
            'is_active' => false
        ]);

        $data = [
            'name' => 'test name updated',
            'is_active' => true
        ];
        $genre->update($data);

        foreach ($data as $key => $value) {
            $this->assertEquals($value, $genre->{$key});
        }
    }

    public function testDelete()
    {
        /** @var Genre $genre */
        $genre = factory(Genre::class)->create()->first();
        $this->assertNull($genre->deleted_at);

        $genre->delete();
        $this->assertNotNull($genre->deleted_at);
        $this->assertNull(Genre::find($genre->id));

        $genre->restore();
        $this->assertNull($genre->deleted_at);
        $this->assertNotNull(Genre::find($genre->id));
    }

}
