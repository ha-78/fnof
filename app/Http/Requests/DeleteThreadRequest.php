<?php

namespace App\Http\Requests;

use App\Models\Thread;
use Illuminate\Foundation\Http\FormRequest;

class DeleteThreadRequest extends FormRequest
{
    public function authorize(): bool
    {
        $thread = $this->route('thread');

        return $thread instanceof Thread && $this->user()->can('delete', $thread);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
