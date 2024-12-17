<!DOCTYPE html>
<html dir="ltr" lang="en">

<head>
    <meta charset="utf-8">
    <meta content="IE=edge" http-equiv="X-UA-Compatible">
    <meta content="width=device-width, initial-scale=1, shrink-to-fit=no" name="viewport">
	 <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <title>{{ \App\Models\Setting::first()->app_name }}</title>

    <!-- Global stylesheets -->
    <link href="/assets/fonts/inter/inter.css" rel="stylesheet" type="text/css">
    <link href="/assets/icons/phosphor/styles.min.css" rel="stylesheet" type="text/css">
    <link href="/assets_2/css/ltr/all.min.css" id="stylesheet" rel="stylesheet" type="text/css">
    <link href="/assets_2/css/custom.css" rel="stylesheet">
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
    {{-- <script src="/assets/js/vendor/editors/ckeditor/ckeditor_classic.js"></script>
    <script src="/assets/demo/pages/editor_ckeditor_classic.js"></script> --}}
    <script src="/assets/js/vendor/ui/moment/moment.min.js"></script>
    <script src="/js/app.js"></script>
    <script src="/assets/js/vendor/pickers/daterangepicker.js"></script>
    <!-- /theme JS files -->

    @stack('style')

</head>

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

<!-- SA -->
@include("admin.layout.notification")
<!-- /SA -->

</body>

<script src="{{ asset("assets_2/js/custom.js") }}"></script>

@stack("js")

</html>
