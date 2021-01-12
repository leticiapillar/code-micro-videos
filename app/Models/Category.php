<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // estes campos são utilizados no POST ao criar uma categoria
    protected $fillable = ['name', 'description', 'is_active'];
}
