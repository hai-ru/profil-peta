@extends('layouts.master')

@section('content')
    <div class="featured-content">
        {{-- <div class="ratio ratio-16x9">
            <iframe src="https://www.youtube.com/embed/Edzk3YnBstE?rel=0&autoplay=1" title="YouTube video" allowfullscreen></iframe>
        </div> --}}
        <video width="480" height="240" controls>
            <source src="/infrastruktur sambas.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>
@endsection