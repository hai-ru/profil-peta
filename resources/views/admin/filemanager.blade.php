@extends('adminlte::page')

@section('title', 'Filemanager')

@section('content_header')
    <h1 class="m-0 text-dark">Filemanager</h1>
@stop

@section('adminlte_css')
   <style>
    iframe{
        height: 70vh;
    }
   </style>
@endsection

@section('content')
    <iframe src="/filemanager" width="100%"></iframe>
@stop


@section('adminlte_js')
   
@endsection