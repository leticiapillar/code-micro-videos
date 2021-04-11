<?php

namespace App\Http\Controllers\Api;

use App\Models\CastMember;

class CastMemberController extends BasicCrudController
{
    private $rules;

    public function __construct()
    {
        $this->rules = [
            'name' => 'required|max:255',
            // 'type' => 'required|in:' . implode(',', [CastMember::TYPE_DIRECTOR, CastMember::TYPE_ACTOR])
            'type' => 'required|in:' . implode(',', CastMember::typesCastMembers())
        ];
    }

    public function model()
    {
        return CastMember::class;
    }

    public function rulesStore()
    {
        return $this->rules;
    }
    public function rulesUpdate()
    {
        return $this->rules;
    }

    protected function resourceCollection()
    {
        // TODO: Implement resourceCollection() method.
    }

    protected function resource()
    {
        // TODO: Implement resource() method.
    }
}
