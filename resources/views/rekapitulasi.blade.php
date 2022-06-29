@extends('layouts.master')

@push('css')
    <link href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <style>
        .filter_data{
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }
        .filter_data p{
            margin: 5px 0px;
        }
        .filter_data select{
            margin: 5px 10px;
        }
        table thead tr {
            background: black;
            text-align: center;
        }
        table thead tr th {
            color: white;
        }
        table tbody tr td {
            text-align: center;
        }
    </style>
@endpush

@section('content')
    <div class="container">
        <form id="filter_form" class="filter_data mb-3">
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
            <button class="btn btn-primary btn-sm">Apply</button>
        </form>
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="tabel_data">
                <thead>
                    <tr>
                        <th>Sektor</th>
                        <th>Nama Fasilitas</th>
                        <th>Koordinat</th>
                        <th>Fungsi Kawasan</th>
                        <th>Kondisi Fasilitas</th>
                        <th>Kondisi Akses</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@push('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap5.min.js"></script>
    <script>
         $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        function getFormData($form){
            var unindexed_array = $form.serializeArray();
            var indexed_array = {};

            $.map(unindexed_array, function(n, i){
                indexed_array[n['name']] = n['value'];
            });

            return indexed_array;
        }
        const tipe = null;
        let main_url = '{{ route("list.rekap.umum") }}'
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
                url:"{{ route('sektor.data.umum') }}",
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

        let srcdata = [];

        const data_table = $("#tabel_data").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '{{ route("list.rekap.umum") }}',
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
                }
            ]
        })
    </script>
@endpush