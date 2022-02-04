<?php

namespace HeadlessLaravel\Finders\Traits;

use HeadlessLaravel\Finders\ApplySort;

trait Sortable
{
    public function scopeSort($query, array $sortables, array $values = [])
    {
        ApplySort::on($query, $sortables, $values);
    }
}
