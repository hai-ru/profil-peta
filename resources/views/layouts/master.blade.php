<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sistem Informasi Infrastruktur Perbatasan</title>

     <!-- Bootstrap CSS -->
     <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .nav-pills .nav-link{border-radius: 0px;}
        .nav-fill{background: black;}
        .nav-fill .nav-link{color: white;}
    </style>
    @stack('css')
</head>
<body>
    <div class="container-content">
        <h5 class="text-center my-2">SISTEM INFORMASI INFRASTRUKTUR PERBATASAN BERBASIS WEB GIS</h5>
        <ul class="nav nav-pills nav-fill">
            <li class="nav-item">
              <a class="nav-link {{ Request::URL() == route("/") ? "active" : "" }}" href="{{ route("/") }}">Beranda</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::URL() == route("rekapitulasi") ? "active" : "" }}" href="{{ route("rekapitulasi") }}">Rekapitulasi</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::URL() == route("basis-data") ? "active" : "" }}" href="{{ route("basis-data") }}">Basis Data</a>
            </li>
            <li class="nav-item">
              <a class="nav-link {{ Request::URL() == route("web-gis") ? "active" : "" }}" href="{{ route("web-gis") }}">Web GIS</a>
            </li>
        </ul>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>