<?php

namespace App\Http\Requests;

use App\Models\Thread;
use App\Rules\NoImages;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class UpdateThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');

        return $thread instanceof Thread && $this->user()->can('update', $thread);
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('title')) {
            return;
        }

        $title = Str::of((string) $this->input('title'))->trim()->toString();

        $this->merge([
            'title' => $title === '' ? null : $title,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255', new NoImages],
        ];
    }
}
