@extends('layouts.dashboard')
@section('content')
<section class="section">
    <div class="section-header">
        <h1>@lang('blog::posts.manage_posts')</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item"><a href="{{ url('admin/dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ url('admin/blog/posts') }}">@lang('blog::posts.manage_posts')</a></div>
        </div>
    </div>
    <div class="section-body">
        <h2 class="section-title">@lang('blog::posts.post_list')</h2>
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-header">
                        <h4>@lang('blog::posts.manage_posts')</h4>
                    </div>
                    <div class="card-body">
                        @include('blog::admin.shared.flash')
                        @include('blog::admin.posts._filter')
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped table-md">
                                <thead>
                                    <th>@lang('blog::posts.title_label')</th>
                                    <th>@lang('blog::posts.author_label')</th>
                                    <th>@lang('blog::posts.updated_at_label')</th>
                                    <th width="20%">@lang('blog::general.action_label')</th>
                                </thead>
                                <tbody>
                                    @forelse ($posts as $post)
                                        <tr>
                                            <td>{{ $post->title }}</td>
                                            <td><a href="{{ url('admin/users/'. $post->user->id) }}">{{ $post->user->name }}</a></td>
                                            <td>{{ $post->updated_at_formatted }}</td>
                                            <td>
                                                <a class="btn btn-sm btn-warning" href="{{ url('admin/blog/posts/'. $post->id .'/edit')}}"><i class="far fa-edit"></i> @lang('general.btn_edit_label') </a>
                                                <a href="{{ url('admin/blog/posts/'. $post->id) }}" class="btn btn-sm btn-danger" onclick="
                                                    event.preventDefault();
                                                    if (confirm('Do you want to remove this?')) {
                                                        document.getElementById('delete-role-{{ $post->id }}').submit();
                                                    }">
                                                    <i class="far fa-trash-alt"></i> @lang('general.btn_delete_label')
                                                </a>
                                                <form id="delete-role-{{ $post->id }}" action="{{ url('admin/blog/posts/'. $post->id) }}" method="POST">
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
                        {{ $posts->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection