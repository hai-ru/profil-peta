@php($c = \App\Models\config::first())

@extends('layouts.master')

@section('content')
    <div class="featured-content">
        <video width="100%" height="100%" controls autoplay>
            <source src="{{ $c->video }}" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
@endsection