<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use SoftDeletes, Uuid;

    // estes campos são utilizados no POST ao criar uma categoria
    protected $fillable = ['name', 'description', 'is_active'];
    // entende que o campos deletec_at é do tipo data
    protected $dates = ['deleted_at'];

    // servem par informar que não é auto-incremento no banco e que a chave primária é string
    public $incrementing = false;
    protected $keyType = 'string';

    // este passou para um trait para ser reutilizado
    // // evento creating, sobrescreve o evento boot para setar o uuid ao criar uma categoria
    // public static function boot()
    // {
    //     parent::boot();
    //     static::creating(function($obj){
    //         $obj->id = Uuid::uuid4();
    //     });
    // }

}
