@php($c = \App\Models\config::first())
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>SIMPUL TERNAK PROVINSI KALIMANTAN BARAT</title>
    <meta name="description" content="Aplikasi Sri Sarah Lestari membantu analisis kelayakan investasi sapi potong dan sapi perah dengan perhitungan ROI, BEP, dan NPV. Dapatkan simulasi keuangan, manajemen risiko, serta laporan komprehensif untuk keputusan investasi yang lebih cerdas.">
    <link rel="shortcut icon" href="/img/favicon/simpulternak-favicon.ico" type="image/x-icon">
    <link rel="icon" href="/img/favicon/simpulternak-favicon.ico" type="image/x-icon">

     <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="/loading/jquery-easy-loading/dist/jquery.loading.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: "Montserrat", sans-serif;
        }
        .nav-pills .nav-link{border-radius: 0px;}
        .nav-fill{background: black;}
        .nav-fill .nav-link{color: white;}
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
            /* position: absolute; */
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
        }

        #show-control {
            display: block; 
            /* position: absolute;  */
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
            /* position: absolute;  */
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
            /* position: absolute;  */
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
        img.logo_apps{
            max-width: 300px;
        }
        #close-control {
            display: block; /* Tampilkan secara default */
        }
        .accordion-button:not(.collapsed) {
            color: #ffffff;
            background-color: #3c0f09;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }
        .accordion-button:not(.collapsed)::after {
            background-image: none;
        }

        @media (min-width: 768px) { /* Untuk layar lebih besar (tablet & desktop) */
            #close-control {
                display: none;
            }
            #show-control{
                display: none;
            }
        }
        @media (max-width: 767px) { /* Untuk layar mobile */
            #show-control {
                margin-left: 5%; /* Geser ke kiri */
                margin-top: 15%;
                position: absolute;
                border-radius: 5px;
                background:#ffffffb3;
            }
            #map-container{
                padding: 0px;
            }
            .hidden-mobile {
                display: none;
            }
            .show-mobile {
                display: block;
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
            #control-map{
                position: absolute;
                left: 10px;
                top: 100px;
            }
        }
    </style>
    
</head>
<body>

    <div class="container-content">

        <div id="show-control">
            <div class="mobile_control">
                <img src="/img/simpul_ternak_logo.png" class="logo_apps" alt="SIMPUL TERNAK LOGO">
                <button id="btn-control" class="btn btn-secondary btn-xl"><i class="fa fa-list-alt"></i> Menu</button>
            </div>
        </div>
    
        <div class="row">
    
            <div class="col-md-9" >
                <div id="map"></div>
            </div>
            <div id="show_sidebar" class="col-md-3 hidden-mobile">
    
                <div id="control-map">
                    <button id="close-control" class="btn btn-danger"> <i class="fa fa-close"></i> </button>
                    <div style="text-align: center;">
                        <img src="/img/simpul_ternak_logo.png" class="logo_apps" alt="SIMPUL TERNAK LOGO">
                    </div>
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
                                        <label>Komoditi</label>
                                        <select id="komoditi" class="form-control">
                                            @foreach ($komoditi as $item)
                                            <option value="{{ $item }}">
                                                {{ $item }}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="layer_data">
                                            <div class="form-check" style="margin-left: 5px;">
                                                <div style='
                                                height: 10px;
                                                width: 21px;
                                                border: 1px solid #f54242;
                                                background: #f54242;
                                                display: inline-block;'></div>
                                                <label for="layer_DEFISIT" class="form-check-label">
                                                    DEFISIT
                                                    {{-- (<span id="layer_DEFISIT">0</span>) --}}
                                                </label>
                                            </div> 
                                            <div class="form-check" style="margin-left: 5px;">
                                                <div style='
                                                height: 10px;
                                                width: 21px;
                                                border: 1px solid #42f55a;
                                                background: #42f55a;
                                                display: inline-block;'></div>
                                                <label for="layer_SURPLUS" class="form-check-label">
                                                    SURPLUS
                                                    {{-- (<span id="layer_SURPLUS">0</span>) --}}
                                                </label>
                                            </div> 
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
            map?.data?.forEach(function(feature) {
                map.data.remove(feature);
            });
        }
    
        $("#tahun").change(function(){
            loadData();
        });
        $("#komoditi").change(function(){
            loadData();
        });
    
        let listData = {
            "surplus":0,
            "defisit":0
        }
    
        const loadData = () => {
            UnloadMap();
            const year = $("#tahun").val();
            const komoditi = $("#komoditi").val();
            listData = {
                "surplus":0,
                "defisit":0
            }
            $.ajax({
                url:`/simpul-ternak/service?tahun=${year}&komoditi=${komoditi}`,
                type:"GET",
                success:function(response){
    
                    
                    if(!response.status) 
                    return alert(response.message);
    
                    response.data.forEach( (elm,index) => {
                        const nama = elm['komoditi'];
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
    
                        properties.status_neraca == 'defisit' ? 
                        listData.defisit++
                        : listData.surplus++;
    
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
    
                    // $("#layer_DEFISIT").text(listData.defisit);
                    // $("#layer_SURPLUS").text(listData.surplus);
    
                    map.data.setStyle(function(feature){
                        const status_neraca = feature.getProperty("status_neraca") || {};
                        let warna = "#000"
                        if(status_neraca == 'defisit') {
                            warna = "#f54242";
                            listData.surplus++;
                        }
                        if(status_neraca == 'surplus') {
                            warna = "#42f55a";
                            listData.defisit++;
                        }
    
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
                    if(!item) return;
                    if(key == "style") return;
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
            console.log("$(window).width()",$(window).width())
            if ($(window).width() >= 768) {
                console.log("desktop")
                $('#close-control').hide("slow");
            } else {
                $('#close-control').show("slow");
                $("#show_sidebar").show("slow");
                $('#control-map').show("slow");
                $("#show-control").hide("slow");
            }
            $(this).hide("slow");
        });
    
        $('#close-control').click(function(){
            $('#control-map').hide("slow");
            $(this).hide("slow");
            $("#show-control").show("slow");
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
            if ($(window).width() >= 768) {
                $('#btn-control').click();
            }
            $("#tahun").trigger('change');
        });
    
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZzzDr8qs1sU2cxVAk-ewxecN9dBpqirc&callback=initMap&libraries=geometry,places" async defer></script>
</body>
</html>