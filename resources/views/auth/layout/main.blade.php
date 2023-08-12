<!DOCTYPE html>
<html dir="ltr"
  lang="en">

<head>
  <meta charset="utf-8">
  <meta content="IE=edge"
    http-equiv="X-UA-Compatible">
  <meta content="width=device-width, initial-scale=1, shrink-to-fit=no"
    name="viewport">
  <title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

  <!-- Global stylesheets -->
  <link href="/assets/fonts/inter/inter.css"
    rel="stylesheet"
    type="text/css">
  <link href="/assets/icons/phosphor/styles.min.css"
    rel="stylesheet"
    type="text/css">
  <link href="/assets_2/css/ltr/all.min.css"
    id="stylesheet"
    rel="stylesheet"
    type="text/css">
  <!-- /global stylesheets -->

  <!-- Core JS files -->
  <script src="/assets/demo/demo_configurator.js"></script>
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
  <!-- /theme JS files -->

</head>

<body>

  <!-- Page content -->
  <div class="page-content">

    <!-- Main content -->
    <div class="content-wrapper">

      <!-- Inner content -->
      <div class="content-inner">

        <!-- Content area -->
        <div class="content d-flex justify-content-center align-items-center">

          <!-- form -->
          @yield('content')
          <!-- /form -->

        </div>
        <!-- /content area -->

      </div>
      <!-- /main content -->

    </div>
    <!-- /page content -->

    @include('auth.layout.notification')

</body>

</html>
