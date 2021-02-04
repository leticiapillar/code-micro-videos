<?php

namespace Tests\Stubs\Controllers;

use App\Http\Controllers\Api\BasicCrudController;
use Tests\Stubs\Models\CategoryStub;

class CategoryControllerStub extends BasicCrudController
{
    protected function model()
    {
        return CategoryStub::class;
    }

    protected function rulesStore()
    {
        // Incluimos até os campos que não são obrigatorio, como o description, 
        // para que o retorno da função validation retorne esse campo para inserir quando o mesmo tiver dados
        return [
            'name' => 'required|max:255',
            'description' => 'nullable'
        ];
    }
}
