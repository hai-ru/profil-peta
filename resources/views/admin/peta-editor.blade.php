@extends('adminlte::page')

@section('title', 'Peta Editor')

@section('content_header')
    <h1 class="m-0 text-dark">Peta Editor</h1>
@stop

@section('adminlte_css')
    <style>
         #map {
            height: 50vh;
            width: 100%;
            margin-top: 0px;
            position: relative;
        }
        #tabel_grup_data tr td{
            padding: 5px 10px;
        }
        div.panel{
            display: flex;
            align-items: center;
            border: 1px solid black;
            border-radius: 10px;
            flex-direction: column;
            padding: 15px 0px;
        }
        input:read-only {
            border: none;
            text-align: center;
            font-weight: bold;
            padding: 5px 0px;
        }
        input.kolom{
           width: 80%;
       }
    </style>
@endsection

@section('content')
<x-adminlte-modal id="tambah_kolom_form" title="Tambah Kolom" static-backdrop>
    <form id="add_column_form">
        <div class="form-group">
            <label>Nama Kolom</label>
            <input class="form-control" name="name" required />
        </div>
        <div class="form-group">
            <label>Tipe Kolom</label>
            <select class="form-control" name="tipe" required>
                <option value="text" >Teks</option>
                <option value="image" >Image</option>
                <option value="file" >File</option>
            </select>
        </div>
        <button class="btn btn-primary btn-block">Simpan</button>
    </form>
</x-adminlte-modal>

<x-adminlte-modal id="pengaturan_style" title="Pengaturan Style" static-backdrop>
    <form id="add_column_form">
        <p>Terdapat 3 section yaitu, pengaturan marker icon, polygon background color, polyline border width dan border color</p>
        <button class="btn btn-primary btn-block">Simpan</button>
    </form>
