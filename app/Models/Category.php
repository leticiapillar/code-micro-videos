<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\Category
 *
 * @method static \Illuminate\Database\Eloquent\Builder|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Category newQuery()
 * @method static \Illuminate\Database\Query\Builder|Category onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Category query()
 * @method static \Illuminate\Database\Query\Builder|Category withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Category withoutTrashed()
 * @mixin \Eloquent
 * @property string $id
 * @property string $name
 * @property string|null $description
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Category whereUpdatedAt($value)
 */
class Category extends Model
{
    use SoftDeletes, Uuid;

    // estes campos são utilizados no POST ao criar uma categoria
    protected $fillable = ['name', 'description', 'is_active'];
    // entende que o campos deletec_at é do tipo data
    protected $dates = ['deleted_at'];
    protected $casts =[
        'is_active' => 'boolean'
    ];

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
