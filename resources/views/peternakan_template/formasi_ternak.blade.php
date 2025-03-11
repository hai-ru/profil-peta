@php($c = \App\Models\config::first())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>FORMASI TERNAK PROVINSI KALIMANTAN BARAT</title>
    <meta name="description" content="FORMASI TERNAK adalah aplikasi yang menyediakan data dan analisis parameter peternakan di Provinsi Kalimantan Barat, Indonesia. Pantau populasi ternak, kesehatan hewan, produktivitas, dan manajemen peternakan secara real-time untuk mendukung pertumbuhan sektor peternakan." />
    <link rel="shortcut icon" href="/img/favicon/formasi-favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon/formasi-favicon.ico" type="image/x-icon">

     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="/loading/jquery-easy-loading/dist/jquery.loading.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <style>
        body{
            font-family: "Open Sans", serif !important;
            font-optical-sizing: auto;
            background: #a9ac9a;
        }
        iframe {
            height: 100vh;
            width: 100%;
        }
        .kotak {
            border: 1px solid blue;
            padding: 10px;
            border-radius: 10px;
            margin-bottom: 10px;
            margin-top: 10px;
        }
        .logo_timpa{
            float: left;
            z-index: 99;
            position: absolute;
            left: 11px;
            top: 9px;
        }
        #map {
            height: 100vh;
            width: 100%;
            margin-top: 0px;
            position: relative;
        }

        #control-map{
            /* display: none;  */
            display: block; 
            /* position: absolute;  */
            margin-top: 0.5%;
            z-index: 2; 
            background: white; 
            padding: 25px; 
            right: 70px;
            border-radius: 10px;
        }

        #show-control {
            display: block; 
            position: absolute; 
            margin-top: 0.5%;
            margin-left: 84%;
            z-index: 1; 
            background: white; 
            padding: 10px; 
        }

        #accordion {
            margin-top: 10px;
        }

        .icon-legenda {
            height: 10px;
            width: 10px;
            float: right; 
            margin-top: 8px; 
        }

        .marker-peta {
            float: right;
        }

        .marker-peta > img{
            height: 30px;
        }

        #log-control{
            display: block; 
            position: absolute; 
            left: 50px;
            top: 90vh;
            z-index: 1; 
            background: white; 
            padding: 10px; 
        }
        #timer-control{
            left: 155px;
            top: 85vh;
            display: block; 
            position: absolute; 
            z-index: 1; 
            background: white; 
            padding: 10px; 
        }
        .mt-10 {
            margin-top: 15px !important;
            margin-bottom: 15px !important;
        }
        .lebih{
            color: red;
            font-weight: bold;
        }
        .accordion-body form {
            min-width: 200px;
        }
        .kotak h5 {
            margin-bottom: 10px;
            text-align: center;
        }
        .accordion-header{
            min-width: 200px;
        }
         .navbar-custom {
            background-color: #fcc46f;
            color: #2e4134;
        }
        .navbar-brand img {
            height: 50px;
        }
        .logo-right {
            max-height: 100px;
        }
        .navbar-brand{
            display: flex;
            align-items: center;
        }
        .navbar-brand .text_container{
            margin-left: 10px;
        }
        .accordion-button:not(.collapsed) {
            color: #ffffff;
            background-color: #2d3e2f;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }
        .accordion-button:not(.collapsed)::after {
            background-image: none;
        }
        img.logo_disbun{
            display: none;
        }
        .rounded-container {
            background-color: #2d3e2f;
            padding: 20px;
            border-radius: 20px;
            margin-top: 30px;
        }
        .image-container img {
            border-radius: 15px;
            width: 100%;
            height: auto;
            object-fit: cover;
        }
        img.logo_formasi{
            width: 300px;
        }
        #map_control_container{
            padding-right:30px;
        }
        img.logo_apps{
            max-width: 300px;
        }
        @media (min-width: 768px) { /* Untuk layar lebih besar (tablet & desktop) */
            #close-control {
                display: none;
            }
            #show-control{
                display: none;
            }
        }
        /* Untuk layar mobile */
        @media (max-width: 767px) { 
            .hidden-mobile {
                display: none !important;
            }
            #show-control {
                margin-left: 5%; /* Geser ke kiri */
                margin-top: 15%;
                position: absolute;
                border-radius: 5px;
                background:#2d3e2f;
            }
            #control-map{
                left: 20px;
                position: absolute;
                top: 100px;
                display: none;
            }
            .navbar-brand {
                align-items: center;
                flex-direction: column;
                text-align: center;
            }
            img.logo_disbun{
                display: block;
                margin-left: 10px;
            }
            .nav-logo{
                display: flex;
                align-items: center;
            }
            img.logo_apps{
                width: 200px;
            }
            div.mobile_control{
                display: flex;
                flex-direction: column;
                justify-content: center;
                align-items: center;
            }
        }
    </style>
    
