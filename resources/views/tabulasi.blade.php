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
        <div class="row mt-5">
            <div class="col-md-3">
                <ul class="nav nav-pills flex-column nav_menu">
                    @foreach (\App\Models\Pemetaan::get() as $item)
                        <li class="nav-item">
                            <a href="{{ route("tabulasi",$item->id) }}" 
                                class="nav-link {{ $id === $item->id ? "active" : "" }}">{{$item->name}}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-md-9">
                <div class="table-responsive mt-5">
                    <table class="table table-striped table-hover" id="tabel_data">
                        <thead>
                            <tr>
                                @foreach ($columns as $item)
                                    <th>{{$item}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
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

        let srcdata = [];
        const data_table = $("#tabel_data").DataTable({
            "ordering": false,
            "searching": false,
            "processing": true,
            "serverSide": true,
            "ajax": {
                "url": '{{ route("tabulasi.data") }}?pemetaan_id={{$id}}',
                "type": 'GET',
                "dataSrc": function ( json ) {
                    srcdata = json.data;
                    return json.data;
                },
            },
            "columns":[
                @foreach ($columns as $item)
                {"data":"property.{{$item}}"},
                @endforeach
            ]
        })
    </script>
@endpush