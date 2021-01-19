<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Traits\Uuid;
use PHPUnit\Framework\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryTest extends TestCase
{

    public function testIfUseTraits()
    {
        // Verifica se a classe tem as respectivas traits
        $traits = [
            SoftDeletes::class, Uuid::class
        ];
        $categoryTraits = array_keys(class_uses(Category::class));
        $this->assertEquals($traits, $categoryTraits);
    }

    public function testFillableAttribute()
    {
        // Verifica se a classe tem os respectivos campos fillable
        $fillable = ['name', 'description', 'is_active'];
        $category = new Category();
        $this->assertEquals($fillable, $category->getFillable());
    }

    public function testKeyTypeAttribute()
    {
        // Verifica se o campo key é do tipo string
        $category = new Category();
        $this->assertEquals('string', $category->getKeyType());
    }

    public function testIncrementingAttribute()
    {
        // Verifica se o incrementing esa false, pq o id é gerado pelo uuid
        $category = new Category();
        $this->assertFalse($category->incrementing);
    }

    public function testDatesAttribute()
    {
        // Verifica se a classe tem os campos datas
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        $category = new Category();
        foreach ($dates as $date) {
            $this->assertContains($date, $category->getDates());
        }
        $this->assertCount(count($dates), $category->getDates());
    }

}
