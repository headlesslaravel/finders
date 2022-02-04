<?php

namespace HeadlessLaravel\Finders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class ApplyFilters
{
    public static function on(Builder $query, array $filters, array $values = [])
    {
        if(! empty($values)) {
            Request::replace($values);
        }

        foreach($filters as $filter) {
            $filter->setRequest(Request::instance());

            if($filter->isActive()) {
                $filter->validate();
                $filter->apply($query);
            }
        }
    }
}
