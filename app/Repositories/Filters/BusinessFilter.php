<?php

namespace App\Repositories\Filters;

class BusinessFilter extends BaseFilter
{
    protected function verified($value): void
    {
        if ($value === 'true') {
            $this->builder->whereHas('user', function ($query) use ($value) {
                return $query->whereNotNull('email_verified_at');
            });
        } elseif ($value === 'false') {
            $this->builder->whereHas('user', function ($query) use ($value) {
                return $query->whereNull('email_verified_at');
            });
        }
    }

    protected function category($value): void
    {
        $this->builder->when($value, function ($query) use ($value){
            $categories = explode(',', $value);

            $query->whereHas('categories', function ($query) use ($categories) {
                $query->whereIn('work_categories.name', $categories);
            });
        });
    }

    protected function name($value): void
    {
        $this->builder->where('name', 'like', "%$value%");
    }

    protected function email($value): void
    {
        $this->builder->where('email', 'like', "%$value%");

    }

    protected function description($value): void
    {
        $this->builder->where('description', 'like', "%$value%");

    }

    protected function post_code($value): void
    {
        $this->builder->whereHas('bankDetail', function ($query) use ($value) {
            $query->where('post_code', 'like', "%$value%");
        });
    }
}
