@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('roles.role_management')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="{{ url('admin/roles') }}">@lang('roles.role_management')</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/roles/create') }}">@lang('roles.role_add_new')</a></div>
        </div>
    </div>
    @if (empty($role))
        <form method="POST" action="{{ url('admin/roles') }}">
    @else
        <form method="POST" action="{{ route('roles.update', $role->id) }}">
        <input type="hidden" name="id" value="{{ $role->id }}"/>
        @method('PUT')
    @endif
    @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($role) ? __('roles.role_add_new') : __('roles.role_update') }}</h2>
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($role) ? __('roles.add_card_title') : __('roles.update_card_title') }}</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.shared.flash')
                            <div class="form-group">
                                <label>@lang('roles.name_label')</label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror @if (!$errors->has('name') && old('name')) is-valid @endif" value="{{ old('name', !empty($role) ? $role->name : null) }}" {{ $disabled ?? '' }}>
                                @error('name')
                                    <div class="invalid-feedback">
                                    {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button class="btn btn-primary">{{ empty($role) ? __('general.btn_create_label') : __('general.btn_update_label') }}</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ __('roles.set_role_permissions_label') }}</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.roles._permissions', ['disabled' => $disabled ?? ''])
                        </div>
                        <div class="card-footer text-right">
                            @php
                                $roleId = empty($role) ? null : $role->id;
                            @endphp
                            <a href="{{ url('admin/roles/reload-permissions/' . $roleId ) }}" class="btn btn-icon icon-left btn-primary"><i class="fa fa-sync-alt"></i> @lang('roles.btn_reload_permissions_label')</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>
@endsection