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
                        @include('admin.shared.flash')
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection