@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold">{{ $user->name }}</h1>
    <p class="mt-1 text-stone-600">{{ $user->email }}</p>
    <p class="mt-4 text-sm">{{ __('users.role') }}: {{ $user->role->label() }}</p>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="text-sm underline-offset-2 hover:underline">{{ __('users.log_out') }}</button>
    </form>

    @can('viewAny', App\Models\User::class)
        <h2 class="mt-10 text-lg font-medium">{{ __('users.users') }}</h2>

        <div class="mt-4 overflow-hidden rounded-lg border border-stone-200 bg-white">
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
                    @foreach ($users as $member)
                        <tr class="border-b border-stone-100 last:border-b-0">
                            <td class="px-4 py-3">{{ $member->name }}</td>
                            <td class="px-4 py-3">{{ $member->email }}</td>
                            <td class="px-4 py-3">{{ $member->role->label() }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('users.show', $member) }}" class="underline-offset-2 hover:underline">{{ __('users.manage') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    @endcan
@endsection
