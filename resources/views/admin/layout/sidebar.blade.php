@php
    $active = request()->segments()[1];
@endphp
<div class="sidebar sidebar-dark sidebar-main sidebar-expand-lg">

    <!-- Sidebar content -->
    <div class="sidebar-content">

        <!-- Sidebar header -->
        <div class="sidebar-section">
            <div class="sidebar-section-body d-flex justify-content-center">
                <h5 class="sidebar-resize-hide flex-grow-1 my-auto">Menu</h5>

                <div>
                    <button
                        class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-control sidebar-main-resize d-none d-lg-inline-flex"
                        type="button">
                        <i class="ph-arrows-left-right"></i>
                    </button>

                    <button
                        class="btn btn-flat-white btn-icon btn-sm rounded-pill border-transparent sidebar-mobile-main-toggle d-lg-none"
                        type="button">
                        <i class="ph-x"></i>
                    </button>
                </div>
            </div>
        </div>
        <!-- /sidebar header -->

        <!-- Main navigation -->
        <div class="sidebar-section">
            <ul class="nav nav-sidebar" data-nav-type="accordion">
                <li class="nav-item-header">
                    <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">
                        General</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>

                <li class="nav-item">
                    <a class="nav-link {{ $active == 'dashboard' ? 'active' : '' }}" href="/admin/dashboard">
                        <i class="ph-house"></i>
                        <span>
                            Dashboard
                        </span>
                    </a>
                </li>

                @can('status-types-read')
                    <li
                        class="nav-item nav-item-submenu {{ $active == 'status_types' ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a class="nav-link" href="#">
                            <i class="ph-activity"></i>
                            <span>Status Type</span>
                        </a>
                        <ul class="nav-group-sub collapse {{ $active == 'status_types' ? 'show' : '' }}">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.status_types.index') }}">All</a>
                            </li>
                            @can('status-types-create')
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.status_types.create') }}">Create</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @canany(['devices-read', 'device-types-read', 'device-logs-read'])
                    <li class="nav-item-header">
                        <div class="text-uppercase fs-sm lh-sm opacity-50 sidebar-resize-hide">
                            Device</div>
                        <i class="ph-dots-three sidebar-resize-show"></i>
                    </li>
                @endcanany()

                @can('device-types-read')
                    <li
                        class="nav-item nav-item-submenu {{ $active == 'device_types' ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a class="nav-link" href="#">
                            <i class="ph-bookmarks-simple"></i>
                            <span>Device Type</span>
                        </a>
                        <ul class="nav-group-sub collapse {{ $active == 'device_types' ? 'show' : '' }}">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.device_types.index') }}">All</a>
                            </li>
                            @can('device-types-create')
                                <li class="nav-item"><a class="nav-link"
                                        href="{{ route('admin.device_types.create') }}">Create</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('devices-read')
                    <li
                        class="nav-item nav-item-submenu {{ $active == 'devices' ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a class="nav-link" href="#">
                            <i class="ph-atom"></i>
                            <span>Device</span>
                        </a>
                        <ul class="nav-group-sub collapse {{ $active == 'devices' ? 'show' : '' }}">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.devices.index') }}">All</a></li>
                            @can('devices-create')
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.devices.create') }}">Create</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('device-logs-read')
                    <li class="nav-item">
                        <a class="nav-link {{ $active == 'device_logs' ? 'active' : '' }}"
                            href="{{ route('admin.device_logs.index') }}">
                            <i class="ph-notebook"></i>
                            <span>
                                Log and Report
                            </span>
                        </a>
                    </li>
                @endcan

                @can('users-read')
                    <li
                        class="nav-item nav-item-submenu {{ $active == 'users' ? 'nav-item-expanded nav-item-open' : '' }}">
                        <a class="nav-link" href="#">
                            <i class="ph-users"></i>
                            <span>User</span>
                        </a>
                        <ul class="nav-group-sub collapse {{ $active == 'users' ? 'show' : '' }}">
                            <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.index') }}">All</a></li>
                            @can('users-create')
                                <li class="nav-item"><a class="nav-link" href="{{ route('admin.users.create') }}">Create</a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan

                @can('systems-control')
                    <li class="nav-item">
                        <a class="nav-link {{ $active == 'settings' ? 'active' : '' }}"
                            href="{{ route('admin.settings.index') }}">
                            <i class="ph-gear"></i>
                            <span>
                                Setting
                            </span>
                        </a>
                    </li>
                @endcan
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
