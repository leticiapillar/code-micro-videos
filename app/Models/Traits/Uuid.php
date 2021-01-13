<?php

namespace App\Models\Traits;
use \Ramsey\Uuid\Uuid as RamseyUuid;

trait Uuid
{
    // evento creating, sobrescreve o evento boot para setar o uuid ao criar uma categoria
    public static function boot()
    {
        parent::boot();
        static::creating(function ($obj) {
            $obj->id = RamseyUuid::uuid4()->toString();
        });
    }

}

