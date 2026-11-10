<?php

namespace App\Http\Requests;

use App\Models\Bookmark;
use Illuminate\Foundation\Http\FormRequest;

class StoreBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Bookmark::class)
            && (! $this->boolean('is_public') || $this->user()->can('publish', Bookmark::class));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'url' => trim((string) $this->input('url')),
            'is_public' => $this->boolean('is_public'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'string', 'max:2048', 'url:http,https'],
            'is_public' => ['boolean'],
        ];
    }
}
