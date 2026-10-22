<?php

namespace App\Http\Requests;

use App\Models\Reply;
use Illuminate\Foundation\Http\FormRequest;

class DeleteReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        $reply = $this->route('reply');

        return $reply instanceof Reply && $this->user()->can('delete', $reply);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
