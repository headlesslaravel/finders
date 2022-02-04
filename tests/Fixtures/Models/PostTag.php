<?php

namespace HeadlessLaravel\Finders\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class PostTag extends Pivot
{
    public $guarded = [];
}
