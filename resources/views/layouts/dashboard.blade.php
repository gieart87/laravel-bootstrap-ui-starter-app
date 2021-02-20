<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
        <meta name="description" content="{{ config('app.name', 'Laravel') }}">
        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- General CSS Files -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

        <!-- CSS Libraries -->
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/jqvmap/dist/jqvmap.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/summernote/dist/summernote-bs4.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/owl.carousel/dist/assets/owl.carousel.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/owl.carousel/dist/assets/owl.theme.default.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/bootstrap-daterangepicker/daterangepicker.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/plugins/select2/dist/css/select2.min.css') }}">
        <!-- Template CSS -->
        <link rel="stylesheet" href="{{ asset('admin/stisla/assets/css/style.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/assets/css/components.css') }}">
        <link rel="stylesheet" href="{{ asset('admin/stisla/assets/css/custom.css') }}">
        <style>
            .btn-filter {
                padding: .500rem .75rem;
            }
        </style>
    </head>

    <body>
        <div id="app">
            <div class="main-wrapper">
                @include('admin.shared.navbar')
                <div class="main-sidebar">
                    @include('admin.shared.sidebar')
                </div>

                <!-- Main Content -->
                <div class="main-content">
                    @yield('content')
                </div>
                @include('admin.shared.footer')
            </div>
        </div>

        <!-- General JS Scripts -->
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.nicescroll/3.7.6/jquery.nicescroll.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js"></script>
        <script src="{{ asset('admin/stisla/assets/js/stisla.js') }}"></script>

        <!-- JS Libraies -->
        <script src="{{ asset('admin/stisla/plugins/jquery-sparkline/jquery.sparkline.min.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/chart.js/dist/Chart.min.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/owl.carousel/dist/owl.carousel.min.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/summernote/dist/summernote-bs4.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js') }}"></script>
        <script src="{{ asset('admin/stisla/plugins/select2/dist/js/select2.min.js') }}"></script>

        <!-- Template JS File -->
        <script src="{{ asset('admin/stisla/assets/js/scripts.js') }}"></script>
        <script src="{{ asset('admin/stisla/assets/js/custom.js') }}"></script>
        <script>
            $(document).ready(function() {
                $('.select2').select2();
                $(".select2-tags").select2({
                    tags: true
                });

            });
        </script>

        <!-- Page Specific JS File -->
        @if ($currentAdminMenu && $currentAdminMenu == 'dashboard')
            <script src="{{ asset('admin/stisla/assets/js/page/index.js') }}"></script>
        @endif
    </body>
</html>
