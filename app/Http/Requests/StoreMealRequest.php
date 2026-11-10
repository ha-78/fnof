<?php

namespace App\Http\Requests;

use App\Models\Meal;
use Illuminate\Foundation\Http\FormRequest;

class StoreMealRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Meal::class);
    }

    protected function prepareForValidation(): void
    {
        $recipeLink = trim((string) $this->input('recipe_link'));
        $comment = trim((string) $this->input('comment'));

        $this->merge([
            'name' => trim((string) $this->input('name')),
            'recipe_link' => $recipeLink === '' ? null : $recipeLink,
            'comment' => $comment === '' ? null : $comment,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'recipe_link' => ['nullable', 'string', 'max:2048', 'url:http,https'],
            'comment' => ['nullable', 'string', 'max:10000'],
        ];
    }
}
