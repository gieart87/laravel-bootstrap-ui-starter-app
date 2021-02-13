<form method="GET" action="{{ url('admin/blog/posts') }}">
    <div class="form-row">
        <div class="form-group col-md-2 offset-md-10">
            <a href="{{ url('admin/blog/posts/create') }}" class="btn btn-block btn-icon icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('general.btn_create_label')</a>
        </div>
    </div>
</form>