<?php

namespace App\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CastMember
 *
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember newQuery()
 * @method static \Illuminate\Database\Query\Builder|CastMember onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CastMember query()
 * @method static \Illuminate\Database\Query\Builder|CastMember withTrashed()
 * @method static \Illuminate\Database\Query\Builder|CastMember withoutTrashed()
 * @mixin \Eloquent
 */
class CastMember extends Model
{
    use SoftDeletes, Uuid;

    const TYPE_DIRECTOR = 1;
    const TYPE_ACTOR = 2;
    protected $fillable = ['name', 'type'];
    protected $dates = ['deleted_at'];

    public $incrementing = false;
    protected $keyType = 'string';

    public static function typesCastMembers()
    {
        return [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR];
    }
}
