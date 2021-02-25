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
    @if(isset($post))
        {!! Form::model($post, ['url' => ['admin/blog/posts', $post->id], 'method' => 'PUT', 'files' => true ]) !!}
        {!! Form::hidden('id') !!}
    @else
        {!! Form::open(['url' => 'admin/blog/posts', 'files'=>true]) !!}
    @endif
    @csrf
        <div class="section-body">
            <h2 class="section-title">{{ empty($post) ? __('blog::posts.post_add_new') : __('blog::posts.post_update') }}</h2>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header">
                            <h4>{{ empty($post) ? __('blog::posts.add_card_title') : __('blog::posts.update_card_title') }}</h4>
                        </div>
                        <div class="card-body">
                            @include('admin.shared.flash')
                            <div class="form-group">
                                {!! Form::label('title', __('blog::posts.title_label')) !!}
						        {!! Form::text('title', null, ['class' => 'form-control' . ($errors->has('title') ? ' is-invalid' : '') . ((!$errors->has('title') && old('title')) ? ' is-valid' : ''), 'placeholder' => __('blog::posts.title_label')]) !!}
                                @error('title')
                                    <div class="invalid-feedback">
                                    {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('excerpt', __('blog::posts.excerpt_label')) !!}
                                {!! Form::textarea('excerpt', null, ['class' => 'form-control summernote-simple', 'placeholder' => __('blog::posts.excerpt_label')]) !!}
                                @error('excerpt')
                                    <div class="invalid-feedback">
                                    {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <div class="form-group">
                                {!! Form::label('body', __('blog::posts.body_label')) !!}
                                {!! Form::textarea('body', null, ['class' => 'form-control summernote', 'placeholder' => __('blog::posts.body_label')]) !!}
                                @error('body')
                                    <div class="invalid-feedback">
                                    {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>
                    @include('blog::admin.posts._meta_fields', ['metaFields' => $metaFields])
                </div>
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Publish</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        {!! Form::label('publish_date', __('blog::posts.publish_date_label')) !!}
							            {!! Form::text('publish_date', !empty(old('publish_date'))? old('publish_date'): date('Y-m-d H:i:s'), ['class' => 'form-control datetimepicker', 'placeholder' => __('blog::posts.publish_date_label')]) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('status', __('blog::posts.status_label')) !!}
                                        {!! Form::select('status', $statuses, !empty($post->status) ? $post->status : old('status'), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button class="btn btn-primary">{{ __('blog::posts.btn_save_label') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Categories</h4>
                                </div>
                                <div class="card-body">
                                    @include('blog::admin.posts._nested_categories', ['categoryIds' => !empty($categoryIds) ? $categoryIds : []])
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Tags</h4>
                                </div>
                                <div class="card-body">
                                    <div class="form-group">
                                        {!! Form::select('tags[]', $tags, !empty($tagIds) ? $tagIds : old('tags'), ['class' => 'form-control select2-tags', 'multiple' => true]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h4>Featured Image</h4>
                                </div>
                                <div class="card-body">
                                    @if (!empty($post) && $post->featured_image)
                                    <div class="form-group">
                                        <img src="{{ $post->featured_image }}" alt="{{ $post->featured_image_caption }}" class="img-fluid img-thumbnail"/>
                                    </div>
                                    @endif
                                    <div class="form-group">
                                        <input type="file" name="image" class="form-control"/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {!! Form::close() !!}
</section>
@endsection