@if(isset($category))
    {!! Form::model($category, ['url' => ['admin/blog/categories', $category->id], 'method' => 'PUT', 'files' => true ]) !!}
    {!! Form::hidden('id') !!}
@else
    {!! Form::open(['url' => 'admin/blog/categories', 'files'=>true]) !!}
@endif
@csrf
<div class="card">
    <div class="card-header">
        <h4>{{ empty($category) ? __('blog::categories.add_card_title') : __('blog::categories.update_card_title') }}</h4>
    </div>
    <div class="card-body">
        @include('admin.shared.flash')
        <div class="form-group">
            {!! Form::label('name', __('blog::categories.name_label')) !!}
            {!! Form::text('name', null, ['class' => 'form-control' . ($errors->has('name') ? ' is-invalid' : '') . ((!$errors->has('name') && old('name')) ? ' is-valid' : ''), 'placeholder' => __('blog::categories.name_label')]) !!}
            @error('name')
                <div class="invalid-feedback">
                {{ $message }}
                </div>
            @enderror
        </div>
        <div class="form-group">
            {!! Form::label('parent_id', __('blog::categories.parent_label')) !!}
            {!! Form::select('parent_id', $nestedCategories, !empty($category->parent_id) ? $category->parent_id : old('category_id'), ['class' => 'form-control', 'placeholder' => '-- Parent Category --']) !!}
            @error('parent_id')
                <div class="invalid-feedback">
                {{ $message }}
                </div>
            @enderror
        </div>
    </div>
    <div class="card-footer text-right">
        <button class="btn btn-primary">{{ empty($category) ? __('blog::categories.btn_create_label') : __('blog::categories.btn_update_label') }}</button>
    </div>
</div>
{!! Form::close() !!}