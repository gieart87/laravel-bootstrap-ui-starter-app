@extends('layouts.dashboard')

@section('content')
    <section class="section">
        <div class="section-header">
            <h1>@lang('users.user_management')</h1>
            <div class="section-header-breadcrumb">
                <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
                <div class="breadcrumb-item"><a href="{{ url('admin/roles') }}">@lang('users.user_management')</a></div>
                <div class="breadcrumb-item active"><a
                        href="{{ url('admin/roles/create') }}">@lang('users.user_add_new')</a></div>
            </div>
        </div>
        @if (empty($user))
            <form method="POST" action="{{ route('users.store') }}">
            @else
                <form method="POST" action="{{ route('users.update', $user->id) }}">
                    <input type="hidden" name="id" value="{{ $user->id }}" />
                    @method('PUT')
        @endif
        @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($user) ? __('users.user_add_new') : __('users.user_update') }}</h2>
            <div class="row">
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($user) ? __('users.add_card_title') : __('users.update_card_title') }}</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.shared.flash')
                            <div class="form-group">
                                <label>@lang('users.name_label')</label>
                                <input type="text" name="name"
                                    class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif"
                                value="{{ old('name', !empty($user) ? $user->name : null) }}">
                                @error('name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>@lang('users.email_label')</label>
                                <input type="text" name="email"
                                    class="form-control @error('email') is-invalid @enderror @if (!$errors->has('email') && old('email')) is-valid @endif"
                                value="{{ old('email', !empty($user) ? $user->email : null) }}">
                                @error('email')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>@lang('users.password_label')</label>
                                <input type="password" name="password"
                                    class="form-control @error('password') is-invalid @enderror @if (!$errors->has('password') && old('password')) is-valid @endif">
                                @error('password')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>@lang('users.password_confirmation_label')</label>
                                <input type="password" name="password_confirmation" class="form-control @error('password_confirmation') is-invalid @enderror @if (!$errors->has('password_confirmation') &&
                                old('password_confirmation')) is-valid @endif">
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>@lang('users.role_label')</label>
                                <select class="form-control" name="role_id">
                                    <option>@lang('users.select_role_label')</option>

                                    @foreach ($roles as $key => $value)
                                        <option value="{{ $key }}" {{ $key == $roleId ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('password_confirmation')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                        </div>
                        <div class="card-footer text-right">
                            <button
                                class="btn btn-primary">{{ empty($user) ? __('general.btn_create_label') : __('general.btn_update_label') }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('users.set_user_permissions_label') }}</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.roles._permissions')
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
    </section>
@endsection
