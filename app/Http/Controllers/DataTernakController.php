<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DataTernakController extends Controller
{
    public function upload_sri(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        // Process  CSV 
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // read CSV 
            $file = fopen($path, 'r');
            $header = fgetcsv($file,0,';');

            $expectedHeaders = [
                'kabupaten',
                'kecamatan',
                'tingkat_kesesuaian_lahan',
                'luas_lahan',
                'daya_tampung_ternak',
                'nama_unit',
                'jenis_investasi',
                'simulasi_rancangan_investasi_sri',
                'discount_factor',
                'irr',
                'npv',
                'net_bc',
                'pbp'
            ];

            DB::beginTransaction();
            DB::table('sri-ternak')->truncate();
            while (($row = fgetcsv($file,5000,';')) !== FALSE) {

                $data = array_combine($header, $row);
                
                $kecamatan = $data['kecamatan'] ?? "";
                $kabupaten = $data['kabupaten'] ?? "";
                
                $kecamatan_data = \App\Models\Kecamatan::where('nama','like',"%$kecamatan%")
                ->whereHas('kabupaten',function($query) use ($kabupaten){
                    $query->where('nama','like',"%$kabupaten%");
                })->first();


                // insert to table
                DB::table('sri-ternak')->insert([
                    'kabupaten' => $data['kabupaten'] ?? "",
                    'kecamatan' => $data['kecamatan'] ?? "",
                    'tingkat_kesesuaian_lahan' => $data['tingkat_kesesuaian_lahan'] ?? '',
                    'luas_lahan' => $data['luas_lahan'] ?? '',
                    'daya_tampung_ternak' => $data['daya_tampung_ternak'] ?? '',
                    'nama_unit' => $data['nama_unit'] ?? '',
                    'jenis_investasi' => $data['jenis_investasi'] ?? '',
                    'simulasi_rancangan_investasi_sri' => $data['simulasi_rancangan_investasi_sri'] ?? '',
                    'discount_factor' => $data['discount_factor'] ?? '',
                    'irr' => $data['irr'] ?? '',
                    'npv' => $data['npv'] ?? '',
                    'net_bc' => $data['net_bc'] ?? '',
                    'pbp' => $data['pbp'] ?? '',
                    'kecamatan_id' => $kecamatan_data->id ?? null
                ]);
            }

            fclose($file);
            DB::commit();
            return response()->json(['success' => 'Data imported successfully']);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }

    public function upload_formasi_ternak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'tahun' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }
        // Process  CSV 
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            $file = fopen($path, 'r');
            $header = fgetcsv($file,0,';');

            $expectedHeaders = [
                'provinsi',
                'kabupaten',
                'bobot_hidup_siap_potong',
                'berat_karkas',
                'berat_daging_murni',
                'berat_daging_variasi',
                'berat_jeroan',
                'umur_afkir',
                'struktur_populasi_jantan_dewasa',
                'struktur_populasi_jantan_muda',
                'struktur_populasi_jantan_anak',
                'struktur_populasi_betina_dewasa',
                'struktur_populasi_betina_muda',
                'struktur_populasi_betina_anak',
                'tingkat_kelahiran',
                'tingkat_kebuntingan',
                'tingkat_kematian_pedet',
                'tingkat_kematian_total',
                'bcs',
                'calving_interval_ekstensif_intensif',
                'sc_jika_ib',
                'adg_average_daily_gain',
                'jumlah_pemberian_hijauan_pakan_ternak',
                'jenis_hijauan_pakan_ternak',
                'jumlah_pemberian_pakan_tambahan',
                'jenis_pakan_tambahan',
                'jumlah_pemberian_suplemen_vitamin_obat',
                'jenis_ternak',
                'tahun'
            ];
            // Process row
            $tahun = $request->input('tahun');
            DB::beginTransaction();
            while (($row = fgetcsv($file,5000,';')) !== FALSE) {
                $data = array_combine($header, $row);
                $kabupaten = $data['kabupaten'] ?? "";
                $kabupaten_data = \App\Models\Kabupaten::where(
                    'nama','like',"%$kabupaten%"
                )->first();
                DB::table('formasi_ternak')->updateOrInsert(
                    [
                        'tahun' => $tahun,
                        'provinsi' => $data['provinsi'] ?? "",
                        'kabupaten' => $kabupaten
                    ],
                    [
                        'provinsi' => $data['provinsi'] ?? "",
                        'kabupaten' => $kabupaten,
                        'bobot_hidup_siap_potong' => $data['bobot_hidup_siap_potong'],
                        'berat_karkas' => $data['berat_karkas'],
                        'berat_daging_murni' => $data['berat_daging_murni'],
                        'berat_daging_variasi' => $data['berat_daging_variasi'],
                        'berat_jeroan' => $data['berat_jeroan'],
                        'umur_afkir' => $data['umur_afkir'],
                        'struktur_populasi_jantan_dewasa' => $data['struktur_populasi_jantan_dewasa'],
                        'struktur_populasi_jantan_muda' => $data['struktur_populasi_jantan_muda'],
                        'struktur_populasi_jantan_anak' => $data['struktur_populasi_jantan_anak'],
                        'struktur_populasi_betina_dewasa' => $data['struktur_populasi_betina_dewasa'],
                        'struktur_populasi_betina_muda' => $data['struktur_populasi_betina_muda'],
                        'struktur_populasi_betina_anak' => $data['struktur_populasi_betina_anak'],
                        'tingkat_kelahiran' => $data['tingkat_kelahiran'],
                        'tingkat_kebuntingan' => $data['tingkat_kebuntingan'],
                        'tingkat_kematian_pedet' => $data['tingkat_kematian_pedet'],
                        'tingkat_kematian_total' => $data['tingkat_kematian_total'],
                        'bcs' => $data['bcs'],
                        'calving_interval_ekstensif_intensif' => $data['calving_interval_ekstensif_intensif'],
                        'sc_jika_ib' => $data['sc_jika_ib'],
                        'adg_average_daily_gain' => $data['adg_average_daily_gain'],
                        'jumlah_pemberian_hijauan_pakan_ternak' => $data['jumlah_pemberian_hijauan_pakan_ternak'],
                        'jenis_hijauan_pakan_ternak' => $data['jenis_hijauan_pakan_ternak'],
                        'jumlah_pemberian_pakan_tambahan' => $data['jumlah_pemberian_pakan_tambahan'],
                        'jenis_pakan_tambahan' => $data['jenis_pakan_tambahan'],
                        'jumlah_pemberian_suplemen_vitamin_obat' => $data['jumlah_pemberian_suplemen_vitamin_obat'],
                        'jenis_ternak' => $data['jenis_ternak'],
                        'tahun' => $tahun,
                        'kabupaten_id' => $kabupaten_data ?? null
                    ]
                );
            }
            fclose($file);
            DB::commit();
            return response()->json(['success' => 'Data imported successfully']);
        }
        return response()->json(['error' => 'File upload failed'], 500);
    }

    public function upload_simpul_ternak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'tahun' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        // Process  CSV 
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // read the CSV 
            $file = fopen($path, 'r');
            $header = fgetcsv($file,0,';');

            $expectedHeaders = ['tahun','provinsi', 'kabupaten', 'jumlah_penduduk', 'status_neraca', 'produksi', 'konsumsi', 'neraca', 'komoditi'];

            // if ($header !== $expectedHeaders) {
            //     return response()->json(['error' => 'Invalid CSV headers'], 422);
            // }

            // Process  row
            $tahun = $request->input('tahun');
            DB::beginTransaction();
            while (($row = fgetcsv($file,5000,';')) !== FALSE) {

                $data = array_combine($header, $row);


                $kabupaten = $data['kabupaten'] ?? "";
                $kabupaten_data = \App\Models\Kabupaten::where(
                    'nama','like',"%$kabupaten%"
                )->first();

                //  updateOrInsert 
                DB::table('simpul_ternak_kabupaten')->updateOrInsert(
                    [
                        'tahun' => $tahun,
                        'provinsi' => $data['provinsi'] ?? "",
                        'kabupaten' => $kabupaten
                    ],
                    [
                        'provinsi' => $data['provinsi'] ?? "",
                        'kabupaten' => $kabupaten,
                        'jumlah_penduduk' => $data['jumlah_penduduk'],
                        'status_neraca' => $data['status_neraca'],
                        'produksi' => $data['produksi'],
                        'konsumsi' => $data['konsumsi'],
                        'neraca' => $data['neraca'],
                        'komoditi' => $data['komoditi'],
                        'tahun' => $tahun,
                        'kabupaten_id' => $kabupaten_data->id
                    ]
                );
            }

            fclose($file);
            DB::commit();
            return response()->json(['success' => 'Data imported successfully']);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }

    public function upload_kompas_ternak(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048',
            'tahun' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 422);
        }

        // Process  CSV 
        if ($request->hasFile('csv_file')) {
            $file = $request->file('csv_file');
            $path = $file->getRealPath();

            // read the CSV 
            $file = fopen($path, 'r');
            $header = fgetcsv($file,0,';');

            $expectedHeaders = [
                'tahun',
                'Provinsi',
                'Kabupaten',
                'Perkiraan Penduduk',
                'Perkiraan Konsumsi Daging Ayam Ras',
                'Target Produksi Daging Ayam Ras',
                'Target Populasi Ayam Ras Pedaging',
                'Target Pemotongan Ayam Ras Pedaging',
                'Perkiraan Konsumsi Telur Ayam Ras',
                'Target Produksi Telur Ayam Ras',
                'Target Populasi Ayam Ras Petelur',
                'Perkiraan Konsumsi Daging Sapi/Kerbau',
                'Target Produksi Daging Sapi/ Kerbau',
                'Target Populasi Sapi/Kerbau',
                'Target Pemotongan Sapi/Kerbau',
                'Perkiraan Konsumsi Daging Kambing/ Domba',
                'Target Produksi Daging Kambing/ Domba',
                'Target Populasi Kambing/ Domba',
                'Target Pemotongan Kambing/ Domba',
                'Perkiraan Konsumsi Daging Babi',
                'Target Produksi Daging Babi',
                'Target Populasi Babi',
                'Target Pemotongan Babi',
                'Perkiraan Konsumsi Daging Ayam Lokal/Buras',
                'Target Produksi Daging Ayam Lokal/Buras',
                'Target Populasi Ayam Lokal/Buras',
                'Target Pemotongan Ayam Lokal/Buras',
                'Perkiraan Konsumsi Daging Bebek/ Itik',
                'Target Produksi Daging Bebek/ Itik',
                'Target Populasi Bebek/ Itik',
                'Target Pemotongan Bebek/ Itik',
            ];

            // dd(
            //     $header,
            //     count($header),
            //     $expectedHeaders,
            //     count($expectedHeaders)
            // );

            // Process  row
            $tahun = $request->input('tahun');
            DB::beginTransaction();
            while (($row = fgetcsv($file,5000,';')) !== FALSE) {

                $data = array_combine($header, $row);
                
                $kabupaten = $data['Kabupaten/Kota'] ?? "";
                $kabupaten_data = \App\Models\Kabupaten::where(
                    'nama','like',"%$kabupaten%"
                )->first();
                
                //  updateOrInsert 

                array_unshift($row,$kabupaten_data->id);

                $val = '';
                foreach ($row as $key => $value) {
                    $val .= "'".$value."'".',';
                }
                $val = rtrim($val,',');

                $check = DB::table('kompas_ternak_kabupaten')->where(
                    [
                        'tahun' => $tahun,
                        'Provinsi' => $data['Provinsi'] ?? "",
                        'Kabupaten' => $kabupaten
                    ])->first();

                if(!empty($check)){
                    DB::table('kompas_ternak_kabupaten')->where('id', $check->id)->delete();
                }
                $qry = 'INSERT INTO `kompas_ternak_kabupaten` (`kabupaten_id`, `tahun`, `Provinsi`, `Kabupaten`, `Perkiraan Penduduk`, `Perkiraan Konsumsi Daging Ayam Ras`, `Target Produksi Daging Ayam Ras`, `Target Populasi Ayam Ras Pedaging`, `Target Pemotongan Ayam Ras Pedaging`, `Perkiraan Konsumsi Telur Ayam Ras`, `Target Produksi Telur Ayam Ras`, `Target Populasi Ayam Ras Petelur`, `Perkiraan Konsumsi Daging Sapi/Kerbau`, `Target Produksi Daging Sapi/ Kerbau`, `Target Populasi Sapi/Kerbau`, `Target Pemotongan Sapi/Kerbau`, `Perkiraan Konsumsi Daging Kambing/ Domba`, `Target Produksi Daging Kambing/ Domba`, `Target Populasi Kambing/ Domba`, `Target Pemotongan Kambing/ Domba`, `Perkiraan Konsumsi Daging Babi`, `Target Produksi Daging Babi`, `Target Populasi Babi`, `Target Pemotongan Babi`, `Perkiraan Konsumsi Daging Ayam Lokal/Buras`, `Target Produksi Daging Ayam Lokal/Buras`, `Target Populasi Ayam Lokal/Buras`, `Target Pemotongan Ayam Lokal/Buras`, `Perkiraan Konsumsi Daging Bebek/ Itik`, `Target Produksi Daging Bebek/ Itik`, `Target Populasi Bebek/ Itik`, `Target Pemotongan Bebek/ Itik`) VALUES ('.$val.')';
                DB::statement($qry);
            }

            fclose($file);
            DB::commit();
            return response()->json(['success' => 'Data imported successfully']);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }


}
