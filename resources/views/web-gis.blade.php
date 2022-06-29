@extends('layouts.master')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" />
    <link href="/loading/jquery-easy-loading/dist/jquery.loading.css" rel="stylesheet">
    <style>
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
            display: none; 
            position: absolute; 
            margin-top: 0.5%;
            z-index: 2; 
            background: white; 
            padding: 25px; 
            right: 70px;
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
    </style>
@endpush

@section('content')

	<div class="row">

		<div class="col-md-12">

			<div id="show-control">
				<button id="btn-control" class="btn btn-primary btn-xl"><i class="fa fa-cog"></i> Pengaturan</button>
			</div>

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
                                
                                <form id="data">

                                    <div class="form-group">
                                        <label>Kab/Kota</label>
                                        <select id="kab_id" name="kab_id" class="form-control">
                                            <option value="semua">Semua Data</option>
                                            @foreach (\App\Models\Kabupaten::orderBy("id","desc")->get() as $item)
                                                <option value="{{$item->id}}">{{$item->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Kecamatan</label>
                                        <select id="daerah_id" name="daerah_id" class="form-control">
                                            <option value="semua">Semua Data</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>Desa</label>
                                        <select id="daerah_id" name="daerah_id" class="form-control">
                                            <option value="semua">Semua Data</option>
                                        </select>
                                    </div>

                                    <div class="kotak">
                                        <h5>SEKTOR</h5>
                                        <div class="sektor_data">
                                            <div class="form-check">
                                                <input id="flexCheckDefault" class="form-check-input" type="checkbox" value="1" name="sektor">
                                                <label for="flexCheckDefault" class="form-check-label">
                                                    Air Bersih
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-primary" id="refresh"> Lihat</button>
                                    </div>

                                </form>

                                <form id="peta-dasar">

                                    <div class="kotak">
                                        <h5>LAYER</h5>
                                        <div class="layer_data">
                                            <div class="form-check">
                                                <input id="flexCheckDefault" class="form-check-input" type="checkbox" value="1" name="sektor">
                                                <label for="flexCheckDefault" class="form-check-label">
                                                    KAWASAN HUTAN LINDUNG
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid">
                                        <button class="btn btn-primary" id="refresh"> Lihat</button>
                                    </div>

                                </form>
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

		<div class="col-md-12" >
			<div id="map"></div>
		</div>

	</div>
@endsection

@push('js')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script type="text/javascript" src="/js/MeasureTool.js"></script>
<script src="/infobubble/src/infobubble.js"></script>
<script src="/geoxml3/kmz/geoxml3.js"></script>
<script src="/geoxml3/kmz/ZipFile.complete.js"></script>
<script src="/loading/jquery-easy-loading/dist/jquery.loading.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

<script type="text/javascript">
    var map;
    var ib;
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

    // function myTimer(){
    //     let seconds = 0;
    //     var current = moment();
    //     if(start_render !== undefined){
    //         seconds = current.diff(start_render, 'seconds');
    //     }
    //     let format = seconds+" detik";
    //     $("#timer").html(format);
    // }

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

        parserGeoxml = new geoXML3.parser({
            map: map,
            afterParse: useTheData,
            zoom : false
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
        
            ib = new InfoBubble({
                      animation:false,
                    //   maxHeight:100,
                      shadowStyle: 0,
                      padding: 0,
                      backgroundColor: 'white',
                      // borderRadius: 4,
                      // arrowSize: 0,
                      // disableAutoPan: true,
                      hideCloseButton: true,
                      arrowPosition: 50,
                      arrowStyle: 0
                    });

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

    let placemarks_counter = 0;
    function useTheData(doc) {
        placemarks_counter = placemarks_counter+doc[0].placemarks.length;
        $("#log_placemark_counter").html(placemarks_counter);
        $('body').loading('stop');
    };
    
    let placemarks_counter_dasar = 0;
    function useTheDataDasar(doc) {
        placemarks_counter_dasar = placemarks_counter_dasar+doc[0].placemarks.length;
        $("#log_placemark_dasar_counter").html(placemarks_counter_dasar);
      $('body').loading('stop');
    };

    function addMyMarker(placemark,doc) {

        var marker = new google.maps.Marker({
            title: placemark.name,
            position: placemark.latlng,
            map: map,
            //This case href is the tag in my KML
            icon: "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB4AAAAeCAYAAAA7MK6iAAAACXBIWXMAAA7EAAAOxAGVKw4bAAAAoklEQVRIiWP8//8/w0AApgGxddTiUYtHosVb/lszMv5nJBkn/Z/LwIC3gGAh5DLxjKMMDI6KpHln/yKCSgZrUI9aDAUXZjMwhEsxMIT7MTBcoKfFp+uhjDMMDKf30dFi30Yow4SBwdSJLCMIZiesQCKVgWFlKllaYWCIJa5Ri8kB////x4M3/7eCFPYk4sT/c/7//4/PbMbRVuaoxaMWD3mLASKxZOQDbEC1AAAAAElFTkSuQmCC"
        });

        ib = new InfoBubble({
            animation:false,

            shadowStyle: 0,
            padding: 0,
            backgroundColor: 'white',
            hideCloseButton: true,
            arrowPosition: 50,
            arrowStyle: 0
        });

        ib.close();

        google.maps.event.addListener(marker, 'click', function(evt) {

            ib.setContent(placemark.description);
            ib.setPosition(evt.latLng);
            ib.setMap(map);
            ib.open();
        });
        
        return marker;
    }

    $('#data').submit(function(){

        var data = "tahun="+$('#tahun').val()+"&daerah_id="+$('#daerah_id').val()+"&kategori_id=";

        var id = $("#data input[name=kategori]:checkbox:checked").map(function(){
          return $(this).val();
        }).get();

        for (var i = 0; i < id.length; i++) {
            data += id[i]+',';
        }
        data = data.slice(0,-1);

        placemarks_counter = 0;
        $("#log_placemark_counter").html(0);

        start_render = moment();

        if(startTimer !== undefined){
            clearInterval(startTimer);
        }

        // startTimer = setInterval(function(){
        //     myTimer();
        // }, 1000);

        $.ajax({
            url: '/api/layer',
            type: "GET",
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            cache: true,
            success: function (json) {

                parserGeoxml.docs.map((item,index)=>{
                    parserGeoxml.hideDocument(item);
                });
                if(parserGeoxml.docs.length > 0){
                    placemarks_counter = 0;
                }

                $("#log_layer_link").empty();
                let counter_kml = 0;
                
                json.forEach(function(entry) {
                    
                    let link = '{{ env("APP_URL") }}'+entry.kml;

                    counter_kml++;
                    
                    let status_data = entry.size > 3000000 ? "lebih" : "biasa";

                    $("#log_layer_link").append(
                        "<tr>"+
                            "<td class="+'"'+status_data+'"'+">"+link+" ("+entry.sizef+")"+"</td>"+
                        "</tr>"
                    );
                    let status = false;
                    parserGeoxml.docs.map((item,index)=>{
                        if(item.baseUrl == link){
                            parserGeoxml.showDocument(item);
                            placemarks_counter = placemarks_counter+item.placemarks.length;
                            $("#log_placemark_counter").html(placemarks_counter);
                            status = true;
                        }
                    });

                    if(!status) parserGeoxml.parse(link);

                });

                $("#log_legenda_counter").html(counter_kml);

            },
            error: function (result) {
                // alert('Silahkan Pilih Kategori Terlebih Dahulu!');
                   Layers.forEach(function(entry) {
                        if (entry.docs.length != 0) {
                            entry.hideDocument();
                        }
                    });
                   Layers = []; 
            }
        });

        $.ajax({
            url: '/api/daerah',
            type: "GET",
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            cache: true,
            success: function (json) {

                var koordinat = {lat: parseFloat(json.latitude), lng: parseFloat(json.longitude)};
                // console.log(koordinat);
                map.setCenter(koordinat);
                  map.setZoom(10);

            },
            error: function (result) {
                // alert('Silahkan Pilih Kategori Terlebih Dahulu!');
                console.log('daerah tidak ditemukan');
                map.setCenter(MyCoor);
                  map.setZoom(zooms);
            }
        });
        
        return false;
    });

    $('#peta-dasar').submit(function(){

        var data = "kategori_id=";

        var id = $("#peta-dasar input:checkbox:checked").map(function(){
          return $(this).val();
        }).get();

        for (var i = 0; i < id.length; i++) {
            data += id[i]+',';
        }
        data = data.slice(0,-1);

        placemarks_counter_dasar = 0;
        $("#log_placemark_dasar_counter").html(0);

        $.ajax({
            url: '/api/layer-dasar',
            type: "GET",
            data: data,
            contentType: "application/json; charset=utf-8",
            dataType: "json",
            cache: true,
            success: function (json) {


                if(LayersDasar.length > 0){   

                    LayersDasar.forEach(function(entry) {
                        if (entry.docs.length != 0) {
                            entry.hideDocument();
                        }
                    });
                   LayersDasar = []; 

                }

                $("#log_layer_link_dasar").empty();
                let counter_kml = 0;

                json.forEach(function(entry) {

                    let link = '{{ env("APP_URL") }}'+entry.kml;

                    counter_kml++;
                    
                    let status_data = entry.size > 3000000 ? "lebih" : "biasa";

                    $("#log_layer_link_dasar").append(
                        "<tr>"+
                            "<td class="+'"'+status_data+'"'+">"+link+" ("+entry.sizef+")"+"</td>"+
                        "</tr>"
                    );

                        LayersDasar[entry.id] = new geoXML3.parser({
                            map: map,
                            preserveViewport: true,
                            zIndex : 1,
                            afterParse: useTheDataDasar,
                            zoom : false
                        });

                    LayersDasar[entry.id].parse(entry.kml);
                });

                $("#log_dasar_counter").html(counter_kml);


            },
            error: function (result) {

                LayersDasar.forEach(function(entry) {
                    if (entry.docs.length != 0) {
                        entry.hideDocument();
                    }
                });
                LayersDasar = [];
            }
        });
        
        return false;
    });

    $('#btn-control').click(function(){
        $('#control-map').show("slow");
        $('#close-control').show("slow");
        $(this).hide("slow");
    });

    $('#close-control').click(function(){
        $('#control-map').hide("slow");
        $(this).hide("slow");
        $('#btn-control').show("slow");
    });

    $(function(){
        // $( "#data" ).submit();
        // $( "#peta-dasar" ).submit();
    });

    $( document ).ajaxStart(function() {

          $('body').loading({
              stoppable: true
            });

    });

    $( document ).ajaxStop(function() {

          $('body').loading('stop');

    });

</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZzzDr8qs1sU2cxVAk-ewxecN9dBpqirc&callback=initMap&libraries=geometry,places" async defer></script>
@endpush