<aside id="sidebar-wrapper">
    <div class="sidebar-brand">
        <a href="{{ url('/')}}">{{ config('app.name', 'Laravel') }}</a>
    </div>
    <div class="sidebar-brand sidebar-brand-sm">
        <a href="{{ url('/')}}">LS</a>
    </div>
    <ul class="sidebar-menu">
        <li><a class="nav-link" href="{{ url('admin/dashboard') }}"><i class="fas fa-fire"></i> <span>Dashboard</span></a></li>
        <li class="menu-header">@lang('general.menu_account_label')</li>
        <li><a class="nav-link" href="{{ url('admin/users')}}"><i class="fas fa-user"></i> <span>Users</span></a></li>
        <li><a class="nav-link" href="{{ url('admin/roles')}}"><i class="fas fa-lock"></i> <span>@lang('roles.menu_role_permission_label')</span></a></li>
    </ul>
    <div class="mt-4 mb-4 p-3 hide-sidebar-mini">
        <a href="https://getstisla.com/docs" class="btn btn-primary btn-lg btn-block btn-icon-split">
            <i class="fas fa-rocket"></i> Documentation
        </a>
    </div>
</aside>
