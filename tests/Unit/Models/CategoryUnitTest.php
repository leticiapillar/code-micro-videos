<?php

namespace Tests\Unit\Models;

use App\Models\Category;
use App\Models\Traits\Uuid;
use Tests\TestCase;
use Illuminate\Database\Eloquent\SoftDeletes;

class CategoryUnitTest extends TestCase
{

    private $category;

    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category();
    }

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
        $this->assertEquals($fillable, $this->category->getFillable());
    }

    public function testCastsAttributes()
    {
        $casts = ['is_active' => 'boolean'];
        $this->assertEquals($casts, $this->category->getCasts());
    }

    public function testKeyTypeAttribute()
    {
        // Verifica se o campo key é do tipo string
        $this->assertEquals('string', $this->category->getKeyType());
    }

    public function testIncrementingAttribute()
    {
        // Verifica se o incrementing esa false, pq o id é gerado pelo uuid
        $this->assertFalse($this->category->incrementing);
    }

    public function testDatesAttribute()
    {
        // Verifica se a classe tem os campos datas
        $dates = ['deleted_at', 'created_at', 'updated_at'];
        foreach ($dates as $date) {
            $this->assertContains($date, $this->category->getDates());
        }
        $this->assertCount(count($dates), $this->category->getDates());
    }
}
