@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">{{ __('users.users') }}</h1>

    <div class="mt-6 overflow-hidden rounded-lg border border-stone-200 bg-white">
        <table class="w-full text-left text-sm">
            <thead class="border-b border-stone-200 bg-stone-50 text-stone-600">
                <tr>
                    <th class="px-4 py-3 font-medium">{{ __('users.name') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('users.email') }}</th>
                    <th class="px-4 py-3 font-medium">{{ __('users.role') }}</th>
                    <th class="px-4 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr class="border-b border-stone-100 last:border-b-0">
                        <td class="px-4 py-3">{{ $user->name }}</td>
                        <td class="px-4 py-3">{{ $user->email }}</td>
                        <td class="px-4 py-3">{{ $user->role->label() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.show', $user) }}" class="underline-offset-2 hover:underline">{{ __('users.manage') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
