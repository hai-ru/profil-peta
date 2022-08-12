@php($c = \App\Models\config::first())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>{{ $c->judul }}</title>

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
        <h5 class="text-center my-2">{{ $c->judul }}</h5>
        <ul class="nav nav-pills nav-fill">
          @foreach ($c->menu as $item)    
            <li class="nav-item">
              <a class="nav-link {{ Request::is($item["link"]) ? "active" : "" }}" href="{{ $item["link"] }}">{{$item["text"]}}</a>
            </li>
          @endforeach
        </ul>
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('js')
</body>
</html>