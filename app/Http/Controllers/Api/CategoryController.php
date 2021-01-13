<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

use function React\Promise\all;

class CategoryController extends Controller
{
    public function index()
    {
        return Category::all();
    }

    public function store(CategoryRequest $request)
    {
        // Validação na controller: Validação dos campos recebidos pela request 
        // $this->validate($request, [
        //     'name' => 'required|max:255',
        //     'is_active' => 'boolean'
        // ]);
        return Category::create($request->all());
    }

    public function show(Category $category) //Route Model Binding
    {
        return $category;
    }

    public function update(CategoryRequest $request, Category $category)
    {
        $category->update($request->all());
        return $category;
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return response()->noContent(); //204 - No Content
    }
}
