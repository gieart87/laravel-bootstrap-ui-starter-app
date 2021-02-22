@extends('layouts.dashboard')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('blog::categories.manage_categories')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/blog/categories') }}">@lang('blog::categories.manage_categories')</a></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title">@lang('blog::categories.category_list')</h2>
        <div class="row">
            <div class="col-lg-4">
                @include('blog::admin.categories.form')
            </div>
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4>@lang('blog::categories.manage_categories')</h4>
                    </div>
                    <div class="card-body">
                        @include('blog::admin.categories._filter')
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>@lang('blog::categories.name_label')</th>
                                    <th>@lang('blog::categories.parent_label')</th>
                                    <th>@lang('blog::categories.updated_at_label')</th>
                                    <th width="25%">@lang('blog::categories.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($categories as $category)
                                        <tr>
                                            <td>{{ $category->name }}</td>
                                            <td>{{ $category->parent ? $category->parent->name : '' }}</td>
                                            <td>{{ $category->updated_at_formatted }}</td>
                                            <td>
                                                @can('edit_blog-categories')
                                                    <a class="btn btn-sm btn-success" href="{{ url('admin/blog/categories/'. $category->id .'/edit')}}"><i class="far fa-edit"></i> @lang('blog::categories.btn_edit_label') </a>
                                                @endcan
                                                @can('delete_blog-categories')
                                                    <a href="{{ url('admin/blog/categories/'. $category->id) }}" class="btn btn-sm btn-danger" onclick="
                                                        event.preventDefault();
                                                        if (confirm('Do you want to remove this?')) {
                                                            document.getElementById('delete-role-{{ $category->id }}').submit();
                                                        }">
                                                        <i class="far fa-trash-alt"></i> @lang('blog::categories.btn_delete_label')
                                                    </a>
                                                    <form id="delete-role-{{ $category->id }}" action="{{ url('admin/blog/categories/'. $category->id) }}" method="post">
                                                        <input type="hidden" name="_method" value="DELETE">
                                                        @csrf
                                                    </form>
                                                @endcan
                                            </td>
                                        </tr>
                                    @empty
                                        
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        {{ $categories->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection