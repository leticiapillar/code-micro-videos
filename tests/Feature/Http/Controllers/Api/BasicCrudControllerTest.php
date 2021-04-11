<?php

namespace Tests\Feature\Http\Controllers\Api;

use App\Http\Controllers\Api\BasicCrudController;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\Stubs\Controllers\CategoryControllerStub;
use Tests\Stubs\Models\CategoryStub;
use Tests\TestCase;

class BasicCrudControllerTest extends TestCase
{
    private $controller;

    protected function setUp(): void
    {
        parent::setUp();
        CategoryStub::dropTable();
        CategoryStub::createTable();
        $this->controller = new CategoryControllerStub();
    }

    protected function tearDown(): void
    {
        CategoryStub::dropTable();
        parent::tearDown();
    }


    public function testIndex()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);
        $result = $this->controller->index()[0]->get()->toArray();
        $this->assertEquals(
            [$category->toArray()],
            $result
        );
    }

    public function testInvalidationDataInStore()
    {
        $this->expectException(ValidationException::class);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => '']);
        $this->controller->store($request);
    }

    public function testStore()
    {
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test name', 'description' => 'test description']);

        $obj = $this->controller->store($request);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $obj->get()->toArray()[0]
        );
    }

    public function testIfFindOrFailFetchModel()
    {
        /** @var CategoryStub $category */
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);
        $reflactionClass = new \ReflectionClass(BasicCrudController::class);
        $reflactionMethod = $reflactionClass->getMethod('findOrFail');
        $reflactionMethod->setAccessible(true);

        $result = $reflactionMethod->invokeArgs($this->controller, [$category->id]);
        $this->assertInstanceOf(CategoryStub::class, $result);
    }

    public function testIfFindOrFailThrowExceptionWhenInvalid()
    {
        $this->expectException(ModelNotFoundException::class);

        $reflactionClass = new \ReflectionClass(BasicCrudController::class);
        $reflactionMethod = $reflactionClass->getMethod('findOrFail');
        $reflactionMethod->setAccessible(true);

        $reflactionMethod->invokeArgs($this->controller, [0]);
    }

    public function testShow()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);
        $result = $this->controller->show($category->id);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $result->get()->toArray()[0]
        );
    }

    public function testUpdate()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);
        $request = \Mockery::mock(Request::class);
        $request
            ->shouldReceive('all')
            ->once()
            ->andReturn(['name' => 'test name', 'description' => 'test description']);
        $result = $this->controller->update($request, $category->id);
        $this->assertEquals(
            CategoryStub::find(1)->toArray(),
            $result->get()->toArray()[0]
        );
    }

    public function testDestroy()
    {
        $category = CategoryStub::create(['name' => 'test name', 'description' => 'test description']);
        $response = $this->controller->destroy($category->id);
        $this
            ->createTestResponse($response)
            ->assertStatus(204);
        $this->assertCount(0, CategoryStub::all());
    }
}
