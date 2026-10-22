<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRoleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $role = Role::tryFrom((string) $this->input('role'));

        if (! $role instanceof Role) {
            return true;
        }

        $subject = $this->route('user');

        return $subject instanceof User
            && $this->user()->can('assignRole', [$subject, $role]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'role' => ['required', Rule::enum(Role::class)],
        ];
    }
}
