<?php

namespace Tests\Feature\Models;

use App\Models\Category;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use DatabaseMigrations;

    public function testList()
    {
        factory(Category::class, 1)->create();

        $categories = Category::all();
        $this->assertCount(1, $categories);

        $categoryKeys = array_keys($categories->first()->getAttributes());
        $this->assertEqualsCanonicalizing(
            [
                'id', 'name', 'description', 'is_active', 'created_at', 'updated_at', 'deleted_at'
            ],
            $categoryKeys
        );
    }

    public function testCreateAttributeName() 
    {
        $category = Category::create([
            'name' => 'teste1'
        ]);
        $category->refresh();

        $this->assertEquals('teste1', $category->name);
        $this->assertNull($category->description);
        $this->assertTrue($category->is_active);
    }

    public function testCreateAttributeNameAndDescription() 
    {
        $category = Category::create([
            'name' => 'teste1',
            'description' => null
        ]);
        $category->refresh();

        $this->assertNull($category->description);

        $category = Category::create([
            'name' => 'teste1',
            'description' => 'teste description'
        ]);
        $category->refresh();

        $this->assertEquals('teste description', $category->description);

    }

    public function testCreateAttributeNameAndIsActive()
    {
        $category = Category::create([
            'name' => 'teste1',
            'is_active' => false
        ]);
        $category->refresh();

        $this->assertFalse($category->is_active);

        $category = Category::create([
            'name' => 'teste1',
            'is_active' => true
        ]);
        $category->refresh();

        $this->assertTrue($category->is_active);
    }

}
