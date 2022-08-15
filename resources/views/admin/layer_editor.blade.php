@extends('adminlte::page')

@section('title', 'Pemetaan Editor')

@section('content_header')
    <h1 class="m-0 text-dark">Pemetaan Editor</h1>
@stop

@section('adminlte_css')
    <style>
         #map {
            height: 70vh;
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


    <input accept=".shp, .zip" id="url" type="hidden" value="{{ getenv('APP_URL') }}" />

    <button id="simpan" class="btn btn-primary float-right"><i class="fa fa-save"></i> Simpan</button>
    <div>
        <a href="{{ route('pemetaan') }}" class="btn btn-primary"><i class="fa fa-arrow-left"></i> Kembali</a>
        <div class="row mt-3">
            <label class="col-sm-1">Judul Pemetaan :</label>
            <div class="col-sm-11">
                <select id="pemetaan_id" name="pemetaan_id" class="form-control">
                    <option>-- Pilih Judul Pemetaan --</option>
                    @foreach (\App\Models\Pemetaan::get() as $item)
                        <option value="{{$item->id}}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header ui-sortable-handle">
                    <h3 class="card-title">
                        <i class="fas fa-map mr-1"></i>
                        Map Visualisasi
                    </h3>
                    <div class="card-tools">
                        <button id="import" class="btn btn-primary">Import SHP</button>
                    </div>
                </div>
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
    <script src='https://unpkg.com/@turf/turf@6/turf.min.js'></script>
    
    <script>

        let map = null
        let layer_collecting = [];
        let columns = [];
        let layer_active;
        let infowindow;
        let master_data = null;
        let layer = null;
        const url = $("#url").val()

        const params = (new URL(document.location)).searchParams;
        const pemetaan_id = parseInt(params.get("pemetaan_id")) ?? 0;

        $(document).ready(()=>{
            if(pemetaan_id !== 0){
                $("#pemetaan_id").val(pemetaan_id);
                LoadData(pemetaan_id);
            }
        })

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

            infowindow = new google.maps.InfoWindow();

            // const drawingManager = new google.maps.drawing.DrawingManager({
            //     drawingControl: true,
            //     drawingControlOptions: {
            //     position: google.maps.ControlPosition.TOP_CENTER,
            //     drawingModes: [
            //         google.maps.drawing.OverlayType.MARKER,
            //         google.maps.drawing.OverlayType.CIRCLE,
            //         google.maps.drawing.OverlayType.POLYGON,
            //         google.maps.drawing.OverlayType.POLYLINE,
            //         google.maps.drawing.OverlayType.RECTANGLE,
            //     ],
            //     },
            //     markerOptions: {
            //         icon: "https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png",
            //     },
            //     circleOptions: {
            //         fillColor: "#ffff00",
            //         fillOpacity: 1,
            //         strokeWeight: 5,
            //         clickable: false,
            //         editable: true,
            //         zIndex: 1,
            //     },
            // });

            // drawingManager.setMap(map);

        }

        $("#pemetaan_id").change(function(e){
            const id = $(this).val();
            if(id == "") return null;
            LoadData(id)
        })

        $("#import").click(function(e){
            $("#file_data").click()
        })

        $("#test").click(function(e){
            // console.log(map.data.g.g)
        })

        $("#file_data").change(function(){
            let formData = new FormData();
            formData.append('file_data', $('#file_data')[0].files[0]);
            const filename = $('#file_data').val().replace(/C:\\fakepath\\/i, '')
            formData.append('file_name', filename);

            const btn = $("#import");
            const btn_text = btn.text()

            $.ajax({
                url : '{{ route("upload") }}',
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                beforeSend:function(){
                    btn.attr("disabled",true)
                    btn.html("Loading...")
                },
                complete:function(){
                    btn.removeAttr("disabled")
                    btn.html(btn_text)
                },
                success : function(data) {
                    $("#file_data").val("")
                    UnloadMap()
                    shp(data)
                    .then(function(geojson){
                        LoadMap(geojson)
                    })
                }
            });
        })

        const LoadMap = geojson => {
            try {
                            
                if(geojson.features.length > 0){
                    for(key in geojson.features[0].properties){
                        columns.push(key)
                    }
                }
                layer_collecting.push(geojson);
                layer_active = geojson;
                map.data.addGeoJson(geojson);
                map.data.addListener('click', function(event) {
                    const f = event.feature;
                    let contentString = "";
                    f.forEachProperty((item,key) => {
                        contentString += `<tr>
                            <td>${key}</td>
                            <td>:</td>
                            <td>${item}</td>
                        </tr>`
                    })
                    const str = `<table>${contentString}</table>`;
                    infowindow.setContent(str)
                    infowindow.setPosition(event.latLng)
                    infowindow.setOptions({pixelOffset: new google.maps.Size(0,-34)});
                    infowindow.open(map)
                })
                LoadAtribut()
                const center = turf.center(geojson);
                const{geometry} = center
                const cor ={
                    lat:geometry.coordinates[1],
                    lng:geometry.coordinates[0]
                }
                map.setCenter(cor)
                map.setZoom(10)
            } catch (error) {
                alert("Failed to load data...")
                console.log(error,geojson)
            }
        }

        const UnloadMap = () => {
            map.data.forEach(function(feature) {
                map.data.remove(feature);
            });
        }

        const LoadData = id => {
            const btn = $("#pemetaan_id");
            $.ajax({
                url : '{{ route("pemetaan.data") }}',
                type : 'GET',
                data:{"pemetaan_id":id},
                beforeSend:function(){
                    btn.attr("disabled",true)
                },
                complete:function(){
                    btn.removeAttr("disabled")
                },
                success : function(response) {
                    const {data} = response
                    UnloadMap()
                    LoadMap(data.geojson)
                },
                error:function(e){
                    console.log(e)
                }
            });
        }

        const LoadAtribut = () => {
            let column_str = "";
            columns.map((item,i)=>{
                column_str += `<th><input data-index="${i}" class="kolom" readonly id="kolom_${item}" name="${item}" value="${item}" /></th>`
            })

            let sub_field = "";
            const atrdata = map.data.forEach((item,index)=>{
                let column_val = "";
                let iteration = 0;
                item.forEachProperty( (val, str) => {
                    const tipe = columns[iteration] == undefined ? "text" : columns[iteration].tipe;
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
                })
                // }
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
            // LoadData();
        })

        $("#add-column").click(function(e){
            $("#tambah_kolom_form").modal("show");
        })

        $("#add_column_form").validate({
            submitHandler:function(form){
                const data = getFormData($(form))
                return false;
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

        $("#simpan").click(async function(e){
            let geojson = {};
            await map.data.toGeoJson((g)=>{
                geojson = g;
            })
            const input = {
                pemetaan_id:$("#pemetaan_id").val(),
                geojson:JSON.stringify(geojson),
                property:JSON.stringify(columns)
            }
            console.log(input)
            const btn = $("#simpan");
            const btn_text = btn.text();
            $.ajax({
                url : '{{ route("layer.store") }}',
                type : 'POST',
                data : input,
                beforeSend:function(){
                    btn.attr("disabled",true)
                    btn.html("Loading...")
                },
                complete:function(){
                    btn.removeAttr("disabled")
                    btn.html(btn_text)
                },
                success : function(data) {
                    console.log(input,data)
                    swal.fire("",data.message,data.status)
                },
                error:function(e){
                    console.log(e)
                }
            });
        })

    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCZzzDr8qs1sU2cxVAk-ewxecN9dBpqirc&callback=initMap&libraries=geometry,places,drawing" async defer></script>
@endsection