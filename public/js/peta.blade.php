@extends('adminlte::page')

@section('title', 'Peta')

@section('content_header')
    <h1 class="m-0 text-dark">Peta</h1>
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
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
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
                                    <th>Desa</th>
                                    <th>Sektor</th>
                                    <th>Status</th>
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

        let main_url = '{{ route("list.peta") }}'
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

        const tipe = null;

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
        })

        $("#filter_form").submit(function(e){
            const data = getFormData($("#filter_form"));
            const params = new URLSearchParams(data).toString();
            const url = main_url+"?"+params;
            data_table.ajax.url(url).load();

            return false;
        });

        const data_table = $("#tabel_data").DataTable({
            "processing": true,
            "serverSide": true,
            "sorting":false,
            "ajax": "{{ route('list.peta') }}",
            "columns":[
                {
                    "data":"desa.name",
                },
                {
                    "data":"tematik.name",
                },
                {
                    "data":"data_peta_id",
                    "render":function(data,meta,row){
                        return data === null ? 
                        `<span class="badge bg-danger">Kosong</span>` : 
                        `<span class="badge bg-success">Tersedia</span>`;
                    },
                    "className":"text-center"
                },
                {
                    "data":"id",
                    "render":function(data,meta,row){
                        return `
                            <a href="/admin/peta/${data}/editor" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></a>
                            <button onclick="deleteData(${row.id},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                        `
                    }
                },
            ]
        })

        const deleteData = (id,elm) => {
            const data = {id:id,delete:1};
            submitData(data,$(elm),()=> data_table.draw(false))
        }

        const submitData = (data,elm,reload) => {
            $.ajax({
                method:"POST",
                url:"{{ route('peta.data.editor') }}",
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