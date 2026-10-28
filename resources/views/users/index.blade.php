@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-semibold tracking-tight">{{ __('users.users') }}</h1>

    <div class="data-table mt-4 overflow-x-auto rounded-3xl">
        <table class="w-full text-left text-sm">
            <thead>
                <tr>
                    <th class="px-4 py-3 font-medium text-ink">{{ __('users.name') }}</th>
                    <th class="px-4 py-3 font-medium text-ink">{{ __('users.email') }}</th>
                    <th class="px-4 py-3 font-medium text-ink">{{ __('users.role') }}</th>
                    <th class="px-4 py-3 font-medium text-ink"></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $member)
                    <tr>
                        <td class="px-4 py-3">{{ $member->name }}</td>
                        <td class="px-4 py-3">{{ $member->email }}</td>
                        <td class="px-4 py-3">{{ $member->role->label() }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('users.show', $member) }}" class="inline-flex rounded-full bg-chip px-3 py-1 text-ink hover:bg-pill-active">{{ __('users.manage') }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-6">{{ $users->links() }}</div>
@endsection
