<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Str;

class NoImages implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value) || $value === '') {
            return;
        }

        if (Str::isMatch('/<img\b|!\[[^\]]*\]\(|data:image\//i', $value)) {
            $fail(__('threads.images_not_allowed'));
        }
    }
}
