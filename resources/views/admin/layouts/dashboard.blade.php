<!DOCTYPE html>
<html lang="en">

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <!-- Meta, title, CSS, favicons, etc. -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title') - Dashboard</title>

        <!-- Bootstrap core CSS -->

        <link href="{{ asset('gentelella/css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ asset('gentelella/fonts/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ asset('gentelella/css/animate.min.css') }}" rel="stylesheet">

        <!-- Custom styling plus plugins -->
        <link href="{{ asset('gentelella/css/custom.css') }}" rel="stylesheet">
        <link href="{{ asset('gentelella/css/icheck/flat/green.css') }}" rel="stylesheet" />
        <link href="{{ asset('gentelella/css/floatexamples.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/sweetalert/sweetalert.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/uploadm/file-upload.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/croppie/croppie.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('assets/bootstrap-select/bootstrap-select.css') }}" rel="stylesheet" type="text/css" />
        <link href="{{ asset('css/styles.css') }}" rel="stylesheet" type="text/css" />
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

        <script src="{{ asset('js/jquery.min.js') }}"></script>
        <script src="{{ asset('js/app-const.js') }}"></script> 
        <script type="text/javascript">appConst.token = '<?php echo csrf_token(); ?>';</script>
        
        <script src="{{ asset('js/jquery-ui.js') }}"></script>
        <script src="{{ asset('js/merge.js') }}"></script>        



        <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
        <!--[if lt IE 9]>
                <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
                <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
              <![endif]-->

    </head>


    <body class="nav-md">

        <div class="container body">


            <div class="main_container">

                @if(Auth::check() && !isset($page_404))

                @include('admin/tmp/sidemenu')

                <!-- top navigation -->
                @include('admin/tmp/topnav')
                <!-- /top navigation -->
                @endif

                <!-- page content -->
                @yield('main')
                <!-- /page content -->

            </div>

        </div>

        <div id="custom_notifications" class="custom-notifications dsp_none">
            <ul class="list-unstyled notifications clearfix" data-tabbed_notifications="notif-group">
            </ul>
            <div class="clearfix"></div>
            <div id="notif-group" class="tabbed_notifications"></div>
        </div>

        <script src="{{ asset('gentelella/js/bootstrap.min.js') }}"></script>

        <!-- gauge js -->

        <!-- bootstrap progress js -->
        <script src="{{ asset('gentelella/js/progressbar/bootstrap-progressbar.min.js') }}"></script>
        <script src="{{ asset('gentelella/js/nicescroll/jquery.nicescroll.min.js') }}"></script>
        <!-- icheck -->
        <script src="{{ asset('gentelella/js/icheck/icheck.min.js') }}"></script>
        <!-- daterangepicker -->
        <script type="text/javascript" src="{{ asset('gentelella/js/moment/moment.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/datepicker/daterangepicker.js') }}"></script>


        <script src="{{ asset('gentelella/js/custom.js') }}"></script>

        <!-- flot js -->
        <!--[if lte IE 8]><script type="text/javascript" src="{{ asset('gentelella/js/excanvas.min.js') }}"></script><![endif]-->
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.pie.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.orderBars.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.time.min.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/date.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.spline.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.stack.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/curvedLines.js') }}"></script>
        <script type="text/javascript" src="{{ asset('gentelella/js/flot/jquery.flot.resize.js') }}"></script>

        <!-- worldmap -->
        <script type="text/javascript" src="{{ asset('gentelella/js/maps/gdp-data.js') }}"></script>

        <!-- pace -->
        <script src="{{ asset('gentelella/js/pace/pace.min.js') }}"></script>

        <!-- skycons -->
        <script src="{{ asset('gentelella/js/skycons/skycons.min.js') }}"></script>
        <script src="{{ asset('assets/sweetalert/sweetalert.min.js') }}"></script>

        <script src="{{ asset('assets/uploadm/jsUpload.js') }}"></script>
        <script src="{{ asset('assets/uploadm/file-upload.js') }}"></script>
        <script src="{{ asset('assets/croppie/croppie.js') }}"></script>
        <script src="{{ asset('assets/bootstrap-select/bootstrap-select.js') }}"></script>
        <script src="{{ asset('gentelella/js/nprogress.js') }}"></script>

        <script src="{{ asset('js/form-validation.js') }}"></script>
        <script src="{{ asset('js/scripts.js') }}"></script>
    </body>

</html>
