<?php

namespace HeadlessLaravel\Finders;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class Sort
{
    public $key;

    public $internal;

    public $relationship;

    public $column;

    public $direction;

    public static function make($key, $internal = null): self
    {
        return (new self())->init($key, $internal);
    }

    public function init($key, $internal = null): self
    {
        $this->key = $key;

        $this->internal = is_null($internal) ? $key : $internal;

        if(Str::contains($this->key, '.')) {
            abort(500, "$this->key needs an alias: Sort::make('alias', 'some.relation')");
        }

        if(Str::contains($this->internal, '.')) {
            $this->relationship = Str::before($this->internal, '.');
            $this->column = Str::after($this->internal, '.');
        } else {
            $this->column = $this->internal;
        }

        $this->direction = Request::filled('sort-desc') ? 'desc' : 'asc';

        return $this;
    }

    public function isActive(): bool
    {
        return Request::input('sort') == $this->key
            || Request::input('sort-desc') == $this->key;
    }

    public function apply($query)
    {
        if (!empty($this->relationship)) {
            $relation = $query->getModel()->{$this->relationship}(); // comments

            $subquery = $relation->getModel() // Comment
                ->select($this->column)  // upvotes
                ->whereColumn(
                    $relation->getQualifiedForeignKeyName(), // comments.post_id
                    $query->getModel()->getQualifiedKeyName() // posts.id
                )->take(1);

            $query->addSelect([
                $this->column => $subquery,  // upvotes
            ]);
        } elseif (method_exists($query->getModel(), $this->column)) {
            $query->withCount($this->column);
            $this->column = $this->column.'_count';
        }

        $query->orderBy($this->column, $this->direction);

        return $query;
    }
}

