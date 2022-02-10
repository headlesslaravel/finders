<?php

namespace HeadlessLaravel\Finders;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class ApplySort
{
    public static function on(Builder $query, array $sorting, array $values = [])
    {
        if (!empty($values)) {
            Request::replace($values);
        }

        $keys = collect($sorting)->implode('key', ',');

        Request::validate([
            'sort'      => ['nullable', "in:$keys"],
            'sort-desc' => ['nullable', "in:$keys"],
        ]);

        foreach ($sorting as $sort) {
            if ($sort->isActive()) {
                $sort->apply($query);
            }
        }
    }
}
