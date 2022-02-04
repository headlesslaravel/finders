<?php

namespace HeadlessLaravel\Finders\Traits;

use HeadlessLaravel\Finders\ApplySearch;

trait Searchable
{
    public function scopeSearch($query, array $searchers, string $term = null)
    {
        ApplySearch::on($query, $searchers, $term);
    }
}
