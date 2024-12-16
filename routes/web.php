<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemController;
use Illuminate\Http\Request;
use DB;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('web-gis');
})->name("/");

Route::get('/rekapitulasi', function () {
    $data["kabupaten"] = \App\Models\Kabupaten::all();
    return view('rekapitulasi',$data);
})->name("rekapitulasi");
Route::get('/basis-data', [SystemController::class,"basis_data"])->name("basis-data");
Route::get('/web-gis', function () {
    $data['pemetaanKategori'] = \App\Models\Pemetaan::get()->groupBy('kategori');
    return view('web-gis',$data);
})->name("web-gis");
Route::get('/esri', function () {return view('esri');})->name("esri");

Route::get('/kompas-ternak/kabupaten', function () {

    $data['tahun'] = DB::table('kompas_ternak_kabupaten')
    ->groupBy('tahun')
    ->get()
    ->pluck('tahun');

    $data['list_kota'] = DB::table('kabupaten_kotas')->get();

    return view('kompas_ternak_kab',$data);
})->name("kompas_ternak_kab");

Route::get('/kompas-ternak/kabupaten/service', function (Request $request) {
    $tahun = $request->tahun ?? "";
    $data = DB::table('kompas_ternak_kabupaten')
    ->select('*','kabupaten_kotas.geojson')
    ->where('tahun',$tahun)
    ->join('kabupaten_kotas', 'kabupaten_kotas.id', '=', 'kompas_ternak_kabupaten.kabupaten_id')
    ->get();
    return [
        'status'=>true,
        'data'=>$data
    ];
})->name("kompas_ternak_kab.service");

Auth::routes();

Route::get('laporan', function() {
    return view('laporan');
})->name('laporan');

Route::get('recap/{id}', function($id) {
    $p = \App\Models\Pemetaan::findorfail($id);
    $data["columns"] = $p->property;
    $data["id"] = intval($id);
    return view('tabulasi',$data);
})->name('tabulasi');

Route::get('/tabulasi-data', [App\Http\Controllers\SystemController::class, 'tabulasi_data'])->name('tabulasi.data');
Route::post('/pemetaan-data', [App\Http\Controllers\SystemController::class, 'pemetaan_service'])->name('pemetaan.service');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::group(['prefix' => 'filemanager', 'middleware' => ['web']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::group(["middleware"=>"auth","prefix"=>"admin"],function(){

    Route::get('pengaturan', function() {
        $data["c"] = \App\Models\config::first();
        return view('admin.config',$data);
    })->name('pengaturan');
    Route::post("pengaturan",[SystemController::class,"pengaturan_store"])->name("pengaturan.store");

    Route::get('filemanager', function() {
        return view('admin.filemanager');
    })->name('basisdata');

    Route::get('pemetaan', function() {
        return view('admin.pemetaan');
    })->name('pemetaan');
    Route::get('pemetaan/editor', function() {
        return view('admin.layer_editor');
    })->name('pemetaan.editor');

    Route::get("pemetaan/data",[SystemController::class,"pemetaan_data"])->name("pemetaan.data");
    Route::post("pemetaan",[SystemController::class,"pemetaan_store"])->name("pemetaan.store");
    Route::post("layer",[SystemController::class,"layer_store"])->name("layer.store");

    Route::get('wilayah', function() {
        return view('admin.wilayah');
    })->name('wilayah');

    Route::get('rekapitulasi', function() {
        $data["kabupaten"] = \App\Models\Kabupaten::all();
        return view('admin.rekap',$data);
    })->name('rekap');

    Route::post("rekapitulasi",[SystemController::class,"rekap_store"])->name("rekap.store");

    Route::get('sektor', function() {
        return view('admin.sektor');
    })->name('sektor');

    Route::post('sektor', [SystemController::class,"sektor_store"])->name('sektor.post');

    Route::get('basisdata/{type}', function($type) {
        switch ($type) {
            case 'spasial':
                $accept = ".zip,.kml";
                break;
            case 'video':
                $accept = "video/*";
                break;
            case 'galeri':
                $accept = "image/*";
                break;
                default:
                    $accept = "*";
                break;
        }
        return view('admin.basisdata',[
            "type"=>$type,
            "accept"=>$accept
        ]);
    })->name('basisdata');

    Route::post('basisdata', [SystemController::class,"basisdata_store"])->name('basisdata.post');

    Route::get('/peta', function() {
        $data["kabupaten"] = \App\Models\Kabupaten::all();
        return view('admin.peta',$data);
    })->name('peta');

    Route::get('/peta/{id}/editor', function($id) {
        return view('admin.peta-editor',["id"=>$id]);
    })->name('peta.editor');

    Route::get("/peta/{id}/data",[SystemController::class,"peta_data"])->name("peta.data");
    Route::post("/peta/editor",[SystemController::class,"peta_data_editor"])->name("peta.data.editor");

    Route::post("/upload",[SystemController::class,"upload_file"])->name("upload");

    Route::post('store/wilayah',[SystemController::class,"wilayah_store"])->name('store.wilayah');

    Route::get('data/sektor',[SystemController::class,"sektor_data"])->name('sektor.data');

    Route::get('list/basisdata',[SystemController::class,"basisdata_list"])->name('list.basisdata');
    Route::get('list/wilayah',[SystemController::class,"wilayah_list"])->name('list.wilayah');
    Route::get('list/sektor',[SystemController::class,"sektor_list"])->name('list.sektor');
    Route::get('list/peta',[SystemController::class,"peta_list"])->name('list.peta');
    Route::get('list/rekap',[SystemController::class,"rekap_list"])->name('list.rekap');
    Route::get('list/pemetaan',[SystemController::class,"pemetaan_list"])->name('list.pemetaan');

});

Route::get('list/wilayah',[SystemController::class,"wilayah_list"])->name('list.wilayah');

Route::get('list/rekap',[SystemController::class,"rekap_list"])->name('list.rekap.umum');
Route::get('data/sektor',[SystemController::class,"sektor_data"])->name('sektor.data.umum');

Route::get("cdn",[SystemController::class,"cdn"])->name("cdn");
Route::get("test",[SystemController::class,"test"]);

// Route::get("olah_kompas_ternak_kabupaten",[SystemController::class,"olah_kompas_ternak_kabupaten"]);
