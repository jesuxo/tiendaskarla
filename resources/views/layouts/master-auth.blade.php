<!doctype html>
<html lang="en" data-bs-theme="light" data-footer="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="SISDATO - Sistema dado para todos" name="description">
    <meta content="Themesbrand" name="author">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ URL::asset('build/images/favicon.ico') }}">


    <title>https://www.tiendaskarla.com.ve - SISDATO</title>
    <meta name="description" content="TIENDAS KARLA">
    <link rel="canonical" href="https://www.tiendaskarla.com.ve">
    <meta property="og:title" content="https://www.tiendaskarla.com.ve - SISDATO">
    <meta property="og:description" content=" TIENDAS KARLA">
    <meta property="og:type" content="WebPage">
    <meta property="og:image" content="https://tiendaskarla.com.ve/build/images/logo.png">
    <meta property="og:url" content="https://tiendaskarla.com.ve">

    <meta name="twitter:title" content="https://www.tiendaskarla.com.ve - SISDATO ">
    <meta name="twitter:description" content=" TIENDAS KARLA ">
    <meta name="twitter:site" content="@tiendaskarlave">
    <script type="application/ld+json">{"@context":"https://schema.org","@type":"WebPage","name":"TIENDAS KARLA","description":" "}</script>

    <!-- head css -->
    @include('layouts.head-css')
</head>

<body>

    <section
        class="auth-page-wrapper position-relative bg-light min-vh-100 d-flex align-items-center justify-content-between">


        <!--content here-->
        @yield('content')
    </section>

    <!--script-->
    @include('layouts.vendor-scripts')
</body>

</html>
