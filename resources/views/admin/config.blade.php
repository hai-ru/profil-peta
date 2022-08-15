@extends('adminlte::page')

@section('title', 'Pengaturan')

@section('content_header')
    <h1 class="m-0 text-dark">Pengaturan</h1>
@stop

@section('adminlte_css')
  
@endsection

@section('content')
<div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header ui-sortable-handle">
                    <h3 class="card-title">
                        <i class="fas fa-map mr-1"></i>
                        Video
                    </h3>
                </div>
                <div class="card-body">
			        <div class="form-group">
                        <label>Video URL</label>
                        <div class="input-group">
                            <span class="input-group-btn">
                                <a id="lfm" data-input="thumbnail" data-preview="holder" class="btn btn-primary">
                                <i class="fa fa-picture-o"></i> Choose
                                </a>
                            </span>
                            <input id="thumbnail" class="form-control" type="text" name="filepath" value="{{ $c->video }}">
                        </div>
                    </div>
                    <video width="100%" height="100%" controls autoplay>
                        <source id="vid" src="{{ $c->video }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        </div>
</div>
@stop


@section('adminlte_js')
<script src="/vendor/laravel-filemanager/js/stand-alone-button.js"></script>
<script>
     $('#lfm').filemanager('file');
     $("#thumbnail").change(function(e){
        const val = $(this).val()
        updateConfig({video:val})
        $("#vid").val(val)
     })

     const updateConfig = data => {
        $.ajax({
          url:"{{ route('pengaturan.store') }}"  ,
          type:"POST",
          data:data,
          beforeSend:function(){
            $("button").attr("disabled",true)
            $("input").attr("disabled",true)
          },
          complete:function(){
            $("button").removeAttr("disabled")
            $("input").removeAttr("disabled")
          },
          success:function(res){
            swal.fire('',res.message,res.status)
          },
          error:function(e){
            console.log(e)
          }
        })
     }
</script>
@endsection