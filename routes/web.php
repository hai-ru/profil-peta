<?php

use App\Http\Controllers\DataTernakController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SystemController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
// use DB;
use Illuminate\Support\Facades\DB;

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
    // return redirect()->route('web-gis');
    return redirect()->to('kompas-ternak/kabupaten');
})->name("/");

Route::get('/rekapitulasi', function () {
    $data["kabupaten"] = \App\Models\Kabupaten::all();
    return view('rekapitulasi', $data);
})->name("rekapitulasi");
Route::get('/basis-data', [SystemController::class, "basis_data"])->name("basis-data");
Route::get('/web-gis', function () {
    $data['pemetaanKategori'] = \App\Models\Pemetaan::get()->groupBy('kategori');
    return view('web-gis', $data);
})->name("web-gis");
Route::get('/esri', function () {
    return view('esri');
})->name("esri");

Route::get('/simpul-ternak', function () {

    $data['tahun'] = DB::table('simpul_ternak_kabupaten')
        ->groupBy('tahun')
        ->get()
        ->pluck('tahun');
    $data['komoditi'] = DB::table('simpul_ternak_kabupaten')
        ->groupBy('komoditi')
        ->get()
        ->pluck('komoditi');

    return view('simpul_ternak_kab', $data);
})->name("simpul_ternak_kab");

Route::get('/simpul-ternak/service', function (Request $request) {
    $tahun = $request->tahun ?? "";
    $komoditi = $request->komoditi ?? "";
    $data = DB::table('simpul_ternak_kabupaten')
        ->select('*', 'kabupaten_kotas.geojson')
        ->where([
            'tahun' => $tahun,
            'komoditi' => $komoditi,
        ])
        ->join('kabupaten_kotas', 'kabupaten_kotas.id', '=', 'simpul_ternak_kabupaten.kabupaten_id')
        ->get();
    return [
        'status' => true,
        'data' => $data
    ];
});

Route::get('/siap-mbg', function () {

    $data['kabupaten'] = DB::table('siap_mbg_ternak')
        ->groupBy('kabupaten')
        ->get()
        ->pluck('kabupaten');

    $data['list_skor'] = [
        [
            'val' => 1,
            'nama' => "TIDAK CUKUP",
            'warna' => "#f5ec42",
        ],
        [
            'val' => 2,
            'nama' => "CUKUP",
            'warna' => "#66f542",
        ],
        [
            'val' => 3,
            'nama' => "SANGAT CUKUP",
            'warna' => "#156b00",
        ],
    ];

    return view('siap_mbg', $data);
})->name("siap_mbg");

Route::get('/siap-mbg/service', function (Request $request) {
    $kabupaten = $request->kabupaten ?? "";
    $data = DB::table('siap_mbg_ternak')
        ->select('*', 'kecamatans.geojson')
        ->where([
            'kabupaten' => $kabupaten,
        ])
        ->join('kecamatans', 'kecamatans.id', '=', 'siap_mbg_ternak.kecamatan_id')
        ->get();
    return [
        'status' => true,
        'data' => $data
    ];
});

Route::get('/sri-sarah-lestari', function () {

    $data['kabupaten'] = DB::table('sri-ternak')
        ->groupBy('kabupaten')
        ->get()
        ->pluck('kabupaten');

    $data['list_skor'] = [
        [
            'val' => 0,
            'nama' => "Sangat Sesuai",
            'warna' => "#17ad00",
        ],
        [
            'val' => 1,
            'nama' => "Sangat Tidak Sesuai",
            'warna' => "#fc0303",
        ],
        [
            'val' => 2,
            'nama' => "Sedang",
            'warna' => "#ad8500",
        ],
        [
            'val' => 3,
            'nama' => "Sesuai",
            'warna' => "#618f31",
        ],
        [
            'val' => 4,
            'nama' => "Tidak Sesuai",
            'warna' => "#8f3131",
        ],
    ];

    return view('sri_sarah_lestari', $data);
})->name("sri-sarah-lestari");

