<?php

namespace HeadlessLaravel\Finders\Traits;

use HeadlessLaravel\Finders\ApplyFilters;

trait Filterable
{
    public function scopeFilters($query, array $filters, array $values = [])
    {
        ApplyFilters::on($query, $filters, $values);
    }
}
