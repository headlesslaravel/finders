<?php

namespace HeadlessLaravel\Finders\Tests\Fixtures\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Like extends Pivot
{
    public $table = 'likes';

    public $guarded = [];
}
