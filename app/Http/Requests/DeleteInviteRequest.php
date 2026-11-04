<?php

namespace App\Http\Requests;

use App\Models\Invite;
use Illuminate\Foundation\Http\FormRequest;

class DeleteInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $invite = $this->route('invite');

        return $invite instanceof Invite
            && $this->user()->can('delete', $invite);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [];
    }
}
