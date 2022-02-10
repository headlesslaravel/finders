<?php

namespace HeadlessLaravel\Finders;

use HeadlessLaravel\Finders\Scopes\SearchScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Request;

class ApplySearch
{
    public static function on(Builder $query, array $searchers, $term = null)
    {
        if (!is_null($term)) {
            Request::replace(['search' => $term]);
        }

        if (!Request::has('search')) {
            return;
        }

        $columns = [];

        foreach ($searchers as $search) {
            $columns[] = $search->internal;
        }

        (new SearchScope())->apply($query, $columns, Request::input('search'));
    }
}
