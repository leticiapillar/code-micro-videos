<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Genre
 *
 * @method static Builder|Genre newModelQuery()
 * @method static Builder|Genre newQuery()
 * @method static \Illuminate\Database\Query\Builder|Genre onlyTrashed()
 * @method static Builder|Genre query()
 * @method static \Illuminate\Database\Query\Builder|Genre withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Genre withoutTrashed()
 * @mixin Eloquent
 * @property string $id
 * @property string $name
 * @property bool $is_active
 * @property Carbon|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Genre whereCreatedAt($value)
 * @method static Builder|Genre whereDeletedAt($value)
 * @method static Builder|Genre whereId($value)
 * @method static Builder|Genre whereIsActive($value)
 * @method static Builder|Genre whereName($value)
 * @method static Builder|Genre whereUpdatedAt($value)
 * @property-read Collection|Category[] $categories
 * @property-read int|null $categories_count
 */
class Genre extends Model
{
    use SoftDeletes, Uuid;

    protected $fillable = ['name', 'is_active'];
    protected $dates = ['deleted_at'];
    protected $casts = [
        'is_active' => 'boolean'
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    public function categories()
    {
        return $this->belongsToMany(Category::class)->withTrashed();
    }
}
