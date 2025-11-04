<head>
    <!-- Title Meta -->
    <meta charset="utf-8" />
    <title>@yield('title', 'Hub Certificats') | {{ env('APP_NAME') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="A fully responsive premium admin dashboard template" />
    <meta name="author" content="Techzaa" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- Vendor css (Require in all Page) -->
    <link href="{{ asset('/assets/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Icons css (Require in all Page) -->
    <link href="{{ asset('/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- App css (Require in all Page) -->
    <link href="{{ asset('/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Theme Config js (Require in all Page) -->
    <script src="{{ asset('/assets/js/config.js') }}"></script>
</head>

<body>
    <!-- START Wrapper -->
    <div class="wrapper">
        @include('partials.header')

        <!-- ========== App Menu Start ========== -->
        <div class="main-nav">

            <!-- Sidebar Logo -->
            <div class="logo-box" style="
    padding-top: 40px;
    padding-bottom: 40px;
">
                <a href="{{ route('admin.index') }}" class="logo-dark">
                    <img src="{{ asset('/assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
                    <img src="{{ asset('/assets/images/logo-dark.png') }}" class="logo-lg" alt="logo dark">
                </a>

                <a href="{{ route('admin.index') }}" class="logo-light">
                    <img src="{{ asset('/assets/images/logo-sm.png') }}" class="logo-sm" alt="logo sm">
                    <img src="{{ asset('/assets/images/logo-light.png') }}" class="logo-lg" alt="logo light">
                </a>
            </div>

            <!-- Menu Toggle Button (sm-hover) -->
            <button type="button" class="button-sm-hover" aria-label="Show Full Sidebar">
                <iconify-icon icon="solar:double-alt-arrow-right-bold-duotone"
                    class="button-sm-hover-icon"></iconify-icon>
            </button>

            @include('partials.menu')
        </div>
        <!-- ========== App Menu End ========== -->

        <!-- ==================================================== -->
        <!-- Start right Content here -->
        <!-- ==================================================== -->
        <div class="page-content">

            <!-- Start Container Fluid -->
            <div class="container-fluid">
                @yield('content')
            </div>
            <!-- End Container Fluid -->

            <!-- ========== Footer Start ========== -->
            @include('partials.footer')
            <!-- ========== Footer End ========== -->
        </div>
        <!-- ==================================================== -->
        <!-- End Page Content -->
        <!-- ==================================================== -->
    </div>
    <!-- END Wrapper -->



    <!-- Vendor Javascript (Require in all Page) -->
    <script src="{{ asset('/assets/js/vendor.js') }}"></script>

    <!-- App Javascript (Require in all Page) -->
    <script src="{{ asset('/assets/js/app.js') }}"></script>

    @yield('scripts')
</body>
