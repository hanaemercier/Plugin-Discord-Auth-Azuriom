@extends('layouts.app')

@section('title', trans('auth.register'))

@section('content')
    <div class="container content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ trans('auth.register') }}</div>

                    <div class="card-body">
                        <p>{{ trans('discord-auth::messages.guild_must_join') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
