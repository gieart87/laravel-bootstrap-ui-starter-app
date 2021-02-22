@extends('layouts.dashboard')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('blog::pages.manage_pages')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/blog/pages') }}">@lang('blog::pages.manage_pages')</a></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title">@lang('blog::pages.page_list')</h2>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>@lang('blog::pages.manage_pages')</h4>
                    </div>
                    <div class="card-body">
                        @include('blog::admin.shared.flash')
                        @include('blog::admin.pages._filter')
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>@lang('blog::pages.title_label')</th>
                                    <th>@lang('blog::pages.author_label')</th>
                                    <th>@lang('blog::pages.updated_at_label')</th>
                                    <th width="25%">@lang('blog::pages.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($pages as $page)
                                        <tr>
                                            <td>{{ $page->title }}</td>
                                            <td><a href="{{ url('admin/users/'. $page->user->id) }}">{{ $page->user->name }}</a></td>
                                            <td>{{ $page->updated_at_formatted }}</td>
                                            <td>
                                                @if ($page->trashed())
                                                    @can('delete_blog-pages')
                                                        <a class="btn btn-sm btn-warning" href="{{ url('admin/blog/pages/'. $page->id .'/restore')}}"><i class="fa fa-sync-alt"></i> @lang('blog::pages.btn_restore_label') </a>
                                                        <a href="{{ url('admin/blog/pages/'. $page->id) }}" class="btn btn-sm btn-danger" onclick="
                                                            event.preventDefault();
                                                            if (confirm('Do you want to remove this permanently?')) {
                                                                document.getElementById('delete-role-{{ $page->id }}').submit();
                                                            }">
                                                            <i class="far fa-trash-alt"></i> @lang('blog::pages.btn_delete_permanent_label')
                                                        </a>
                                                        <form id="delete-role-{{ $page->id }}" action="{{ url('admin/blog/pages/'. $page->id) }}" method="POST">
                                                            <input type="hidden" name="_method" value="DELETE">
                                                            <input type="hidden" name="_permanent_delete" value="TRUE">
                                                            @csrf
                                                        </form>
                                                    @endcan
                                                @else
                                                    @can('view_blog-pages')
                                                        <a class="btn btn-sm btn-info" href="{{ url('post/'. $page->id )}}"><i class="far fa-eye"></i> @lang('blog::pages.btn_show_label') </a>
                                                    @endcan
                                                    @can('edit_blog-pages')
                                                        <a class="btn btn-sm btn-success" href="{{ url('admin/blog/pages/'. $page->id .'/edit')}}"><i class="far fa-edit"></i> @lang('blog::pages.btn_edit_label') </a>
                                                    @endcan
                                                    @can('delete_blog-pages')
                                                        <a href="{{ url('admin/blog/pages/'. $page->id) }}" class="btn btn-sm btn-warning" onclick="
                                                            event.preventDefault();
                                                            if (confirm('Do you want to remove this?')) {
                                                                document.getElementById('delete-role-{{ $page->id }}').submit();
                                                            }">
                                                            <i class="far fa-trash-alt"></i> @lang('blog::pages.btn_delete_label')
                                                        </a>
                                                        <form id="delete-role-{{ $page->id }}" action="{{ url('admin/blog/pages/'. $page->id) }}" method="POST">
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
                        {{ $pages->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection