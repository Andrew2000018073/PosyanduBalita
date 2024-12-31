<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Posyandu Kelapa Kampit</title>

    <!-- AdminLTE CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">

    <!-- Google Fonts: Source Sans Pro -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">

    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet"
        href="{{ asset('lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/jqvmap/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
    <link rel="stylesheet" href="{{ asset('lte/plugins/summernote/summernote-bs4.min.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">


    <!-- Ionicons -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://cdn.jsdelivr.net/npm/ionicons@latest/dist/ionicons/ionicons.js"></script>
</head>

<body class="antialiased">
    <div id="app">
        <div class="wrapper">
            <!-- Navbar & Sidebar -->
            <sidebar-component v-if="!isLoginPage"></sidebar-component>
            <nav-component v-if="!isLoginPage"></nav-component>

            <div v-if="!isLoginPage">
                <!-- Main content -->
                <div class="content-wrapper">
                    <section class="content">
                        <div class="container-fluid">
                            <router-view></router-view>
                        </div><!-- /.container-fluid -->
                    </section>
                </div>

                <!-- /.content -->
            </div>


            <div v-else>
                <!-- Hanya konten login -->
                <router-view></router-view>
            </div>






            <!-- Footer -->
            <footer class="main-footer" v-if="!isLoginPage">
                <strong>Copyright &copy; 2024 <a
                        href="https://www.youtube.com/watch?v=py6MgBsXjYc&list=TLPQMDExMDIwMjS-SY3FmE2AdA&index=8">Andrew
                        Ikhtisar Afiq</a>.</strong>
                All rights reserved.
                <div class="float-right d-none d-sm-inline-block">
                    <b>Version</b> 12.27.24
                </div>
            </footer>
        </div>
    </div>

    <!-- App JS -->
    <script src="{{ asset('js/app.js') }}"></script>

    <!-- AdminLTE & Plugins -->
    <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <script>
        $.widget.bridge('uibutton', $.ui.button)
    </script>
    <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
    <script src="{{ asset('lte/dist/js/adminlte.js') }}"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</body>

</html>

<style>
    /* Styling untuk router link */
    .router-link-active {
        color: inherit;
        text-decoration: none;
    }

    .router-link {
        color: inherit;
        text-decoration: none;
    }
</style>
