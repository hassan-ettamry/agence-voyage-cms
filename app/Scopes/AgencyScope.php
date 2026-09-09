<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use App\Support\AgencyContext;

class AgencyScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (AgencyContext::has()) {
            $builder->where('agency_id', AgencyContext::get());
        }
    }
}