Route::get('/sri-sarah-lestari/service', function (Request $request) {
    $kabupaten = $request->kabupaten ?? "";
    $jenis = null;
    $simulasi_rancangan_investasi_sri = null;
    if ($request->jenis) {
        $jenisSplit = explode("/", $request->jenis);
        $jenis = $jenisSplit[0];
        $simulasi_rancangan_investasi_sri = $jenisSplit[1];
    }

    if (!empty($simulasi_rancangan_investasi_sri)) {
        $simulasi_rancangan_investasi_sri = $simulasi_rancangan_investasi_sri . " ekor";
    }

    $data = DB::table('sri-ternak')
        ->select('*', 'kecamatans.geojson')
        ->where([
            'kabupaten' => $kabupaten,
            'jenis_investasi' => $jenis,
            'simulasi_rancangan_investasi_sri' => $simulasi_rancangan_investasi_sri
        ])
        ->join('kecamatans', 'kecamatans.id', '=', 'sri-ternak.kecamatan_id')
        ->get();
    return [
        'status' => true,
        'data' => $data
    ];
});


Route::get('/kompas-ternak/kabupaten', function () {

    $data['tahun'] = DB::table('kompas_ternak_kabupaten')
        ->groupBy('tahun')
        ->get()
        ->pluck('tahun');

    $data['list_kota'] = DB::table('kabupaten_kotas')->get();

    return view('kompas_ternak_kab', $data);
})->name("kompas_ternak_kab");

Route::get('/kompas-ternak/kabupaten/service', function (Request $request) {
    $tahun = $request->tahun ?? "";
    $data = DB::table('kompas_ternak_kabupaten')
        ->select('*', 'kabupaten_kotas.geojson')
        ->where('tahun', $tahun)
        ->join('kabupaten_kotas', 'kabupaten_kotas.id', '=', 'kompas_ternak_kabupaten.kabupaten_id')
        ->get();
    return [
        'status' => true,
        'data' => $data
    ];
})->name("kompas_ternak_kab.service");

Route::get('/formasi-ternak', function () {

    $data['tahun'] = DB::table('formasi_ternak')
        ->groupBy('tahun')
        ->get()
        ->pluck('tahun');

    $data['list_kota'] = DB::table('kabupaten_kotas')->get();

    return view('formasi_ternak', $data);
})->name("formasi_ternak");

Route::get('/formasi-ternak/service', function (Request $request) {
    $tahun = $request->tahun ?? "";
    $data = DB::table('formasi_ternak')
        ->select('*', 'kabupaten_kotas.geojson')
        ->where('tahun', $tahun)
        ->join('kabupaten_kotas', 'kabupaten_kotas.id', '=', 'formasi_ternak.kabupaten_id')
        ->get();
    return [
        'status' => true,
        'data' => $data
    ];
})->name("formasi_ternak.service");

Route::get('/potret-ternak', function (Request $request) {

    $data['kab_kota'] = DB::table('potret_pakan_marker')
        ->groupBy('Kabupaten/Kota')
        ->get()
        ->pluck('Kabupaten/Kota');

    return view('potret_ternak', $data);
})->name("potret_ternak");

Route::get('/potret-ternak/service', function (Request $request) {

    $data = DB::table('potret_pakan_marker');

    if ($request->kab_kota) {
        $data = $data->where('Kabupaten/Kota', $request->kab_kota);
    }

    $data = $data->get();

    return [
        'status' => true,
        'data' => $data
    ];
});

