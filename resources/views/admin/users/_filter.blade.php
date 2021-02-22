<form method="GET" action="{{ route('users.index') }}">
    <div class="form-row">
        <div class="form-group col-md-4">
        <input type="text" name="q" class="form-control" id="q" placeholder="Type name or email.." value="{{ !empty($filter['q']) ? $filter['q'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="start_date" class="form-control datepicker" id="start_date" placeholder="Start date" value="{{ !empty($filter['start_date']) ? $filter['start_date'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <input type="text" name="end_date" class="form-control datepicker" id="end_date" placeholder="End date" value="{{ !empty($filter['end_date']) ? $filter['end_date'] : '' }}">
        </div>
        <div class="form-group col-md-2">
            <button class="btn btn-block btn-primary btn-filter"><i class="fas fa-search"></i> {{ __('general.btn_search_label') }}</button>
        </div>
        <div class="form-group col-md-2">
            @can('add_users')
                <a href="{{ url('admin/users/create') }}" class="btn btn-block btn-icon icon-left btn-success btn-filter"><i class="fas fa-plus-circle"></i> @lang('general.btn_create_label')</a>
            @endcan
        </div>
    </div>
</form>