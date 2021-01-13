<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes;

    // estes campos são utilizados no POST ao criar uma categoria
    protected $fillable = ['name', 'description', 'is_active'];
    // entende que o campos deletec_at é do tipo data
    protected $dates = ['deleted_at'];
}
