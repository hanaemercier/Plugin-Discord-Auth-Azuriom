@extends('layouts.app')

@section('title', trans('auth.register'))

@section('content')
    <div class="container content">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ trans('discord-auth::messages.register-username') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('discord-auth.register-username') }}" id="captcha-form">
                            @csrf

                            <div class="form-group row mb-3">
                                <label for="name" class="col-md-4 col-form-label text-md-right">{{ trans('auth.name') }}</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>

                            @if($conditions !== null)
                                <div class="form-group row">
                                    <div class="col-md-6 offset-md-4">
                                        <div class="custom-control custom-checkbox">
                                            <input class="custom-control-input @error('conditions') is-invalid @enderror" type="checkbox" name="conditions" id="conditions" {{ old('conditions') ? 'checked' : '' }}>

                                            <label class="custom-control-label" for="conditions">
                                                @lang('auth.conditions', ['url' => $conditions])
                                            </label>

                                            @error('conditions')
                                            <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            @endif

                            @include('elements.captcha', ['center' => true])

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ trans('auth.register') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
