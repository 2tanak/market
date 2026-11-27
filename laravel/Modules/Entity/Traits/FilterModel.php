<?php

namespace Modules\Entity\Traits;

use Illuminate\Http\Request;

trait FilterModel
{
    protected function filterClass()
    {
        return false;
    }

    public function scopeFilter($query, Request $request)
    {
        $filter_class = $this->filterClass() ?: ($this->filter_class ?? false);

        if (! $filter_class) {
            return $query;
        }

        $filter = new $filter_class($query, $request);
        $filter->filter();

        return $filter->getQuery();
    }
}
