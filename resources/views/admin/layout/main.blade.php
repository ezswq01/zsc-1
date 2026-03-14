<!DOCTYPE html>
<html dir="ltr" lang="en" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ \App\Models\Setting::first()->app_name }}</title>

    {{--
        INIT SCRIPT — runs before ANY paint:
        · Applies saved dark/light theme → prevents colour flash
        · NOTE: sidebar-xs is applied immediately after <body> opens (see below)
    --}}
    <script>
        (function () {
            var t = localStorage.getItem('zsc-theme') || 'light';
            document.documentElement.setAttribute('data-theme', t);
        })();
    </script>

    <!-- Global stylesheets -->
    <link href="/assets/fonts/inter/inter.css" rel="stylesheet" type="text/css">
    <link href="/assets/icons/phosphor/styles.min.css" rel="stylesheet" type="text/css">
    <link href="/assets_2/css/ltr/all.min.css" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="/assets_2/css/custom.css" rel="stylesheet">
    <link href="/assets_2/css/global-theme.css" rel="stylesheet">
    <!-- /global stylesheets -->

    <!-- Core JS files -->
    <script src="/assets/js/bootstrap/bootstrap.bundle.min.js"></script>
    <!-- /core JS files -->

    <!-- Theme JS files -->
    <script src="/assets/js/jquery/jquery.min.js"></script>
    <script src="/assets/js/vendor/forms/selects/select2.min.js"></script>
    <script src="/assets/js/vendor/notifications/sweet_alert.min.js"></script>
    <script src="/assets/js/vendor/tables/datatables/datatables.min.js"></script>
    <script src="/assets/demo/pages/extra_sweetalert.js"></script>
    <script src="/assets/demo/pages/form_select2.js"></script>
    <script src="/assets_2/js/app.js"></script>
    <script src="/assets/demo/pages/datatables_basic.js"></script>
    <script src="/assets/js/vendor/ui/moment/moment.min.js"></script>
    <script src="/js/app.js"></script>
    <script src="/assets/js/vendor/pickers/daterangepicker.js"></script>
    <!-- /theme JS files -->

    @stack('style')
</head>

{{--
    ─────────────────────────────────────────────────────────────────
    BODY — sidebar-xs applied SYNCHRONOUSLY as the very first script
    inside <body>. This fires before the browser lays out anything,
    so the sidebar is already in icon-only mode on first paint.
    Limitless reads `sidebar-xs` on <body> to switch to slim mode.
    ─────────────────────────────────────────────────────────────────
--}}
<body>
<script>
    (function () {
        // Default = collapsed. Only expand if the user previously chose expanded.
        var state = localStorage.getItem('zsc-sidebar') || 'collapsed';
        if (state === 'collapsed') {
            document.body.classList.add('sidebar-xs');
        }
    })();
</script>

<!-- Main navbar -->
@include("admin.layout.navbar")
<!-- /main navbar -->

<!-- Page content -->
<div class="page-content">

    <!-- Main sidebar -->
    @include("admin.layout.sidebar")
    <!-- /main sidebar -->

    <!-- Main content -->
    <div class="content-wrapper">

        <!-- Inner content -->
        <div class="content-inner">

            <!-- Page header -->
            @stack("header")
            <!-- /page header -->

            <!-- Content area -->
            <div class="content">
                @yield("content")
            </div>
            <!-- /content area -->

            <!-- Footer -->
            @include("admin.layout.footer")
            <!-- /footer -->

        </div>
        <!-- /inner content -->

    </div>
    <!-- /main content -->

</div>
<!-- /page content -->

<!-- Notifications offcanvas -->
@include("admin.layout.notification")

<script src="{{ asset("assets_2/js/custom.js") }}"></script>

{{-- ============================================================
     GLOBAL SCRIPTS
     1. Theme toggle (light / dark) — syncs sun/moon icon
     2. Sidebar resize button — persists collapsed/expanded state
============================================================ --}}
<script>
(function () {

    /* ── 1. Theme ──────────────────────────────────────────── */
    function applyTheme(theme) {
        document.documentElement.setAttribute('data-theme', theme);
        localStorage.setItem('zsc-theme', theme);
        var btn = document.getElementById('zsc-theme-toggle');
        if (!btn) return;
        btn.querySelector('.zsc-icon-sun').style.display  = theme === 'dark'  ? 'none'   : 'inline';
        btn.querySelector('.zsc-icon-moon').style.display = theme === 'dark'  ? 'inline' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Sync icon with current theme on load
        applyTheme(localStorage.getItem('zsc-theme') || 'light');

        // Theme toggle click
        var themeBtn = document.getElementById('zsc-theme-toggle');
        if (themeBtn) {
            themeBtn.addEventListener('click', function () {
                var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                applyTheme(next);
            });
        }

        /* ── 2. Sidebar resize — persist state ─────────────── */
        document.querySelectorAll('.sidebar-main-resize').forEach(function (btn) {
            btn.addEventListener('click', function () {
                // Limitless toggles sidebar-xs on <body>; read after its handler runs
                setTimeout(function () {
                    var collapsed = document.body.classList.contains('sidebar-xs');
                    localStorage.setItem('zsc-sidebar', collapsed ? 'collapsed' : 'expanded');
                }, 50);
            });
        });
    });

})();
</script>

@stack("js")

</body>
</html>