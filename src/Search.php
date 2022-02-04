<?php

namespace HeadlessLaravel\Finders;

class Search
{
    public $internal;

    public static function make($internal = null): self
    {
        return (new self())->init($internal);
    }

    public function init($internal): self
    {
        $this->internal = $internal;

        return $this;
    }
}
