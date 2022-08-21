@extends('adminlte::page')

@section('title', 'Pengaturan')

@section('content_header')
    <h1 class="m-0 text-dark">Pengaturan</h1>
@stop

@section('adminlte_css')
  
@endsection

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-1"></i>
                    Judul Website
                </h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                 <input name="judul" class="form-control" value="{{ $c->judul }}" />
              </div>
              <button id="simpan_judul" class="btn btn-primary btn-block">Simpan</button>
            </div>
        </div>
    </div>
    <div class="col-md-4">
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
                <video width="100%" height="100%" controls>
                    <source id="vid" src="{{ $c->video }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header ui-sortable-handle">
                <h3 class="card-title">
                    <i class="fas fa-cog mr-1"></i>
                    Menu
                </h3>
            </div>
            <div class="card-body row">
              <div class="col-md-6">
                <button id="simpan_menu" class="btn btn-primary btn-block mb-3">Simpan</button>
                <ul id="myEditor" class="sortableLists list-group"></ul>
              </div>
              <div class="col-md-6">
                <div class="card border-primary mb-3">
                    <div class="card-header bg-yellow text-white">Menu Editor</div>
                    <div class="card-body">
                      <form id="frmEdit" class="form-horizontal">
                        <div class="form-group">
                          <label for="text">Text</label>
                          <div class="input-group">
                            <input type="text" class="form-control item-menu" name="text" id="text" placeholder="Text">
                          </div>
                          <input type="hidden" name="icon" class="item-menu">
                        </div>
                        <div class="form-group">
                          <label for="href">URL</label>
                          <input type="text" class="form-control item-menu" id="href" name="href" placeholder="URL">
                        </div>
                        <div class="form-group">
                          <label for="target">Target</label>
                          <select name="target" id="target" class="form-control item-menu">
                            <option value="_self">Self</option>
                            <option value="_blank">Blank</option>
                            <option value="_top">Top</option>
                          </select>
                        </div>
                        <div class="form-group">
                          <label for="title">Tooltip</label>
                          <input type="text" name="title" class="form-control item-menu" id="title" placeholder="Tooltip">
                        </div>
                      </form>
                    </div>
                    <div class="card-footer">
                        <button type="button" id="btnUpdate" class="btn btn-primary" disabled><i class="fas fa-sync-alt"></i> Ubah</button>
                        <button type="button" id="btnAdd" class="btn btn-success"><i class="fas fa-plus"></i> Tambah</button>
                    </div>
                </div>
              </div>
            </div>
        </div>
    </div>
</div>
@stop


@section('adminlte_js')
<script src="/vendor/menu-editor/bootstrap-iconpicker/js/bootstrap-iconpicker.min.js"></script>
<script src="/vendor/menu-editor/jquery-menu-editor.min.js"></script>
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

      // sortable list options
      const sortableListOptions = {
          placeholderCss: {'background-color': "#cccccc"}
      };
      const editor = new MenuEditor('myEditor', { listOptions: sortableListOptions } );
      editor.setForm($('#frmEdit'));
      editor.setUpdateButton($('#btnUpdate'));

      let arrayJson = `{!! json_encode($c->menu) !!}`;
      try {
        arrayJson = JSON.parse(arrayJson)
      } catch (error) {
        console.log(error)
        arrayJson = []
      }
      console.log(arrayJson)
      editor.setData(arrayJson);

      //Calling the update method
      $("#btnUpdate").click(function(){
          editor.update();
      });
      // Calling the add method
      $('#btnAdd').click(function(){
          editor.add();
      });
      // Calling the add method
      $('#simpan_menu').click(function(){
        const json = {"menu":editor.getString()}
        updateConfig(json)
      });

      $("#simpan_judul").click(function(){
        const json = {"judul":$("input[name=judul]").val()}
        updateConfig(json)
      })

</script>
@endsection