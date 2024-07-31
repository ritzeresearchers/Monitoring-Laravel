<?php

namespace App\Repositories\Filters;

class JobFilter extends BaseFilter
{
    protected function status($value): void
    {
        $this->builder->where('status', 'like', "%$value%");
    }

    protected function category($value): void
    {
        $this->builder->whereHas('category', function ($query) use ($value) {
            $query->where('name', 'like', "%$value%");
        });
    }

    protected function title($value): void
    {
        $this->builder->where('title', 'like', "%$value%");
    }

    protected function businessName($value): void
    {
        $this->builder->whereHas('hiredBusiness', function ($query) use ($value) {
            $query->where('name', 'like', "%$value%");
        });
    }
}
