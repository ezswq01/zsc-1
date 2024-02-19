<div class="navbar navbar-expand-lg navbar-static border-bottom border-opacity-10">
    <div class="container-fluid">
        <div class="d-flex d-lg-none me-2">
            <button class="navbar-toggler sidebar-mobile-main-toggle rounded-pill" type="button">
                <i class="ph-list"></i>
            </button>
        </div>

        <div class="navbar-brand flex-1 flex-lg-0 p-0">
            <a class="d-inline-flex align-items-center" href="/">
                <img src="{{ App\Models\Setting::first()?->logo ? Storage::url(App\Models\Setting::first()?->logo) : "/assets/images/logo_icon.svg" }}"
                    alt="logo" style="min-height: 75px">
            </a>
        </div>

        <ul class="nav flex-row justify-content-end order-1 order-lg-2">
            <li class="nav-item">
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill" data-bs-toggle="offcanvas"
                    data-bs-target="#notifications">
                    <i class="ph-bell"></i>
                    <span class="notification-count badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1">
                        0
                    </span>
                </a>
            </li>
            <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-2">
                <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                    <div class="status-indicator-container">
                        <img src="/assets/images/demo/users/face11.jpg" class="w-32px h-32px rounded-pill">
                        <span class="status-indicator bg-success"></span>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">{{ auth()->user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="/admin/change-password">
                        <i class="ph-wrench me-2"></i>
                        Change Password
                    </a>
                    <a class="dropdown-item" href="/admin/logout">
                        <i class="ph-sign-out me-2"></i>
                        Logout
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
