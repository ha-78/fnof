<?php

namespace App\Http\Requests;

use App\Models\ItemList;
use Illuminate\Foundation\Http\FormRequest;

class StoreListRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ItemList::class)
            && (! $this->boolean('is_public') || $this->user()->can('publish', ItemList::class));
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
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
            'is_public' => ['boolean'],
        ];
    }
}
