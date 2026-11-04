<?php

namespace App\Http\Requests;

use App\Models\Bookmark;
use Illuminate\Foundation\Http\FormRequest;

class DeleteBookmarkRequest extends FormRequest
{
    public function authorize(): bool
    {
        $bookmark = $this->route('bookmark');

        return $bookmark instanceof Bookmark && $this->user()->can('delete', $bookmark);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
