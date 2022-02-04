<?php

namespace HeadlessLaravel\Finders\Tests\Fixtures\Models;

use HeadlessLaravel\Finders\Tests\Fixtures\Database\Factories\TagFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $guarded = [];

    public static function factory()
    {
        return TagFactory::new();
    }

    public function posts()
    {
        return $this->belongsToMany(Post::class);
    }
}
