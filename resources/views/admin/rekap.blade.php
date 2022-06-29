@extends('adminlte::page')

@section('title', 'Data Rekap')

@section('content_header')
    <h1 class="m-0 text-dark">Rekapitulasi</h1>
@stop

@section('adminlte_css')
    <style>
        .form_input{
            display: flex;
            align-self: center;
            justify-content: center;
        }
       .form_group{flex: 1;}
       .filter_data{display: flex;align-items: center;margin:5px;flex-wrap: wrap;}
       .filter_data p {margin: 3px 10px;}
       .filter_data select {margin: 3px 10px; width: 150px;}
       div.input input.form-control {
        margin: 5px auto;
       }
       div.d-grid button {
           width: 100%;
       }
       #tabel_data{
           margin-top: 20px;
       }
    </style>
@endsection

@section('content')


    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Editor Data</div>
                <form id="form_store" class="filter_data">
                    <div class="card-body">
                        <p>Data Kelompok</p>
                            @csrf
                            <input type="hidden" name="id" value="0" />
                            <select required id="kab_id_editor">
                                <option value="">-- Pilih Kabupaten --</option>
                                @foreach ($kabupaten as $item)
                                    <option value="{{$item->id}}">{{$item->name}}</option>
                                @endforeach
                            </select>
                            <select required id="kec_id_editor">
                                <option value="">-- Pilih Kecamatan --</option>
                            </select>
                            <select name="desa_id" required id="desa_id_editor">
                                <option value="">-- Pilih Desa --</option>
                            </select>
                            <select class="sektor_data" name="tematik_id" required id="sektor_id_editor">
                                <option value="">-- Pilih Sektor --</option>
                            </select>
                        <p>Data Input</p>
                        <div class="input">
                            <input class="form-control" name="nama" placeholder="Nama Fasilitas" />
                            <input class="form-control" name="fungsi" placeholder="Fungsi Kawasan" />
                            <input class="form-control" name="kondisi" placeholder="Kondisi Fasilitas" />
                            <input class="form-control" name="akses" placeholder="Kondisi Akses" />
                            <div class="row mb-2">
                                <div class="col-md-6">
                                    <input class="form-control" name="x" placeholder="X" />
                                </div>
                                <div class="col-md-6">
                                    <input class="form-control" name="y" placeholder="Y" />
                                </div>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                {{-- <div class="card-header">Kabupaten/Kota</div> --}}
                <div class="card-body">
                    <button onclick="tambah()" class="btn btn-primary mb-4"><i class="fa fa-plus"></i> Tambah</button>
                    <form id="filter_form" class="filter_data">
                        <p>Filter Data : </p>
                        <select name="kabupaten_id" id="kab_id">
                            <option value="0">-- Semua Kabupaten --</option>
                            @foreach ($kabupaten as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                        <select name="kecamatan_id" id="kec_id">
                            <option value="0">-- Semua Kecamatan --</option>
                        </select>
                        <select name="desa_id" id="desa_id">
                            <option value="0">-- Semua Desa --</option>
                        </select>
                        <select name="tematik_id" class="sektor_data" id="sektor_id">
                            <option value="0">-- Semua Sektor --</option>
                        </select>
                        <button class="btn btn-primary btn-xs">Apply</button>
                    </form>
                    <div class="table-responsive mt-5">
                        <table class="table table-striped table-hover" id="tabel_data">
                            <thead>
                                <tr>
                                    <th>Sektor</th>
                                    <th>Nama Fasilitas</th>
                                    <th>Koordinat</th>
                                    <th>Fungsi Kawasan</th>
                                    <th>Kondisi Fasilitas</th>
                                    <th>Kondisi Akses</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop


@section('adminlte_js')
    
    <script>

        let main_url = '{{ route("list.rekap") }}'
        $("#kab_id").change(function(e){
            let data = {kabupaten_id:$(this).val()}
            let elm = $("#kec_id")
            $("#kec_id").val("").trigger("change")
            loadWilayah(data,elm)
        })
        $("#kec_id").change(function(e){
            let data = {kecamatan_id:$(this).val()}
            let elm = $("#desa_id")
            loadWilayah(data,elm)
        })

        $("#filter_form").submit(function(e){
            const data = getFormData($("#filter_form"));
            const params = new URLSearchParams(data).toString();
            const url = main_url+"?"+params;
            data_table.ajax.url(url).load();

            return false;
        });

        $("#kab_id_editor").change(function(e){
            let data = {kabupaten_id:$(this).val()}
            let elm = $("#kec_id_editor")
            elm.val("").trigger("change")
            loadWilayah(data,elm)
        })
        $("#kec_id_editor").change(function(e){
            let data = {kecamatan_id:$(this).val()}
            let elm = $("#desa_id_editor")
            loadWilayah(data,elm)
        })

        const loadWilayah = (data,elm) => {
            $.ajax({
                method:"GET",
                url:"/list/wilayah?front",
                data:data,
                beforeSend:function(){
                    elm.attr("disabled",true);
                },
                complete:function(){
                    elm.removeAttr("disabled");
                },
                success:function(res){
                    elm.find('option').not(':first').remove();
                    for(key in res){
                        const data = res[key]
                        let selected = ""
                        if(data.kabupaten_id !== undefined && tipe == "edit"){
                            selected = data.id == wilayah.kec_id ? "selected" : "";
                        }
                        if(data.kecamatan_id !== undefined && tipe == "edit"){
                            selected = data.id == wilayah.desa_id ? "selected" : "";
                        }
                        const string = `<option ${selected} value="${data.id}">${data.name}</option>`;
                        elm.append(string)
                    }
                    if(data.kabupaten_id !== undefined && tipe == "edit"){
                        $("#kec_id_editor").trigger("change");
                    }
                },
                error:function(e){
                    console.log(e)
                }
            })
        }

        const loadSektor = (elm) => {
            $.ajax({
                method:"GET",
                url:"{{ route('sektor.data') }}",
                beforeSend:function(){
                    elm.attr("disabled",true);
                },
                complete:function(){
                    elm.removeAttr("disabled");
                },
                success:function(res){
                    elm.find('option').not(':first').remove();
                    for(key in res){
                        const data = res[key]
                        elm.append(`<option value="${data.id}">${data.name}</option>`)
                    }
                },
                error:function(e){
                    console.log(e)
                }
            })
        }

        $(document).ready(function(e){
            loadSektor($("#sektor_id"));
            loadSektor($("#sektor_id_editor"));
        })

        let srcdata = [];

        const data_table = $("#tabel_data").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '{{ route("list.rekap") }}',
                "type": 'GET',
                "dataSrc": function ( json ) {
                    srcdata = json.data;
                    return json.data;
                },
            },
            "columns":[
                {
                    "data":"tematik.name",
                },
                {
                    "data":"nama",
                },
                {
                    "data":"x",
                    "render":function(data,meta,row){
                        return data+","+row.y
                    }
                },
                {
                    "data":"fungsi",
                },
                {
                    "data":"kondisi",
                },
                {
                    "data":"akses",
                },
                {
                    "data":"id_data",
                    "render":function(data,type,row,meta){
                        return `
                            <button onclick="editData(${data},${meta.row})" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
                            <button onclick="deleteData(${data},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                        `
                    }
                },
            ]
        })

        const deleteData = (id,elm) => {
            tipe = null;
            const data = {id:id,delete:1};
            submitData(data,$(elm),()=> data_table.draw(false))
        }

        let wilayah = {
            "kab_id":null,
            "kec_id":null,
            "desa_id":null
        }

        const form_dom = $("#form_store");
        let tipe = null;
        const editData = (id,row) => {
            const val = srcdata[row];
            if(val === undefined){
                return console.log(val);
            }
            tipe = "edit";
            form_dom.find("input[name=id]").val(id)
            form_dom.find("input[name=nama]").val(val.nama)
            form_dom.find("input[name=fungsi]").val(val.fungsi)
            form_dom.find("input[name=kondisi]").val(val.kondisi)
            form_dom.find("input[name=akses]").val(val.akses)
            form_dom.find("input[name=x]").val(val.x)
            form_dom.find("input[name=y]").val(val.y)
            form_dom.find("select[name=tematik_id]").val(val.tematik_id)

            wilayah.kab_id = val.desa.kecamatan.kabupaten.id;
            wilayah.kec_id = val.desa.kecamatan.id;
            wilayah.desa_id = val.desa_id;
            $("#kab_id_editor").val(wilayah.kab_id).trigger("change");
            console.log(wilayah)

            form_dom.find("input").first().focus();
        }

        const tambah = () => {
            tipe = null;
            form_dom.find("input").val("");
            form_dom.find("input[name=id]").val(0);
            form_dom.find("input").first().focus();
        }

        $("#form_store").validate({
            submitHandler:function(form){
                const data = getFormData($(form))
                console.log(data)
                const elm = $(form).find("button[type=submit]");
                submitData(data,elm,()=> data_table.draw(false))
            }
        })


        const submitData = (data,elm,reload) => {
            $.ajax({
                method:"POST",
                url:"{{ route('rekap.store') }}",
                data:data,
                beforeSend:function(){
                    elm.attr("disabled",true)
                },
                complete:function(){
                    elm.removeAttr("disabled")
                },
                success:function(res){
                    console.log(res)
                    reload()
                },
                error:function(error){
                    console.log(error)
                }
            })
        }

    </script>
@endsection