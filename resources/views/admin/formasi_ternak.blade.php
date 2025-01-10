@extends('adminlte::page')

@section('title', 'Formasi Ternak')

@section('content_header')
<h1 class="m-0 text-dark">Formasi Ternak</h1>
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
        margin: 10px;
    }

    .d-grid button {
        width: 100%;
    }
</style>
@endsection

@section('content')
<div class="row justify-content-center">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Upload Data Formasi Ternak</div>
            <div class="card-body">
                <form action="{{route('upload.formasi_ternak')}}" id="upload_form" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="year">Pilih Tahun</label>
                        <select class="form-control" id="year" name="tahun" required>
                            @for ($year = 2010; $year <= 2099; $year++)
                                <option {{$year == date('Y') ? 'selected' : ''}} value="{{ $year }}">{{ $year }}</option>
                                @endfor
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="csv_file">Upload File CSV</label>
                        <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required />
                    </div>
                    <div class="form-group d-grid">
                        <button type="submit" class="btn btn-primary">Unggah</button>
                    </div>
                </form>
                <div class="form-group d-grid mt-3">
                    <a href="/template/formasi.csv" class="btn btn-secondary">Unduh Template CSV</a>
                </div>
            </div>
        </div>
    </div>

</div>
@stop

@section('adminlte_js')
<script>
    $(document).ready(function() {
        $('#upload_form').on('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajax({
                method: "POST",
                url: "{{ route('upload.formasi_ternak') }}",
                data: formData,
                contentType: false,
                processData: false,
                success: function(response) {
                    alert('Data berhasil diunggah');
                },
                error: function(error) {
                    alert('Terjadi kesalahan, silakan coba lagi');
                }
            });
        });
    });
</script>
@endsection