@php
    $route = 'admin/blog/categories';
@endphp

{!! Form::open(['url' => $route, 'method' => 'GET']) !!}
    <div class="form-row">
        <div class="form-group col-md-8">
            <input type="text" name="q" class="form-control" id="q" placeholder="Type keyword ..." value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-block btn-primary btn-filter"><i class="fas fa-search"></i> {{ __('general.btn_search_label') }}</button>
        </div>
        <div class="form-group col-md-2">
            @can('add_blog-categories')
                <a href="{{ url('admin/blog/categories/create') }}" class="btn btn-icon btn-block icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('blog::categories.btn_create_label')</a>
            @endcan
        </div>
    </div>
{!! Form::close() !!}
