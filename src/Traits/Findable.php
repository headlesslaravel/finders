<?php

namespace HeadlessLaravel\Finders\Traits;

use Illuminate\Support\Facades\Request;

trait Findable
{
    use Searchable;
    use Sortable;
    use Filterable;

    public function getPerPage()
    {
        return Request::input('per_page', 15);
    }
}