</head>
<body>

    <div class="container-content">

        <div id="show-control">
            <div class="mobile_control">
                <img src="/img/formasi_ternak_logo.png" class="logo_apps" alt="sri sarah lestari">
                <button id="btn-control" class="btn btn-secondary btn-xl"><i class="fa fa-list-alt"></i> Menu</button>
            </div>
        </div>
    
        <div class="row">
    
            <div class="col-md-9" >
                <div id="map"></div>
            </div>

            <div class="col-md-3" id="map_control_container">
                <div class="container rounded-container featured_image hidden-mobile">
                    <img src="/img/formasi_ternak_logo.png" class="logo_formasi"  />
                </div>
                <div class="container rounded-container featured_image" style="background: #678545;">
        
                    <div id="control-map">
                        <button id="close-control" class="btn btn-danger"> <i class="fa fa-close"></i> </button>
        
                        <div class="accordion" id="accordion">
        
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse0">
                                        LEGENDA
                                    </button>
                                </h2>
                                <div id="collapse0" class="accordion-collapse collapse show">
                                    <div class="accordion-body">
                                        <div class="form-group">
                                            <label>Tahun</label>
                                            <select id="tahun" class="form-control">
                                                @foreach ($tahun as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label>Jenis Ternak</label>
                                            <select id="jenis" class="form-control">
                                                @foreach ($jenis_ternak as $item)
                                                <option value="{{ $item }}">
                                                    {{ $item }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="layer_data">
                                            @foreach ($list_kota as $item)
                                                <div class="form-check" style="margin-left: 5px;">
                                                    <div style='
                                                    height: 10px;
                                                    width: 21px;
                                                    border: 1px solid {{$item->warna}};
                                                    background: {{$item->warna}};
                                                    display: inline-block;'></div>
                                                    <label for="layer_{{$item->id}}" class="form-check-label">
                                                        {{$item->nama}}
                                                    </label>
                                                </div> 
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
        
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        PENCARIAN
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse">
                                <div class="accordion-body">
        
                                    <div class="d-grid">
                                        <button class="btn btn-danger" onclick="ResetMarker();">Bersihkan Marker Pada Peta</button>
                                    </div>
        
                                    <hr>
        
                                    <div class="form-group">
                                        <label>Pencarian Lokasi</label>
                                        <input class="form-control" type="text" name="search" id="cari-lokasi" placeholder="Cari Lokasi Disini..">
                                    </div>
        
                                    <hr>
                                    <p style="font-weight: bold;font-size: 14px;"><u>PENCARIAN DENGAN KOORDINAT</u></p>
                                    <form onsubmit="return Koordinat();">
                                        <div class="form-group">
                                            <label>Longitude</label>
                                            <input class="form-control" type="number" name="long" id="long" placeholder="Longitude.." step="0.00000001" value="109.31827">
                                        </div>
                                        <div class="form-group">
                                            <label>Latitude</label>
                                            <input class="form-control" type="number" name="lat" id="lat" placeholder="Latitude.." step="0.00000001" value="-0.10814">
                                        </div>
                                        <div class="d-grid mt-3">
                                            <button type="submit" class="btn btn-primary pull-right"> Cari</button>
                                        </div>
                                    </form>
                                    
                                </div>
                                </div>
                            </div>
        
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        PENGUKURAN
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse">
                                <div class="accordion-body">
                                    <p style="text-align: justify;">
                                        Cara Mengukur Pada Peta :
                                        <ol>
                                            <li>Untuk Pengukuran Pada Peta, Silahkan klik kanan pada peta dan Klik Hitung Jarak.</li>
                                            <li>Lalu klik kiri pada titik-titik yang di inginkan</li>
                                            <li>Jika Ingin Mengukur Luas Silahkan pertemukan titik akhir dan titik awal, maka akan otomatis menghitung luasan area</li>
                                            <li>Jika Selesai, dan ingin menghapus perhitungan. Klik Kanan Pada Peta dan Klik Bersihkan Perhitungan</li>
                                        </ol>
                                    </p>
                                </div>
                                </div>
                            </div>
        
                        </div>
                    </div>
                </div>
            </div>
    
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="/js/MeasureTool.js"></script>
    <script src="/infobubble/src/infobubble.js"></script>
    <script src="/geoxml3/kmz/geoxml3.js"></script>
    <script src="/geoxml3/kmz/ZipFile.complete.js"></script>
    <script src="/loading/jquery-easy-loading/dist/jquery.loading.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>
    <script src='https://unpkg.com/@turf/turf@6/turf.min.js'></script>
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <script type="text/javascript">
        var map;
        var ib;
        let infowindow;
        var Layers = [];
        var LayersDasar = [];
        var measureTool;
        var markers = [];
        var ParseDasar = [];
        var MyCoor = {lat: -0.10814501846297607, lng: 109.3182775878906};
        var zooms = 8;
        var markerpoktan = [];
        let parserGeoxml;
    
        $("#btn-log").click(function(){
            $("#log_layer").modal("show");
        });
    
        let start_render, startTimer;
        $("#stop_btn").click(function(){
            if(startTimer !== undefined){
                clearInterval(startTimer);
            }
        });
    
        $(".cekbox").click(function(e){
            const parent = $("#collapse0")
            let ids = []
            parent.find("input[type=checkbox]:checked").each(function() {
                ids.push($(this).val());
            });
            loadData(ids)
           
        })
    
        const UnloadMap = () => {
            map.data.forEach(function(feature) {
                map.data.remove(feature);
            });
        }
    
        $("#tahun").change(function(){
            const tahun = $('#tahun').val();
            const jenis = $('#jenis').val();
            loadData(tahun);
        });
        $("#jenis").change(function(){
            const tahun = $('#tahun').val();
            const jenis = $('#jenis').val();
            loadData(tahun);
        });
    
        const loadData = (year,jenis) => {
            UnloadMap();
            $.ajax({
                url:`/formasi-ternak/service?tahun=${year}&jenis_ternak=${jenis}`,
                type:"GET",
                success:function(response){
    
                    
                    if(!response.status) 
                    return alert(response.message);
    
                    response.data.forEach( (elm,index) => {
                        const nama = elm['COL 3'];
                        console.log("nama",nama,elm);
                        let geojson = null;
                        try {
                            geojson = JSON.parse(elm.geojson);
                            let properties = elm;
                            delete properties.geojson;
                            delete properties.center;
                            delete properties.id;
                            delete properties.kabupaten_id;
                            geojson.properties = properties;
                            // console.log("geojson",geojson);
                        } catch (error) {
                            geojson = null
                        }
                        if(geojson){
                            map.data.addGeoJson(
                                geojson,
                                nama
                            )
                        }
                    })
    
                    map.data.setStyle(function(feature){
                        const warna = feature.getProperty("warna") || {};
                        return {
                            strokeColor: warna,
                            strokeOpacity: 1.0,
                            strokeWeight: 3.0,
                            fillColor: warna,
                            fillOpacity: 0.5
                        }
                    })
                }
            })
        }
    
        function ResetMarker() {
            // Clear out the old markers.
              markers.forEach(function(marker) {
                marker.setMap(null);
              });
              markers = [];
              return false;
        }
    
        function popitup(url,windowName) {
           newwindow=window.open(url,windowName,'height=500,width=800');
           if (window.focus) {newwindow.focus()}
           return false;
        }
    
        function initMap() {
            infowindow = new google.maps.InfoWindow();
            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -0.10814501846297607, lng: 109.3182775878906},
                zoom: zooms,
                mapTypeId: google.maps.MapTypeId.HYBRID,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_CENTER
                },
                zoomControl: true,
                zoomControlOptions: {
                    position: google.maps.ControlPosition.TOP_RIGHT
                },
                scaleControl: true,
                streetViewControl: true,
                streetViewControlOptions: {
                    position: google.maps.ControlPosition.TOP_RIGHT
                },
                fullscreenControl: true
            });
    
            ib = new InfoBubble({
                animation:true,
                shadowStyle: 0,
                padding: 0,
                backgroundColor: 'white',
                hideCloseButton: false,
                arrowPosition: 50,
                arrowStyle: 0
            });
    
            measureTool = new MeasureTool(map, {
              showSegmentLength: true,
              tooltip: true,
              unit: MeasureTool.UnitTypeId.METRIC // metric or imperial
            });
            
            measureTool.addListener('measure_start', () => {
                console.log('started');
                // measureTool.removeListener('measure_start')
            });
            measureTool.addListener('measure_end', (e) => {
                console.log('ended', e.result);
                //      measureTool.removeListener('measure_end');
            });
            measureTool.addListener('measure_change', (e) => {
                console.log('changed', e.result);
                //      measureTool.removeListener('measure_change');
            });
    
            var input = document.getElementById('cari-lokasi');
            var searchBox = new google.maps.places.SearchBox(input);
    
            map.addListener('bounds_changed', function() {
              searchBox.setBounds(map.getBounds());
            });
    
            searchBox.addListener('places_changed', function() {
    
                var places = searchBox.getPlaces();
    
                if (places.length == 0) {
                    return;
                }
    
                ResetMarker();
    
                places.forEach(function(place) {
    
                    let long = place.geometry.location.lng().toFixed(8);
                    let lat = place.geometry.location.lat().toFixed(8);
    
                    console.log("LAT : ",lat);
                    console.log("LONG : ",long);
    
                    $("#long").val(long);
                    $("#lat").val(lat);
    
                    Koordinat();
    
                });
    
            });
            
            google.maps.event.addListener(map, "click", function(event) {
                if(ib !== undefined && ib !== null ) ib.close();
            });
    
            map.data.addListener('click', function(event) {
                const f = event.feature;
                let contentString = "";
                f.forEachProperty((item,key) => {
                    if(key == "style") return;
                    if(item == null)  return;
                    if(item.tipe == "marker") return;
                    let content = item;
                    if(item.tipe == "image"){
                        content = `<a target="_blank" href="${item.data}"><img src="${item.data}" height="100" /></a>`
                    }
                    contentString += `<tr>
                        <td>${key}</td>
                        <td>:</td>
                        <td>${content}</td>
                    </tr>`
                })
                const str = `<table>${contentString}</table>`;
                infowindow.setContent(str)
                infowindow.setPosition(event.latLng)
                infowindow.setOptions({pixelOffset: new google.maps.Size(0,-34)});
                infowindow.open(map)
            })
        }
    
        function Koordinat() {
    
            var long = parseFloat($('#long').val());
            var lat = parseFloat($('#lat').val());
            MyCoor = {lat: lat, lng: long};
    
            ResetMarker();
    
            var contentString = '<div id="content">Silahkan Klik Pada Peta untuk memindahkan marker'+'</div>';
            var infowindow = new google.maps.InfoWindow({
                content: contentString
            });
    
            // Create a marker for each place.
            markers.push(new google.maps.Marker({
                map: map,
                position: MyCoor
            }));
    
            infowindow.open(map, markers[0]);
    
            google.maps.event.addListener(map, 'click', function(event) {
    
                markers.map((item,index)=>{
                    item.setPosition(event.latLng);
                });
            
                $("#lat").val( event.latLng.lat().toFixed(8) );
                $("#long").val( event.latLng.lng().toFixed(8) );
            });
    
            map.setCenter(MyCoor);
            map.setZoom(15);
    
            return false;
    
        }
    
        function polygonMouseOver(poly, text) {
    
              google.maps.event.addListener(poly,'mouseover', function(evt) {
                ib.setContent(text);
                ib.setPosition(evt.latLng);
                ib.setMap(map);
                ib.open();
              });
              google.maps.event.addListener(poly,'mouseout', function(evt) {
                ib.close();
              });
    
        }
    
    
        $('#btn-control').click(function(){
            console.log($(window).width());
            if ($(window).width() >= 768) {
                $('#close-control').hide("slow");
            } else {
                $('#close-control').show("slow");
            }
            $('#control-map').show("slow");
            $(this).hide("slow");
        });
    
        $('#close-control').click(function(){
            $('#control-map').hide("slow");
            $(this).hide("slow");
            $('#btn-control').show("slow");
        });
    
        $( document ).ajaxStart(function() {
    
              $('body').loading({
                  stoppable: true
                });
    
        });
    
        $( document ).ajaxStop(function() {
    
              $('body').loading('stop');
    
        });
    
        $(document).ready(function(){
            console.log("$(window).width()",$(window).width())
            if ($(window).width() >= 768) {
                $('#btn-control').click();
            }
            $("#tahun").trigger('change');
        });
    
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZzzDr8qs1sU2cxVAk-ewxecN9dBpqirc&callback=initMap&libraries=geometry,places,marker" async defer></script>
</body>
</html>