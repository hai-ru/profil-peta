@extends('adminlte::page')

@section('title', 'Data Wilayah')

@section('content_header')
    <h1 class="m-0 text-dark">Wilayah</h1>
@stop

@section('adminlte_css')
    <style>
        .form_input{
            display: flex;
            align-self: center;
            justify-content: center;
        }
       .form_group{flex: 1;}
    </style>
@endsection

@section('content')


    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Kabupaten/Kota</div>
                <div class="card-body">
                    <form id="add_kab" method="POST">
                        <input type="hidden" value="0" name="id" />
                        <div class="form_input">
                            <div class="form_group">
                                <input name="name" required type="text" class="form-control" placeholder="Nama Kabupaten/Kota">
                            </div>
                            <button type="submit" class="btn btn-outline-secondary" type="submit">Simpan</button>
                        </div>
                    </form>
                    <div class="text-right my-3">
                        <button onclick="tambahKab()" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                    <table id="kab" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Kecamatan</div>
                <div class="card-body">
                    <form id="add_kec" method="POST">
                        <input type="hidden" value="0" name="id" />
                        <div class="form_input">
                            <div class="form_group">
                                <input name="name" required type="text" class="form-control" placeholder="Nama Kabupaten/Kota">
                            </div>
                            <button type="submit" class="btn btn-outline-secondary" type="submit">Simpan</button>
                        </div>
                    </form>
                    <div class="text-right my-3">
                        <button onclick="tambahKec()" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                    <table id="kec" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kabupaten</th>
                                <th>Kecamatan</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">Desa</div>
                <div class="card-body">
                    <form id="add_desa" method="POST">
                        <input type="hidden" value="0" name="id" />
                        <div class="form_input">
                            <div class="form_group">
                                <input name="name" required type="text" class="form-control" placeholder="Nama Kabupaten/Kota">
                            </div>
                            <button type="submit" class="btn btn-outline-secondary" type="submit">Simpan</button>
                        </div>
                    </form>
                    <div class="text-right my-3">
                        <button onclick="tambahDesa()" class="btn btn-primary btn-xs"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                    <table id="desa" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Kecamatan</th>
                                <th>Nama</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop


@section('adminlte_js')
    <script>
        const kab_table = $("#kab").DataTable({
            "processing": true,
            "serverSide": true,
            "ajax": "{{ route("list.wilayah") }}",
            "columns":[
                {
                    "data":"name",
                },
                {
                    "data":"id",
                    "render":function(data,meta,row){
                        return `
                            <button onclick="editKab(${row.id},'${row.name}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
                            <button onclick="deleteKab(${row.id},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                            <button onclick="selectKab(${row.id})" class="btn btn-primary btn-xs"><i class="fa fa-arrow-right"></i></button>
                        `
                    }
                },
            ]
        })

        const deleteKab = (id,elm) => {
            const data = {id:id,delete:1};
            submitData(data,$(elm),()=> kab_table.draw(false))
        }

        let kab_id = null;
        const selectKab = (id) => {
            const url = '{{ route("list.wilayah") }}?kabupaten_id='+id
            kec_table.ajax.url(url).load();
            kab_id = id;
        }

        const editKab = (id,name) => {
            $("#add_kab input[name=id]").val(id);
            $("#add_kab input[name=name]").val(name);
        }

        const tambahKab = () => {
            $("#add_kab input").val("");
            $("#add_kab input[name=id]").val(0);
        }
        let kec_id = null;
        const selectKec = (id) => {
            const url = '{{ route("list.wilayah") }}?kecamatan_id='+id
            desa_table.ajax.url(url).load();
            kec_id = id;
        }

        const kec_table = $("#kec").DataTable({
            "processing": true,
            "serverSide": true,
            "ordering":false,
            "ajax": "{{ route("list.wilayah",["kabupaten_id"=>0]) }}",
            "columns":[
                {
                    "data":"kabupaten.name",
                },
                {
                    "data":"name",
                },
                {
                    "data":"id",
                    "render":function(data,meta,row){
                        return `
                            <button onclick="editKec(${row.id},'${row.name}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
                            <button onclick="deleteKec(${row.id},${row.kabupaten_id},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                            <button onclick="selectKec(${row.id})" class="btn btn-primary btn-xs"><i class="fa fa-arrow-right"></i></button>
                        `
                    }
                },
            ]
        })

        const deleteKec = (id,kab_id,elm) => {
            const data = {id:id,delete:1,kabupaten_id:kab_id};
            submitData(data,$(elm),()=> kec_table.draw(false))
        }

        const editKec = (id,name) => {
            $("#add_kec input[name=id]").val(id);
            $("#add_kec input[name=name]").val(name);
        }

        const tambahKec = () => {
            $("#add_kec input").val("");
            $("#add_kec input[name=id]").val(0);
        }

        const desa_table = $("#desa").DataTable({
            "processing": true,
            "serverSide": true,
            "ordering":false,
            "ajax": "{{ route("list.wilayah",["kecamatan_id"=>0]) }}",
            "columns":[
                {
                    "data":"kecamatan.name",
                },
                {
                    "data":"name",
                },
                {
                    "data":"id",
                    "render":function(data,meta,row){
                        return `
                            <button onclick="editDesa(${row.id},'${row.name}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
                            <button onclick="deleteDesa(${row.id},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                        `
                    }
                },
            ]
        })


        const deleteDesa = (id,elm) => {
            const data = {id:id,delete:1,kecamatan_id:kec_id};
            submitData(data,$(elm),()=> desa_table.draw(false))
        }

        const editDesa = (id,name) => {
            $("#add_desa input[name=id]").val(id);
            $("#add_desa input[name=name]").val(name);
        }

        const tambahDesa = () => {
            $("#add_desa input").val("");
            $("#add_desa input[name=id]").val(0);
        }

        $("#add_kab").validate({
            submitHandler:function(form){
                let data = getFormData($(form))
                console.log(data)
                const elm = $(form).find("button")
                submitData(data,elm,()=> kab_table.draw(false))
            }
        });
        $("#add_kec").validate({
            submitHandler:function(form){
                if(kab_id == null) swal.fire("","Silahkan pilih kabupaten dahulu","info")
                let data = getFormData($(form))
                data.kabupaten_id = kab_id;
                const elm = $(form).find("button")
                submitData(data,elm,()=> kec_table.draw(false))
            }
        });
        $("#add_desa").validate({
            submitHandler:function(form){
                if(kec_id == null) swal.fire("","Silahkan pilih kecamatan dahulu","info")
                let data = getFormData($(form))
                data.kecamatan_id = kec_id;
                const elm = $(form).find("button")
                submitData(data,elm,()=> desa_table.draw(false))
            }
        });

        const submitData = (data,elm,reload) => {
            $.ajax({
                method:"POST",
                url:"{{ route('store.wilayah') }}",
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