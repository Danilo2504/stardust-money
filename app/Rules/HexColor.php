<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class HexColor implements ValidationRule
{
    public function __construct(
        protected bool $allowShort = true,   // #fff
        protected bool $allowAlpha = false,  // #ffffffff
        protected bool $requireHash = true   // obliga #
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! is_string($value)) {
            $fail(__('The :attribute must be a string.'));
            return;
        }

        $pattern = $this->buildPattern();

        if (! preg_match($pattern, $value)) {
            $fail(__('The :attribute must be a valid hex color.'));
        }
    }

    protected function buildPattern(): string
    {
        $hash = $this->requireHash ? '#' : '#?';

        $lengths = [];

        if ($this->allowAlpha) {
            $lengths[] = '8';
        }

        $lengths[] = '6';

        if ($this->allowShort) {
            $lengths[] = '3';
        }

        $lengthPattern = implode('|', array_map(fn ($l) => "[A-Fa-f0-9]{{$l}}", $lengths));

        return "/^{$hash}({$lengthPattern})$/";
    }
}