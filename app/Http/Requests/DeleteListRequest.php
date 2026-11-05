<?php

namespace App\Http\Requests;

use App\Models\ItemList;
use Illuminate\Foundation\Http\FormRequest;

class DeleteListRequest extends FormRequest
{
    public function authorize(): bool
    {
        $list = $this->route('list');

        return $list instanceof ItemList && $this->user()->can('delete', $list);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
