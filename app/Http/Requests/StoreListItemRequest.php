<?php

namespace App\Http\Requests;

use App\Models\ItemList;
use App\Models\ListItem;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', ListItem::class)
            && (! $this->boolean('item_is_public') || $this->user()->can('publish', ListItem::class));
    }

    protected function prepareForValidation(): void
    {
        $estimate = $this->input('item_estimate');
        $description = trim((string) $this->input('item_description'));

        $this->merge([
            'item_name' => trim((string) $this->input('item_name')),
            'item_description' => $description === '' ? null : $description,
            'item_estimate' => $estimate === null || $estimate === '' ? null : $estimate,
            'item_is_public' => $this->boolean('item_is_public'),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'list_id' => ['required', 'integer'],
            'item_name' => ['required', 'string', 'max:255'],
            'item_description' => ['nullable', 'string', 'max:10000'],
            'item_estimate' => ['nullable', 'integer'],
            'item_is_public' => ['boolean'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'list_id' => __('lists.list'),
            'item_name' => __('lists.name'),
            'item_description' => __('lists.description'),
            'item_estimate' => __('lists.estimate'),
        ];
    }

    /**
     * @return array<int, callable(Validator): void>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->has('list_id')) {
                    return;
                }

                $visible = ItemList::query()
                    ->visibleTo($this->user())
                    ->whereKey($this->integer('list_id'))
                    ->exists();

                if (! $visible) {
                    $validator->errors()->add('list_id', __('validation.exists', ['attribute' => __('lists.list')]));
                }
            },
        ];
    }

    public function list(): ItemList
    {
        return ItemList::query()->findOrFail($this->validated('list_id'));
    }

    public function estimate(): ?int
    {
        $estimate = $this->validated('item_estimate');

        return $estimate === null ? null : (int) $estimate;
    }
}
