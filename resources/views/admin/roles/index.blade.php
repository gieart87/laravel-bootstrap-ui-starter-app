@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('roles.role_management')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/roles') }}">@lang('roles.role_management')</a></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title">@lang('roles.role_list')</h2>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <a href="{{ url('admin/roles/create') }}" class="btn btn-icon icon-left btn-success"><i class="fas fa-plus-circle"></i> @lang('general.btn_create_label')</a>
                    </div>
                    <div class="card-body">
                        @include('admin.shared.flash')
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>@lang('roles.name_label')</th>
                                    <th>@lang('roles.guard_name_label')</th>
                                    <th>@lang('roles.updated_at_label')</th>
                                    <th width="25%">@lang('general.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($roles as $role)
                                        <tr>
                                            <td>{{ $role->name}}</td>
                                            <td>{{ $role->guard_name}}</td>
                                            <td>{{ $role->updated_at_formatted}}</td>
                                            <td>
                                                <a class="btn btn-sm btn-info" href="{{ url('admin/roles/'. $role->id)}}"><i class="far fa-eye"></i> @lang('general.btn_show_label') </a>
                                                <a class="btn btn-sm btn-warning" href="{{ url('admin/roles/'. $role->id .'/edit')}}"><i class="far fa-edit"></i> @lang('general.btn_edit_label') </a>
                                                <a href="{{ url('admin/roles/'. $role->id) }}" class="btn btn-sm btn-danger" onclick="
                                                    event.preventDefault();
                                                    if (confirm('Do you want to remove this?')) {
                                                        document.getElementById('delete-role-{{ $role->id }}').submit();
                                                    }">
                                                    <i class="far fa-trash-alt"></i> @lang('general.btn_delete_label')
                                                </a>
                                                <form id="delete-role-{{ $role->id }}" action="{{ url('admin/roles/'. $role->id) }}" method="POST">
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    @csrf
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $roles->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection