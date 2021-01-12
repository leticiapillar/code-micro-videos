<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;

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

    public function update(Request $request, Category $category)
    {
        //
    }

    public function destroy(Category $category)
    {
        //
    }
}
