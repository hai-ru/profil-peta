<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemController;

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

Route::get('/', function () {return view('home');})->name("/");
Route::get('/rekapitulasi', function () {
    $data["kabupaten"] = \App\Models\Kabupaten::all();
    return view('rekapitulasi',$data);
})->name("rekapitulasi");
Route::get('/basis-data', [SystemController::class,"basis_data"])->name("basis-data");
Route::get('/web-gis', function () {return view('web-gis');})->name("web-gis");
Route::get('/esri', function () {return view('esri');})->name("esri");
Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::group(["middleware"=>"auth","prefix"=>"admin"],function(){

    Route::get('pemetaan', function() {
        return view('admin.pemetaan');
    })->name('pemetaan');
    Route::get('pemetaan/editor', function() {
        return view('admin.layer_editor');
    })->name('pemetaan.editor');

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