</x-adminlte-modal>


    <input accept=".shp, .zip" id="url" type="hidden" value="{{ getenv('APP_URL') }}" />

    <button id="simpan" class="btn btn-primary float-right"><i class="fa fa-save"></i> Simpan</button>
    <a href="{{ route('peta') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Kembali</a>


    <div class="row mt-3">
        <div class="col-md-4">

            <div class="card">
                <div class="card-header">Data Group</div>
                <div class="card-body">
                    <table id="tabel_grup_data">
                        <tr>
                            <td>Kab/Kota</td>
                            <td>:</td>
                            <td><span id="kab_kota"></span></td>
                        </tr>
                        <tr>
                            <td>Kecamatan</td>
                            <td>:</td>
                            <td><span id="kecamatan"></span></td>
                        </tr>
                        <tr>
                            <td>Desa</td>
                            <td>:</td>
                            <td><span id="desa"></span></td>
                        </tr>
                        <tr>
                            <td>Sektor</td>
                            <td>:</td>
                            <td><span id="sektor"></span></td>
                        </tr>
                    </table>
                </div>
            </div>

            <button id="import" class="btn btn-primary btn-block">Import SHP</button>
            <button id="style_setup" class="btn btn-primary btn-block">Pengaturan Style</button>

        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
			        <div id="map"></div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Data Attribute</div>
                <div class="card-body">
                    <div class="text-right mb-2">
                        <input id="file_data" type="file" name="file_data" style="display: none;" />
                        <button id="add-column" class="btn btn-primary">Tambah Kolom</button>
                        <button id="edit-column" class="btn btn-primary">Ubah Kolom</button>
                    </div>
                    <div id="list_layer" class="table-responsive">
                        <div class="panel">
                            <i class="fas fa-map fa-3x"></i>
                            <h4>Data Kosong</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('adminlte_js')

    <script src="https://unpkg.com/shpjs@latest/dist/shp.js"></script>

    <script>

        let map = null
        function initMap() {

            map = new google.maps.Map(document.getElementById('map'), {
                center: {lat: -0.10814501846297607, lng: 109.3182775878906},
                zoom: 8,
                mapTypeId: google.maps.MapTypeId.HYBRID,
                mapTypeControl: true,
                mapTypeControlOptions: {
                    style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position: google.maps.ControlPosition.TOP_LEFT
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

            const drawingManager = new google.maps.drawing.DrawingManager({
                drawingControl: true,
                drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [
                    google.maps.drawing.OverlayType.MARKER,
                    google.maps.drawing.OverlayType.CIRCLE,
                    google.maps.drawing.OverlayType.POLYGON,
                    google.maps.drawing.OverlayType.POLYLINE,
                    google.maps.drawing.OverlayType.RECTANGLE,
                ],
                },
                markerOptions: {
                    icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
                },
                circleOptions: {
                    fillColor: "#ffff00",
                    fillOpacity: 1,
                    strokeWeight: 5,
                    clickable: false,
                    editable: true,
                    zIndex: 1,
                },
            });

            drawingManager.setMap(map);

        }
        
        const url = $("#url").val()
        let layer = null;

        $("#import").click(function(e){
            $("#file_data").click()
        })

        $("#file_data").change(function(){
            let formData = new FormData();
            formData.append('file_data', $('#file_data')[0].files[0]);
            $.ajax({
                url : '{{ route("upload") }}',
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success : function(data) {
                    $("#file_data").val("")
                    for(key in layer){
                        UnloadMap(layer[key],key)
                    }
                    shp(data).then(function(geojson){
                        layer = geojson;
                        UnloadMap()
                        LoadMap()
                    })
                }
            });
        })

        const UnloadMap = () => {
            for(key in layer.feature){
                const val = layer.feature[key];
                val.setMap(null);
            }
        }
        let columns = [];
        const LoadMap = () => {
            // console.log(layer)
            if(layer !== null && layer.feature === undefined){    
                layer.feature = [];
                for(key in layer.features){
                    if(key == 0){
                        const first = layer.features[0];
                        let center = first.geometry.coordinates;
                        center = {
                            lat:parseFloat(center[1]),
                            lng:parseFloat(center[0])
                        };
                        // map.setCenter(center);
                        columns = [];
                        for(i in first.properties){
                            columns.push({
                                label:i,
                                tipe:"text"
                            });
                        }
                    }
                    const f = layer.features[key];
                    // console.log(f,key);
                    if(f.type != "Feature") alert("feature not found");
                    const coo = f.geometry.coordinates;
                    let d = null;
                    switch (f.geometry.type) {
                        case "Point":
                            d = renderMarker(coo,key,f)
                            break;
                    
                        default:
                            d = renderPolygon(coo)
                            break;
                    }
                    if(d !== null)
                    layer.feature.push(d);
                }
            } else {
                for(key in layer.feature){
                    if(key == 0){
                        let center = layer.feature[0].getPosition();
                        // map.setCenter(center);
                    }
                    const val = layer.feature[key]
                    val.setMap(map)
                }
            }
            LoadAtribut()
        }

        const renderPolygon = (coordinates) => {
            const polygon = new google.maps.Polyline({
                path: coordinates,
                geodesic: true,
                strokeColor: "#FF0000",
                strokeOpacity: 1.0,
                strokeWeight: 2,
            });

            polygon.setMap(map);
            return polygon;
        }

        const renderMarker = (coordinates,key,mark_data) => {
            const coo = { 
                lat: parseFloat(coordinates[1]), 
                lng: parseFloat(coordinates[0])
            };

            const marker = new google.maps.Marker({
                position: coo,
                draggable: true,
                animation: google.maps.Animation.DROP,
            });

            marker.addListener('dragend', (e) => {
                layer.features[key].geometry = [e.latLng.lng(),e.latLng.lat()]
            });
            
            let contentString = "<table>";
            for(key in mark_data.properties) {
                contentString += `<tr>
                    <td>${key}</td>
                    <td>:</td>
                    <td>${mark_data.properties[key]}</td>
                </tr>`
            }
            const infowindow = new google.maps.InfoWindow({
                content: contentString,
            });

            marker.addListener("click", () => {
                infowindow.open({
                    anchor: marker,
                    map,
                    shouldFocus: false,
                });
            });

            marker.setMap(map);

            return marker;

        }

        const handleEvent = (e,key,index) => {
            // console.log(e.latLng,key,index)
        }

        let master_data = null;
        const LoadData = () => {
            $.ajax({
                url : '{{ route("peta.data",$id) }}',
                type : 'GET',
                success : function(response) {
                    master_data = response
                    if(response.desa !== undefined)
                     $("#desa").text(response.desa.name);
                    if(response.desa !== undefined)
                     $("#kecamatan").text(response.desa.kecamatan.name);
                    if(response.desa !== undefined)
                     $("#kab_kota").text(response.desa.kecamatan.kabupaten.name);
                    if(response.tematik !== undefined)
                     $("#sektor").text(response.tematik.name);
                    if(response.datapeta !== null ){
                        const geojson = JSON.parse(response.datapeta.geojson)
                        layer = geojson;
                        LoadMap();
                    }
                },
                error:function(e){
                    console.log(e)
                }
            });
        }

        const LoadAtribut = () => {
            let column_str = "";
            columns.map((item,i)=>{
                column_str += `<th><input data-index="${i}" class="kolom" readonly id="kolom_${key}" name="${key}" value="${item.label}" /></th>`
            })

            let sub_field = "";
            const atrdata = layer.features.map((item,index)=>{
                let column_val = "";
                let iteration = 0;
                for(key in item.properties){
                    const val = item.properties[key]
                    const tipe = columns[iteration] == undefined ? "text" : columns[iteration].tipe;
                    // console.log(tipe,columns,iteration)
                    switch (tipe) {
                        case "file":
                            column_val += `
                                <td id="atr_${item}_${iteration}">
                                    <input type="file" onchange="changeData(${index},${iteration},this,'${tipe}')" />
                                    <input type="text" readonly name="${key}" value="${val}" />
                                </td>
                            `
                            break;
                        case "image":
                            column_val += `
                                <td id="atr_${item}_${iteration}">
                                    <input accept="image/*" type="file" onchange="changeData(${index},${iteration},this,'${tipe}')" />
                                    <input type="text" readonly name="${key}" value="${val}" />
                                    <img class="img_preview_atr" src="${val}" />
                                </td>
                            `
                            break;
                    
                        default:
                            column_val += `
                                <td id="atr_${item}_${iteration}"><input type="text" name="${key}" value="${val}" /></td>
                            `
                            break;
                    }
                    iteration++;
                }
                sub_field += `<tr>${column_val}</tr>`
            })

            const tabel_data = `
            <table class="table table-striped list_atribut">
                <thead>
                    <tr>
                        ${column_str}
                    </tr>
                </thead>
                <tbody>
                    ${sub_field}
                </tbody>
            </table>
            `
            $("#list_layer").html(tabel_data);
        }

        let active_edit = false;
        $("#edit-column").click(function(e){
            active_edit = !active_edit;
            const elm = $(document).find(".kolom");
            if(active_edit){
                elm.removeAttr("readonly")
                $(this).text("Kunci Kolom");
            } else {
                elm.attr("readonly",true)
                changeColumn();
                $(this).text("Ubah Kolom");
            }
        })

        const changeColumn = () => {
            const elm = $(document).find(".kolom");
            columns = [];
            elm.each(function(i, obj) {
                columns.push( {
                    label:$(obj).val(),
                    tipe:"text"
                } )
            });
        }

        $(function(){
            LoadData();
        })

        $("#add-column").click(function(e){
            $("#tambah_kolom_form").modal("show");
        })

        $("#style_setup").click(function(e){
            $("#pengaturan_style").modal("show");
        })

        $("#add_column_form").validate({
            submitHandler:function(form){
                const data = getFormData($(form))
                columns.push({
                    label:data.name,
                    tipe:data.tipe
                })
                for(key in layer.features){
                    let item = layer.features[key]
                    item.properties[data.name] = "";
                }
                LoadAtribut()
                $("#tambah_kolom_form").modal("hide")
            }
        })

        const changeData = (feature_index,properties_index,elm,tipe) => {
            let formData = new FormData();
            // console.log($(elm))
            formData.append('file_data', $(elm)[0].files[0]);
            $.ajax({
                url : '{{ route("upload") }}',
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success : function(data) {
                    $(elm).siblings("input[type=text]").val(data)
                    const {label,tipe} = columns[properties_index];
                    const res_data = {tipe:tipe,label:label,data:data};
                    layer.features[feature_index].properties[label] = res_data;
                    if(tipe == "image"){
                        $(elm).siblings("img").attr("src",data);
                    }
                    if(tipe !== "text"){
                        $(elm).val("")
                    }
                }
            });
        }

        $("#simpan").click(function(e){
            let input = master_data;
            let layer_mock = layer;
            delete layer_mock.feature;
            input.layer = layer_mock;
            console.log(input)
            $.ajax({
                url : '{{ route("peta.data.editor") }}',
                type : 'POST',
                data : input,
                success : function(data) {
                    // console.log(input,data)
                    swal.fire("",data.message,data.status)
                    if(data.status == "success"){
                        window.location = "{{ route('peta') }}";
                    }
                },
                error:function(e){
                    console.log(e)
                }
            });
        })

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZzzDr8qs1sU2cxVAk-ewxecN9dBpqirc&callback=initMap&libraries=geometry,places,drawing" async defer></script>
@endsection