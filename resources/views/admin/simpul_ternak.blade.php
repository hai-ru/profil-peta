@extends('adminlte::page')

@section('title', 'Simpul Ternak')

@section('content_header')
<h1 class="m-0 text-dark">Simpul Ternak</h1>
@stop

@section('adminlte_css')
<style>
    .form_input {
        display: flex;
        align-self: center;
        justify-content: center;
    }

    .form_group {
        flex: 1;
    }

    .filter_data {
        display: flex;
        align-items: center;
        margin: 5px;
        flex-wrap: wrap;
    }

    .filter_data p {
        margin: 3px 10px;
    }

    .filter_data select {
        margin: 3px 10px;
        width: 150px;
    }

    div.input input.form-control {
        margin: 5px auto;
    }

    div.d-grid button {
        width: 100%;
    }

    #tabel_data {
        margin-top: 20px;
    }
</style>
@endsection

@section('content')
<div class="row">

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Editor Data :</div>
            <div class="card-body">
                <h5>Status : <span id="status">Tambah</span> data</h5>
                <form id="add_form">
                    @csrf
                    <input type="hidden" name="id" value="0" />
                    <div class="form-group">
                        <label>Name</label>
                        <input class="form-control" name="name" required />
                    </div>
                    <div class="d-grid">
                        <button class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <button onclick="tambah()" class="btn btn-primary"><i class="fa fa-plus"></i> Tambah</button>
                <div class="table-responsive mt-5">
                    <table class="table table-striped table-hover" id="tabel_data">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Total Data</th>
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
        "ajax": "{{ route('list.pemetaan') }}",
        "columns": [{
                "data": "name",
            },
            {
                "data": "kategori",
            },
            {
                "data": "feature_count",
            },
            {
                "data": "id",
                "render": function(data, meta, row) {
                    return `
                            <button onclick="editData(${row.id},'${row.name}')" class="btn btn-primary btn-xs"><i class="fa fa-pencil-alt"></i></button>
                            <button onclick="deleteData(${row.id},this)" class="btn btn-primary btn-xs"><i class="fa fa-trash"></i></button>
                            <a target="_blank" href="{{ route("pemetaan.editor") }}?pemetaan_id=${data}" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i></a>
                        `
                }
            },
        ]
    })

    const deleteData = (id, elm) => {
        const data = {
            id: id,
            delete: 1
        };
        submitData(data, $(elm), () => data_table.draw(false))
    }

    let editor = $("#add_form");

    const editData = (id, name) => {
        $("#status").text("Ubah")
        editor.find("input[name=id]").val(id);
        editor.find("input[name=name]").val(name);
        editor.find("input").focus()
    }

    const tambah = () => {
        $("#status").text("Tambah")
        editor.find("input").val("");
        editor.find("input[name=id]").val(0);
        editor.find("input").focus()
    }

    $("#add_form").validate({
        submitHandler: function(form) {
            let data = getFormData($(form))
            let elm = editor.find("button");
            submitData(data, elm, () => data_table.draw(false))
        }
    })

    const submitData = (data, elm, reload) => {
        $.ajax({
            method: "POST",
            url: "{{ route('pemetaan.store') }}",
            data: data,
            beforeSend: function() {
                elm.attr("disabled", true)
            },
            complete: function() {
                elm.removeAttr("disabled")
            },
            success: function(res) {
                console.log(res)
                reload()
            },
            error: function(error) {
                console.log(error)
            }
        })
    }
</script>
@endsection