@extends('adminlte::page')

@section('title', 'Basisdata')

@section('content_header')
    <h1 class="m-0 text-dark">Basisdata - {{ucfirst($type)}}</h1>
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
       td img {
           max-width: 150px;
       }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Editor Data</div>
                <div class="card-body">
                    <form id="input">
                        <input type="hidden" name="id" value="0" />
                        <input type="hidden" name="tipe" value="{{$type}}" />
                        <p>Data</p>
                        <x-adminlte-input-file accept="{{$accept}}" name="file_location" id="file_path" igroup-size="sm" placeholder="Choose a file...">
                            <x-slot name="prependSlot">
                                <div class="input-group-text bg-lightblue">
                                    <i class="fas fa-upload"></i>
                                </div>
                            </x-slot>
                        </x-adminlte-input-file>
                        
                        <div class="mb-3">
                            <input class="form-control" name="filepath" type="url" placeholder="Link file" required />
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <button onclick="tambah()" class="btn btn-primary"><i class="fas fa-plus"></i> Tambah</button>
                    <div class="table-responsive mt-5">
                        <table class="table table-striped table-hover" id="tabel_data">
                            <thead>
                                <tr>
                                    <th>Data</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="tipe" name="tipe" value="{{$type}}" />
@stop


@section('adminlte_js')
    
    <script>

        const data_table = $("#tabel_data").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route('list.basisdata',['tipe'=>$type]) }}",
            "columns":[
                {
                    "data":"filepath",
                    "render":function(data,meta,row){
                        console.log($("#tipe").val())
                        switch ($("#tipe").val()) {
                            case "galeri":
                                return `<img src="${data}" />`;
                                break;
                        
                            default:
                                return `<a href="${data}">${data}</a>`;
                                break;
                        }
                    }
                },
                {
                    "data":"id",
                    "render":function(data,meta,row){
                        return `
                            <button onclick="editData(${row.id},'${row.filepath}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
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

        const editData = (id,filepath) => {
            $("#input input[name=id]").val(id);
            $("#input input[name=filepath]").val(filepath);
        }

        const tambah = () => {
            $("#input input").val("");
            $("#input input[name=id]").val(0);
            $("#input input[name=tipe]").val($("#tipe").val());
        }

        $("#file_path").change(function(e){
            let formData = new FormData();
            formData.append('file_data', $(this)[0].files[0]);
            $.ajax({
                url : '{{ route("upload") }}',
                type : 'POST',
                data : formData,
                processData: false,
                contentType: false,
                success : function(data) {
                    $("input[name=filepath]").val(data)
                }
            })
        })

        $("#input").validate({
            submitHandler:function(form){
                $.ajax({
                    url : '{{ route("basisdata.post") }}',
                    type : 'POST',
                    data : $(form).serialize(),
                    success : function(data) {
                        console.log(data)
                        $("input[name=filepath]").val("")
                        data_table.draw(false)
                    }
                })
            }
        })

        const submitData = (data,elm,reload) => {
            $.ajax({
                method:"POST",
                url:"{{ route('basisdata.post') }}",
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