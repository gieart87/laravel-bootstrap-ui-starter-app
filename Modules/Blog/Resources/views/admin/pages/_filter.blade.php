@php
    $route = 'admin/blog/pages';
    if ($viewTrash) {
        $route = 'admin/blog/pages/trashed';
    }
@endphp

{!! Form::open(['url' => $route, 'method' => 'GET']) !!}
    <div class="form-row">
        <div class="form-group col-md-4">
            <input type="text" name="q" class="form-control" id="q" placeholder="Type keywords.." value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            {!! Form::select('status', $statuses, !empty($filter['status']) ? $filter['status'] : old('status'), ['class' => 'form-control', 'placeholder' => '-- Status --']) !!}
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-block btn-primary btn-filter"><i class="fas fa-search"></i> {{ __('general.btn_search_label') }}</button>
        </div>
        <div class="form-group col-md-2">
            @can('add_blog-pages')
                <a href="{{ url('admin/blog/pages/create') }}" class="btn btn-icon btn-block icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('blog::pages.btn_create_label')</a>
            @endcan
        </div>
        <div class="form-group col-md-2">
            @can('delete_blog-pages')
                @if (!$viewTrash)
                    <a href="{{ url('admin/blog/pages/trashed') }}" class="btn btn-icon btn-block icon-left btn-light btn-filter"><i class="fas fa-trash"></i> @lang('blog::pages.btn_show_trashed_label')</a>
                @endif
            @endcan
        </div>
    </div>
{!! Form::close() !!}
