@extends('layouts.master')

@push('css')
<style>
     iframe{
        height: 70vh;
    }
</style>
@endpush

@section('content')
<div class="featured-content">
    <iframe src="/filemanager" width="100%"></iframe>
</div>
@endsection