Route::get('/kompas-ternak/kecamatan', function (Request $request) {

    $data['tahun'] = DB::table('kompas_ternak_kecamatan')
        ->groupBy('tahun')
        ->get()
        ->pluck('tahun');

    $data['kabupaten'] = DB::table('kompas_ternak_kecamatan')
        ->groupBy('kabupaten')
        ->get()
        ->pluck('kabupaten');

    $data['list_skor'] = [
        [
            'val' => 1,
            'nama' => "SKORING 1",
            'warna' => "#f54242",
        ],
        [
            'val' => 2,
            'nama' => "SKORING 2",
            'warna' => "#4287f5",
        ],
        [
            'val' => 3,
            'nama' => "SKORING 3",
            'warna' => "#42f55a",
        ],
    ];

    return view('kompas_ternak_kec', $data);
})->name("kompas_ternak_kecamatan");

Route::get('/kompas-ternak/kecamatan/service', function (Request $request) {

    $tahun = $request->tahun ?? "";
    $kabupaten = $request->kabupaten ?? "";
    $data = DB::table('kompas_ternak_kecamatan')
        ->select('*', 'kecamatans.geojson')
        ->where([
            'tahun' => $tahun,
            'kabupaten' => $kabupaten,
        ])
        ->join('kecamatans', 'kecamatans.id', '=', 'kompas_ternak_kecamatan.kecamatan_id')
        ->get();

    return [
        'status' => true,
        'data' => $data
    ];
})->name("kompas_ternak_kecamatan.service");

Auth::routes();

Route::get('laporan', function () {
    return view('laporan');
})->name('laporan');

Route::get('recap/{id}', function ($id) {
    $p = \App\Models\Pemetaan::findorfail($id);
    $data["columns"] = $p->property;
    $data["id"] = intval($id);
    return view('tabulasi', $data);
})->name('tabulasi');

Route::get('/tabulasi-data', [App\Http\Controllers\SystemController::class, 'tabulasi_data'])->name('tabulasi.data');
Route::post('/pemetaan-data', [App\Http\Controllers\SystemController::class, 'pemetaan_service'])->name('pemetaan.service');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Auth::routes();

Route::group(['prefix' => 'filemanager', 'middleware' => ['web']], function () {
    \UniSharp\LaravelFilemanager\Lfm::routes();
});

