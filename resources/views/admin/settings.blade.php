@extends('admin.layouts.admin')

@section('title', '')

<!DOCTYPE html>
<title>{{ trans('discord-auth::admin.settings.title') }} | {{ site_name() }}</title>

@section('content')
<div class="row">
    <div class="my-3">
        <div class="card shadow mb-0">
            <div class="card-header">
                <h3 class="mb-0">{{ trans('discord-auth::admin.settings.header') }}</h3>
            </div>
        </div>
        <div class="my-3">
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="card-header">
                        <h4 class="mb-0">{{ trans('discord-auth::admin.settings.subtitle') }}</h4>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('discord-auth.admin.settings') }}" method="POST">
                            @csrf
                            {{ trans('discord-auth::admin.settings.discord-portal') }} :
                            <a class="m-0 font-weight-bold text-primary" target="_blank" rel="noopener noreferrer" href="https://discord.com/developers/applications">
                                https://discord.com/developers/applications
                            </a>
                            <div class="row my-3">
                                <div class="col-md-6 form-group">
                                    <label for="client_id">{{ trans('discord-auth::admin.settings.client_id') }}</label>
                                    <input class="form-control" id="host" placeholder="client_id" name="client_id" value="{{ $client_id }}" required="required">
                                </div>
                                <div class="col-md-6 form-group">
                                    <label for="client_secret">{{ trans('discord-auth::admin.settings.client_secret') }}</label>
                                    <input class="form-control" placeholder="client_secret" type="password" id="client_secret" name="client_secret" value="{{ $client_secret }}" required="required">
                                </div>
                            </div>
                            <div class="my-3 form-group">
                                <label for="guild">{{ trans('discord-auth::admin.settings.guild') }}</label>
                                <input class="form-control" id="guild" name="guild" value="{{ $guild }}" placeholder="guild">
                            </div>
                            <div class="my-3">
                                {{ trans('discord-auth::admin.settings.info') }}
                                <a class="m-0 font-weight-bold text-primary" target="_blank" rel="noopener noreferrer">
                                    {{ url('/discord-auth/callback') }}
                                </a>  
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> {{ trans('messages.actions.save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
