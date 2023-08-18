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
                        Main</div>
                    <i class="ph-dots-three sidebar-resize-show"></i>
                </li>
                <li
                    class="nav-item nav-item-submenu {{ $active == 'devices' ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a class="nav-link" href="#">
                        <i class="ph-address-book"></i>
                        <span>Device</span>
                    </a>
                    <ul class="nav-group-sub collapse {{ $active == 'devices' ? 'show' : '' }}">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.devices.index') }}">All</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.devices.create') }}">Create</a>
                        </li>
                    </ul>
                </li>
                <li
                    class="nav-item nav-item-submenu {{ $active == 'device_types' ? 'nav-item-expanded nav-item-open' : '' }}">
                    <a class="nav-link" href="#">
                        <i class="ph-address-book"></i>
                        <span>Device Type</span>
                    </a>
                    <ul class="nav-group-sub collapse {{ $active == 'device_types' ? 'show' : '' }}">
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.device_types.index') }}">All</a></li>
                        <li class="nav-item"><a class="nav-link" href="{{ route('admin.device_types.create') }}">Create</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- /main navigation -->

    </div>
    <!-- /sidebar content -->

</div>
