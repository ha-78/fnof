<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class CreateGodUser extends Command
{
    protected $signature = 'users:create-god
        {--name= : The user\'s name}
        {--email= : The user\'s email}
        {--password= : The user\'s password}';

    protected $description = 'Create a God user';

    public function handle(): int
    {
        $name = Str::squish((string) ($this->option('name') ?: $this->ask(__('users.name'))));
        $email = Str::lower(trim((string) ($this->option('email') ?: $this->ask(__('users.email')))));
        $password = $this->password();

        if ($password === null) {
            return self::FAILURE;
        }

        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', Rule::unique(User::class)],
                'password' => ['required', 'string', Password::default()],
            ],
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        $user = new User([
            'name' => $name,
            'email' => $email,
            'password' => $password,
        ]);
        $user->role = Role::God;
        $user->save();

        $this->info(__('users.god_created', ['email' => $user->email]));

        return self::SUCCESS;
    }

    private function password(): ?string
    {
        $password = $this->option('password');

        if (is_string($password) && $password !== '') {
            return $this->normalizePassword($password);
        }

        $password = $this->normalizePassword((string) $this->secret(__('users.password')));
        $confirmation = $this->normalizePassword((string) $this->secret(__('users.password_confirmation')));

        if ($password !== $confirmation) {
            $this->error(__('users.password_mismatch'));

            return null;
        }

        return $password;
    }

    private function normalizePassword(string $password): string
    {
        $password = str_replace(["\e[200~", "\e[201~"], '', $password);

        return trim($password);
    }
}
