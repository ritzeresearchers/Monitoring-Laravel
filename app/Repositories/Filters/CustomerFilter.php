<?php

namespace App\Repositories\Filters;

class CustomerFilter extends BaseFilter
{
    protected function verified($value): void
    {
        if ($value === 'true') {
            $this->builder->whereNotNull('email_verified_at');
        } elseif ($value === 'false') {
            $this->builder->whereNull('email_verified_at');
        }
    }

    protected function name($value): void
    {
        $this->builder->where('name', 'like', "%$value%")
            ->orWhere('first_name', 'like', "%$value%")
            ->orWhere('last_name', 'like', "%$value%")
        ;
    }

    protected function email($value): void
    {
        $this->builder->where('email', 'like', "%$value%");
    }
}
