<form method="GET" action="{{ route('users.index') }}">
    <div class="form-row">
        <div class="form-group col-md-2 offset-md-10">
            @can('add_roles')
                <a href="{{ url('admin/roles/create') }}" class="btn btn-block btn-icon icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('general.btn_create_label')</a>
            @endcan
        </div>
    </div>
</form>