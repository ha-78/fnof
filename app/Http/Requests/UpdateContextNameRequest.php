<?php

namespace App\Http\Requests;

use App\Models\Context;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateContextNameRequest extends FormRequest
{
    public function authorize(): bool
    {
        $context = $this->route('context');

        return $context instanceof Context
            && $this->user()->can('update', $context);
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('name')) {
            return;
        }

        $this->merge([
            'name' => Str::of((string) $this->input('name'))->trim()->toString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
