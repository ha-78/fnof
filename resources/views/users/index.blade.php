@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ $user->name }}</h1>
    <p class="mt-2 text-muted">{{ $user->email }}</p>
    <p class="mt-4 inline-flex rounded-full bg-sun px-3 py-1 text-sm text-ink">{{ __('users.role') }}: {{ $user->role->label() }}</p>

    <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit" class="rounded-full bg-pill px-3 py-1 text-sm text-ink hover:bg-pill-active">{{ __('users.log_out') }}</button>
    </form>

    @can('viewAny', App\Models\User::class)
        <h2 class="mt-10 text-lg font-medium">{{ __('users.users') }}</h2>

        <div class="mt-4 overflow-x-auto">
            <table class="w-full border-separate border-spacing-y-2 text-left text-sm">
                <thead>
                    <tr>
                        <th class="bg-lilac px-4 py-3 font-medium text-ink first:rounded-l-2xl last:rounded-r-2xl">{{ __('users.name') }}</th>
                        <th class="bg-lilac px-4 py-3 font-medium text-ink first:rounded-l-2xl last:rounded-r-2xl">{{ __('users.email') }}</th>
                        <th class="bg-lilac px-4 py-3 font-medium text-ink first:rounded-l-2xl last:rounded-r-2xl">{{ __('users.role') }}</th>
                        <th class="bg-lilac px-4 py-3 font-medium text-ink first:rounded-l-2xl last:rounded-r-2xl"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $member)
                        <tr @class(['bg-row' => $loop->odd, 'bg-row-alt' => $loop->even])>
                            <td class="px-4 py-3 first:rounded-l-2xl last:rounded-r-2xl">{{ $member->name }}</td>
                            <td class="px-4 py-3 first:rounded-l-2xl last:rounded-r-2xl">{{ $member->email }}</td>
                            <td class="px-4 py-3 first:rounded-l-2xl last:rounded-r-2xl">{{ $member->role->label() }}</td>
                            <td class="px-4 py-3 text-right first:rounded-l-2xl last:rounded-r-2xl">
                                <a href="{{ route('users.show', $member) }}" class="inline-flex rounded-full bg-chip px-3 py-1 text-ink hover:bg-pill-active">{{ __('users.manage') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">{{ $users->links() }}</div>
    @endcan
@endsection
