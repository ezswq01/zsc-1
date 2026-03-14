<div class="navbar navbar-expand-lg navbar-static">
    <div class="container-fluid">

        {{-- Mobile sidebar toggle --}}
        <div class="d-flex d-lg-none me-2">
            <button class="navbar-toggler sidebar-mobile-main-toggle rounded-pill" type="button">
                <i class="ph-list"></i>
            </button>
        </div>

        {{-- Brand / Logo --}}
        <div class="navbar-brand flex-1 flex-lg-0 p-0">
            <a class="d-inline-flex align-items-center" href="/">
                {{--
                    Logo inversion in dark mode:
                    - class="zsc-logo" → CSS applies filter:invert(1) brightness(1.8) in dark theme
                    - Works best for logos that are dark/black on transparent background
                    - If the logo is already colourful, consider providing a separate dark-mode asset
                --}}
                <img src="{{ App\Models\Setting::first()->logo ? Storage::url(App\Models\Setting::first()->logo) : '/assets/images/logo_icon.svg' }}"
                    alt="logo" class="zsc-logo" style="min-height: 75px">
            </a>
        </div>

        {{-- Right-side nav items --}}
        <ul class="nav flex-row justify-content-end order-1 order-lg-2 align-items-center gap-1">

            {{-- Dark / Light Theme Toggle --}}
            <li class="nav-item">
                <button id="zsc-theme-toggle" title="Toggle theme" aria-label="Toggle dark/light mode">
                    <i class="ph-sun zsc-icon-sun"></i>
                    <i class="ph-moon zsc-icon-moon" style="display:none;"></i>
                </button>
            </li>

            {{-- Notifications --}}
            <li class="nav-item">
                <a href="#" class="navbar-nav-link navbar-nav-link-icon rounded-pill position-relative"
                   data-bs-toggle="offcanvas" data-bs-target="#notifications">
                    <i class="ph-bell"></i>
                    <span class="notification-count badge bg-yellow text-black position-absolute top-0 end-0 translate-middle-top zindex-1 rounded-pill mt-1 me-1">
                        0
                    </span>
                </a>
            </li>

            {{-- User dropdown --}}
            <li class="nav-item nav-item-dropdown-lg dropdown ms-lg-1">
                <a href="#" class="navbar-nav-link align-items-center rounded-pill p-1" data-bs-toggle="dropdown">
                    <div class="status-indicator-container">
                        <img src="/assets/images/demo/users/face26.png" class="w-32px h-32px rounded-pill">
                        <span class="status-indicator bg-success"></span>
                    </div>
                    <span class="d-none d-lg-inline-block mx-lg-2">{{ auth()->user()->name }}</span>
                </a>
                <div class="dropdown-menu dropdown-menu-end">
                    <a class="dropdown-item" href="/admin/change-password">
                        <i class="ph-wrench me-2"></i>
                        Change Password
                    </a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item text-danger" href="/admin/logout">
                        <i class="ph-sign-out me-2"></i>
                        Logout
                    </a>
                </div>
            </li>

        </ul>
    </div>
</div>