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
                    <div class="filter_data">
                        <p>Filter Data : </p>
                        <select required id="kab_id_editor">
                            <option value="">-- Pilih Kabupaten --</option>
                            @foreach ($kabupaten as $item)
                                <option value="{{$item->id}}">{{$item->name}}</option>
                            @endforeach
                        </select>
                        <select required id="kec_id_editor">
                            <option value="">-- Pilih Kecamatan --</option>
                        </select>
                        <select required id="desa_id_editor">
                            <option value="">-- Pilih Desa --</option>
                        </select>
                        <select required id="sektor_id_editor">
                            <option value="">-- Pilih Sektor --</option>
                        </select>
                    </div>
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