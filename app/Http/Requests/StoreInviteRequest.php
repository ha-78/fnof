<?php

namespace App\Http\Requests;

use App\Enums\Role;
use App\Models\Context;
use App\Models\Invite;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class StoreInviteRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        $context = $this->context();

        return $user instanceof User
            && $context instanceof Context
            && $user->can('create', [Invite::class, $context]);
    }

    public function context(): ?Context
    {
        return Context::query()->orderBy('id')->first();
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => Str::of((string) $this->input('name'))->squish()->toString(),
            'email' => Str::of((string) $this->input('email'))->trim()->lower()->toString(),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique(User::class, 'email'),
                Rule::unique(Invite::class, 'email')->where(
                    fn ($query) => $query->where('expires_at', '>', now()),
                ),
            ],
            'role' => ['required', Rule::enum(Role::class)],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'email.unique' => __('users.invites.email_taken'),
        ];
    }
}
