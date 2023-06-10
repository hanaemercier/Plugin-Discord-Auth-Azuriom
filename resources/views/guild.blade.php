@extends('layouts.app')

@section('title', trans('auth.register'))

@section('content')
    <div class="container content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ trans('discord-auth::messages.guild_info') }}</div>

                    <div class="card-body">
                        <p>{{ trans('discord-auth::messages.guild_must_join') }}</p>
                        <a href="{{ setting('discord-auth.guild_invite', '') }}" target="_blank" class="btn btn-primary">{{ trans('discord-auth::messages.guild_invite') }}</a>
                        <a href="{{ route('discord-auth.login') }}" class="btn btn-primary">{{ trans('discord-auth::messages.guild_verify_join') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        window.onload = function() {
            window.open(setting('discord-auth.guild_invite', ''), '_blank');
        };
    </script>
@endsection
