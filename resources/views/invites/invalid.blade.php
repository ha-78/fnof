@extends('layouts.front')

@section('content')
    <div class="mx-auto max-w-sm">
        <x-ui.message type="error" :dismissible="false">{{ __('users.invites.invalid') }}</x-ui.message>
    </div>
@endsection