Route::group(["middleware" => "auth", "prefix" => "admin"], function () {

    Route::get('pengaturan', function () {
        $data["c"] = \App\Models\config::first();
        return view('admin.config', $data);
    })->name('pengaturan');
    Route::post("pengaturan", [SystemController::class, "pengaturan_store"])->name("pengaturan.store");

    Route::get('filemanager', function () {
        return view('admin.filemanager');
    })->name('basisdata');

    Route::get('pemetaan', function () {
        return view('admin.pemetaan');
    })->name('pemetaan');
    Route::get('pemetaan/editor', function () {
        return view('admin.layer_editor');
    })->name('pemetaan.editor');
    Route::get('pemetaan/siap-mbg', function () {
        return view('admin.siap_mbg');
    })->name('pemetaan.siap_mbg');
    Route::get('pemetaan/sri-sarah-lestari', function () {
        return view('admin.sri_sarah_lestari');
    })->name('pemetaan.sri_sarah_lestari');

    Route::get('pemetaan/kompas-ternak', function () {
        return view('admin.kompas_ternak');
    })->name('pemetaan.kompas_ternak');
    Route::get('pemetaan/kompas-ternak/kab_kota', function () {
        return view('admin.kompas_ternak_kab_kota');
    })->name('pemetaan.kompas_ternak_kab_kota');

    Route::get('pemetaan/formasi-ternak', function () {
        return view('admin.formasi_ternak');
    })->name('pemetaan.formasi_ternak');
    Route::get('pemetaan/simpul-ternak', function () {
        return view('admin.simpul_ternak');
    })->name('pemetaan.simpul_ternak');
    Route::get('pemetaan/potret-ternak', function () {
        return view('admin.potret_ternak');
    })->name('pemetaan.potret_ternak');

    Route::post('/upload-sri', [DataTernakController::class, 'upload_sri'])->name('upload.sri');
    Route::post('/upload-formasi-ternak', [DataTernakController::class, 'upload_formasi_ternak'])->name('upload.formasi_ternak');
    Route::post('/upload-simpul-ternak', [DataTernakController::class, 'upload_simpul_ternak'])->name('upload.simpul_ternak');
    Route::post('/upload-kompas-ternak', [DataTernakController::class, 'upload_kompas_ternak'])->name('upload.kompas_ternak');
    Route::post('/upload-kompas-ternak/kabkota', [DataTernakController::class, 'upload_kompas_ternak_kabkota'])->name('upload_kompas_ternak_kabkota');
    Route::post('/upload-potret-ternak', [DataTernakController::class, 'upload_potret_ternak'])->name('upload.potret_ternak');
    Route::post('/upload-siap-mbg', [DataTernakController::class, 'upload_siap_mbg'])->name('upload.siap_mbg');
    Route::get("pemetaan/data", [SystemController::class, "pemetaan_data"])->name("pemetaan.data");
    Route::post("pemetaan", [SystemController::class, "pemetaan_store"])->name("pemetaan.store");
    Route::post("layer", [SystemController::class, "layer_store"])->name("layer.store");

    Route::get('wilayah', function () {
        return view('admin.wilayah');
    })->name('wilayah');

    Route::get('rekapitulasi', function () {
        $data["kabupaten"] = \App\Models\Kabupaten::all();
        return view('admin.rekap', $data);
    })->name('rekap');

    Route::post("rekapitulasi", [SystemController::class, "rekap_store"])->name("rekap.store");

    Route::get('sektor', function () {
        return view('admin.sektor');
    })->name('sektor');

    Route::post('sektor', [SystemController::class, "sektor_store"])->name('sektor.post');

    Route::get('basisdata/{type}', function ($type) {
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
        return view('admin.basisdata', [
            "type" => $type,
            "accept" => $accept
        ]);
    })->name('basisdata');

    Route::post('basisdata', [SystemController::class, "basisdata_store"])->name('basisdata.post');

    Route::get('/peta', function () {
        $data["kabupaten"] = \App\Models\Kabupaten::all();
        return view('admin.peta', $data);
    })->name('peta');

    Route::get('/peta/{id}/editor', function ($id) {
        return view('admin.peta-editor', ["id" => $id]);
    })->name('peta.editor');

    Route::get("/peta/{id}/data", [SystemController::class, "peta_data"])->name("peta.data");
    Route::post("/peta/editor", [SystemController::class, "peta_data_editor"])->name("peta.data.editor");

    Route::post("/upload", [SystemController::class, "upload_file"])->name("upload");

    Route::post('store/wilayah', [SystemController::class, "wilayah_store"])->name('store.wilayah');

    Route::get('data/sektor', [SystemController::class, "sektor_data"])->name('sektor.data');

    Route::get('list/basisdata', [SystemController::class, "basisdata_list"])->name('list.basisdata');
    Route::get('list/wilayah', [SystemController::class, "wilayah_list"])->name('list.wilayah');
    Route::get('list/sektor', [SystemController::class, "sektor_list"])->name('list.sektor');
    Route::get('list/peta', [SystemController::class, "peta_list"])->name('list.peta');
    Route::get('list/rekap', [SystemController::class, "rekap_list"])->name('list.rekap');
    Route::get('list/pemetaan', [SystemController::class, "pemetaan_list"])->name('list.pemetaan');
});

Route::get('list/wilayah', [SystemController::class, "wilayah_list"])->name('list.wilayah');

Route::get('list/rekap', [SystemController::class, "rekap_list"])->name('list.rekap.umum');
Route::get('data/sektor', [SystemController::class, "sektor_data"])->name('sektor.data.umum');

Route::get("cdn", [SystemController::class, "cdn"])->name("cdn");
Route::get("test", [SystemController::class, "test"]);

// Route::get("olah_kompas_ternak_kabupaten",[SystemController::class,"olah_kompas_ternak_kabupaten"]);
