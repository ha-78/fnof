<?php

namespace App\Http\Requests;

use App\Models\ListItem;
use Illuminate\Foundation\Http\FormRequest;

class UnhandleListItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('item');

        return $item instanceof ListItem && $this->user()->can('unhandle', $item);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
