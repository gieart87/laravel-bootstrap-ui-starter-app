@extends('layouts.dashboard')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('users.user_management')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/users') }}">@lang('users.user_management')</a></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title">@lang('users.user_list')</h2>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>@lang('users.user_management')</h4>
                    </div>
                    <div class="card-body">
                        @include('admin.shared.flash')
                        @include('admin.users._filter')
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>@lang('users.name_label')</th>
                                    <th>@lang('users.email_label')</th>
                                    <th>@lang('users.verified_at_label')</th>
                                    <th>@lang('users.user_role')</th>
                                    <th width="25%">@lang('general.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($users as $user)
                                        <tr>
                                            <td>{{ $user->name}}</td>
                                            <td>{{ $user->email}}</td>
                                            <td>{{ $user->verified_at_formatted}}</td>
                                            <td>{{ $user->roles->implode('name', ', ') }}</td>
                                            <td>
                                                @can('view_users')
                                                    <a class="btn btn-sm btn-info" href="{{ url('admin/users/'. $user->id)}}"><i class="far fa-eye"></i> @lang('general.btn_show_label') </a>
                                                @endcan

                                                @if ($user->show_edit_remove_btn)
                                                    @can('edit_users')
                                                        <a class="btn btn-sm btn-warning" href="{{ url('admin/users/'. $user->id .'/edit')}}"><i class="far fa-edit"></i> @lang('general.btn_edit_label') </a>
                                                    @endcan

                                                    @can('delete_users')
                                                        <a href="{{ url('admin/users/'. $user->id) }}" class="btn btn-sm btn-danger" onclick="
                                                            event.preventDefault();
                                                            if (confirm('Do you want to remove this?')) {
                                                                document.getElementById('delete-role-{{ $user->id }}').submit();
                                                            }">
                                                            <i class="far fa-trash-alt"></i> @lang('general.btn_delete_label')
                                                        </a>
                                                        <form id="delete-role-{{ $user->id }}" action="{{ url('admin/users/'. $user->id) }}" method="POST">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            @csrf
                                                        </form>
                                                    @endcan
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection