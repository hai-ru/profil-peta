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
            $header = fgetcsv($file);

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

            if ($header !== $expectedHeaders) {
                return response()->json(['error' => 'Invalid CSV headers'], 422);
            }

            while (($row = fgetcsv($file)) !== FALSE) {
                $data = array_combine($header, $row);

                // insert to table
                DB::table('sri_ternak')->insert([
                    'kabupaten' => $data['kabupaten'],
                    'kecamatan' => $data['kecamatan'],
                    'tingkat_kesesuaian_lahan' => $data['tingkat_kesesuaian_lahan'],
                    'luas_lahan' => $data['luas_lahan'],
                    'daya_tampung_ternak' => $data['daya_tampung_ternak'],
                    'nama_unit' => $data['nama_unit'],
                    'jenis_investasi' => $data['jenis_investasi'],
                    'simulasi_rancangan_investasi_sri' => $data['simulasi_rancangan_investasi_sri'],
                    'discount_factor' => $data['discount_factor'],
                    'irr' => $data['irr'],
                    'npv' => $data['npv'],
                    'net_bc' => $data['net_bc'],
                    'pbp' => $data['pbp'],
                    'kecamatan_id' => '-'
                ]);
            }

            fclose($file);

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
            $header = fgetcsv($file);

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
            if ($header !== $expectedHeaders) {
                return response()->json(['error' => 'Invalid CSV headers'], 422);
            }
            // Process row
            while (($row = fgetcsv($file)) !== FALSE) {
                $data = array_combine($header, $row);
                DB::table('formasi_ternak')->updateOrInsert(
                    ['tahun' => $request->input('tahun')],
                    [
                        'provinsi' => $data['provinsi'],
                        'kabupaten' => $data['kabupaten'],
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
                        'tahun' => $data['tahun'],
                        'kabupaten_id' => '-'
                    ]
                );
            }
            fclose($file);
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
            $header = fgetcsv($file);

            $expectedHeaders = ['provinsi', 'kabupaten', 'jumlah_penduduk', 'status_neraca', 'produksi', 'konsumsi', 'neraca', 'komoditi'];

            if ($header !== $expectedHeaders) {
                return response()->json(['error' => 'Invalid CSV headers'], 422);
            }

            // Process  row
            while (($row = fgetcsv($file)) !== FALSE) {
                $data = array_combine($header, $row);

                //  updateOrInsert 
                DB::table('simpul_ternak')->updateOrInsert(
                    ['tahun' => $request->input('tahun')],
                    [
                        'provinsi' => $data['provinsi'],
                        'kabupaten' => $data['kabupaten'],
                        'jumlah_penduduk' => $data['jumlah_penduduk'],
                        'status_neraca' => $data['status_neraca'],
                        'produksi' => $data['produksi'],
                        'konsumsi' => $data['konsumsi'],
                        'neraca' => $data['neraca'],
                        'komoditi' => $data['komoditi'],
                        'tahun' => $data['tahun'],
                        'kabupaten_id' => '-'
                    ]
                );
            }

            fclose($file);

            return response()->json(['success' => 'Data imported successfully']);
        }

        return response()->json(['error' => 'File upload failed'], 500);
    }
}
