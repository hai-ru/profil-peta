@extends('layouts.master')

@push('css')
    <style>
        .content_data{margin-top: 20px;}
        .nav_menu{
            border: 1px solid #d9d9d9;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid content_data">
        <div class="row">
            <div class="col-md-3">
                <ul class="nav nav-pills flex-column nav_menu">
                    <li class="nav-item">
                      <a class="nav-link {{ !Request::has("tipe") ? "active" : "" }}" href="{{ route('basis-data') }}">GALERI</a>
                    </li>
                    <li class="nav-item">
                      <a 
                      class="nav-link {{ Request::get("tipe") == "peta" ? "active" : "" }}"
                      href="{{ route('basis-data',['tipe'=>'peta']) }}"
                      >PETA (GAMBAR)</a>
                    </li>
                    <li class="nav-item">
                      <a 
                      class="nav-link {{ Request::get("tipe") == "spasial" ? "active" : "" }}"
                      href="{{ route('basis-data',['tipe'=>'spasial']) }}"
                      >DATA SPASIAL</a>
                    </li>
                    <li class="nav-item">
                      <a 
                      class="nav-link {{ Request::get("tipe") == "video" ? "active" : "" }}"
                      href="{{ route('basis-data',['tipe'=>'video']) }}"
                      >VIDEO</a>
                    </li>
                </ul>
            </div>
            <div class="col-md-9">
                <div class="row">
                  @if (Request::has("tipe") && Request::get("tipe") === "spasial")
                    <div class="col-md-12">
                      <table class="table table-striped table-hover" id="tabel_data">
                          <thead>
                              <tr>
                                  <th>Link</th>
                              </tr>
                          </thead>
                          <tbody>
                            @foreach ($data as $item)    
                              <tr>
                                <td><a href="{{$item->filepath}}">{{$item->filepath}}</a></td>
                              </tr>
                            @endforeach
                          </tbody>
                      </table>
                    </div>
                  @else
                    @foreach ($data as $item)    
                            @if (Request::get("tipe") === "video")
                              <div class="col-md-6">
                                  <div class="card my-2">
                                    <div class="card-body">
                                    <video width="480" height="240" controls>
                                      <source src="{{ $item->filepath }}" type="video/mp4">
                                      Your browser does not support the video tag.
                                    </video>
                                  </div>
                                </div>
                              </div>
                                @else
                                <div class="col-md-3">
                                  <div class="card my-2">
                                    <div class="card-body">
                                      <a href="{{ $item->filepath }}" target="_blank"><img class="img-fluid" src="{{ $item->filepath }}" /></a>
                                    </div>
                                  </div>
                                </div>
                            @endif
                          
                    @endforeach
                  @endif
                </div>
            </div>
        </div>
    </div>
@endsection