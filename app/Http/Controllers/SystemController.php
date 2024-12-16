<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SystemController extends Controller
{
    public function upload_file(Request $request)
    {
        try {
            $path = $request->file_data->storeAs("public/data",$request->file_name);
            $path = \str_replace("public","/storage",$path);
            $url = getenv("APP_URL");
            return $url.$path;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function cdn(Request $request)
    {
        $cookie = $request->headers->get("cookie");
        $cookie = \explode("=",$cookie);
        if(!$cookie[0] == "XSRF-TOKEN") return ["status"=>false,"message"=>"invalid request"];
        return response()->file(storage_path('app/' . $request->file_path));
    }

    public function wilayah_list(Request $request)
    {
        $data = \App\Models\Kabupaten::orderBy("id","desc");
        if($request->has("kabupaten_id")){
            $data = \App\Models\Kecamatan::where("kabupaten_id",$request->kabupaten_id)
            ->with("Kabupaten")
            ->orderBy("id","desc");
        }
        if($request->has("kecamatan_id")){
            $data = \App\Models\Desa::where("kecamatan_id",$request->kecamatan_id)
            ->with("kecamatan")
            ->orderBy("id","desc");
        }
        if($request->has("front")) return $data->get();
        return datatables()->of($data)->toJson();
    }

    public function wilayah_store(Request $request)
    {
        $data = $request->all();
        if($request->has("id") && $request->has("delete")){
            if($request->has("kabupaten_id")){
                return \App\Models\Kecamatan::findorfail($request->id)->delete();
           }
           if($request->has("kecamatan_id")){
               return \App\Models\Desa::findorfail($request->id)->delete();
           }
           return \App\Models\Kabupaten::findorfail($request->id)->delete();
        }
        if($request->has("id") && intval($request->id) !== 0){
            if($request->has("kabupaten_id")){
                 return \App\Models\Kecamatan::findorfail($request->id)->update($data);
            }
            if($request->has("kecamatan_id")){
                return \App\Models\Desa::findorfail($request->id)->update($data);
            }
            return \App\Models\Kabupaten::findorfail($request->id)->update($data);
        }
        if($request->has("kabupaten_id")){
            return \App\Models\Kecamatan::create($data);
        }
        if($request->has("kecamatan_id")){
            return \App\Models\Desa::create($data);
        }
        return \App\Models\Kabupaten::create($data);
    }

    public function basisdata_list(Request $request)
    {
        $data = \App\Models\basisdata::orderBy("id","desc");
        $tipe = "galeri";
        if($request->has("tipe")){
            $tipe = $request->tipe;
        }
        $data = $data->where("tipe",$tipe);
        return datatables()->of($data)->toJson();
    }

    public function sektor_store(Request $request)
    {
        $data = $request->all();
        if($request->has("id") && $request->has("delete")){
            return \App\Models\tematik::findorfail($request->id)
            ->delete();
        }
        if($request->has("id") && intval($request->id) !== 0){
            return \App\Models\tematik::findorfail($request->id)
            ->update($data);
        }
        \App\Models\tematik::create($data);
    }

    public function sektor_list(Request $request)
    {
        $data = \App\Models\tematik::orderBy("id","desc");
        return datatables()->of($data)->toJson();
    }

    public function peta_list(Request $request)
    {
        $data = \App\Models\DataPetaDesa::orderBy("id","desc")
        ->with("desa","tematik","datapeta");
        return datatables()->of($data)
        ->toJson();
    }

    public function peta_data(Request $request,$id)
    {
        return \App\Models\DataPetaDesa::where("id",$id)
        ->with(
            "desa",
            "desa.kecamatan",
            "desa.kecamatan.kabupaten",
            "tematik",
            "datapeta"
        )
        ->firstorfail();
    }

    function randomColor()
    {
        return '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
    }

    public function test()
    {
        $val = DB::table('kabupaten_kotas')->get();
        foreach ($val as $key => $value) {
            $warna = $this->randomColor();
            DB::update(
                "UPDATE `kabupaten_kotas` SET `warna`= ? WHERE `id` = ?",
                [$warna,$value->id]
            );
        }
    }

    public function peta_data_editor(Request $request)
    {
        $d = \App\Models\DataPetaDesa::findorfail($request->id);
        if($request->has("delete")){
            $d->update(["data_peta_id"=>null]);
            return ["status"=>"success","message"=>"Data berhasil dihapus"];
        }
        $datapeta = \App\Models\DataPeta::create(["geojson"=>json_encode($request->layer)]);
        $d->update(["data_peta_id"=>$datapeta->id]);
        return ["status"=>"success","message"=>"Data berhasil ditambahkan"];
    }

    public function rekap_store(Request $request)
    {
        $data = $request->all();
        try {
            if(isset($request->id) && intVal($request->id) !== 0){
                $r = \App\Models\rekap::find($request->id);
                if($request->has("delete")){
                    $r->delete();
                    return ["status"=>"success","message"=>"Data berhasil dihapus"];
                }
                $r->update($data);
                return ["status"=>"success","message"=>"Data berhasil diubah"];
            }
            \App\Models\rekap::create($data);
            return ["status"=>"success","message"=>"Data berhasil ditambahkan"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage()];
        }
    }

    public function rekap_list(Request $request)
    {
        $rekap = \App\Models\rekap::orderBy("rekaps.id","desc")
        ->with("desa","desa.kecamatan","desa.kecamatan.kabupaten","tematik")
        ->select("rekaps.*","rekaps.id as id_data","tematik_id as tema_id");
        if($request->has("kabupaten_id") && intval($request->kabupaten_id) !== 0){
            $rekap = $rekap->whereHas("desa.kecamatan.kabupaten",function($q) use($request){
                return $q->where("id",$request->kabupaten_id);
            });
        }
        if($request->has("kecamatan_id") && intval($request->kecamatan_id) !== 0){
            $rekap = $rekap->whereHas("desa.kecamatan",function($q) use($request){
                return $q->where("id",$request->kecamatan_id);
            });
        }
        if($request->has("desa_id") && intval($request->desa_id) !== 0){
            $rekap = $rekap->where("rekaps.desa_id",$request->desa_id);
        }
        if($request->has("tematik_id") && intval($request->tematik_id) !== 0){
            $rekap = $rekap->where("rekaps.tematik_id",$request->tematik_id);
        }
        return datatables()->of($rekap)
        ->toJson();
    }

    public function sektor_data(Request $request)
    {
        return \App\Models\tematik::orderBy("id","desc")->get();
    }

    public function basis_data(Request $request)
    {
        $data = $request->all();
        $data["data"] = \App\Models\basisdata::query();
        if($request->has("tipe")){
            $data["data"] = $data["data"]->where("tipe",$request->tipe);
        } else {
            $data["data"] = $data["data"]->where("tipe","galeri");
        }
        $data["data"] = $data["data"]->get();
        return view('basis-data',$data);
    }

    public function pemetaan_list(Request $request)
    {
        $data = \App\Models\Pemetaan::query()
        ->select("id","name",'kategori')
        ->withCount("Feature");
        return datatables()->of($data)->toJson();
    }

    public function pemetaan_store(Request $request)
    {
        $data = $request->all();
        $model = \App\Models\Pemetaan::query();

        if(!$request->has("id")) 
        return abort(404);

        if($request->has("delete"))
        return $model->findorfail($data["id"])->delete();

        $data["id"] = intVal($data["id"]);
        if($data["id"] !== 0)
        return $model->findorfail($data["id"])->update($data);

        return $model->create($data);
    }

    public function layer_store(Request $request)
    {
        try {
            DB::beginTransaction();
                $p = \App\Models\Pemetaan::findorfail($request->pemetaan_id);
                $geojson = json_decode($request->geojson,true);
                $p->update([
                    "geojson"=>$geojson,
                    "property"=>json_decode($request->property,true)
                ]);
                $p->feature()->delete();
                foreach ($geojson["features"] as $key => $value) {
                    $p->feature()->create([
                        "feature"=>$value,
                        "property"=>$value["properties"],
                    ]);
                }
            DB::commit();
            return ["status"=>"success","message"=>"Data berhasil ditambahkan"];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage(),"line"=>$th->getLine()];
        }
    }

    public function pemetaan_data(Request $request)
    {
        try {
            $p = \App\Models\Pemetaan::findorfail($request->pemetaan_id);
            return ["status"=>true,"data"=>$p];
        } catch (\Throwable $th) {
            return ["status"=>false,"message"=>$th->getMessage(),"line"=>$th->getLine()];
        }
    }

    public function tabulasi_data(Request $request)
    {
        try {
            $p = \App\Models\Pemetaan::findorfail($request->pemetaan_id) ?? \App\Models\Pemetaan::first();
            return datatables()->of($p->feature()->select("id","property"))->make(true);
        } catch (\Throwable $th) {
            return ["status"=>true,"message"=>$th->getMessage()];
        }
    }

    public function pemetaan_service(Request $request)
    {
        try {
            $p = \App\Models\Pemetaan::query();

            $data = $p->whereIn("id",$request->collect ?? [])
            ->select("id","geojson","marker","name")
            ->get();
            return [
                "status"=>true,
                "data"=>$data
            ];
        } catch (\Throwable $th) {
            return ["status"=>false,"message"=>$th->getMessage(),"line"=>$th->getLine()];
        }
    }

    public function pengaturan_store(Request $request)
    {
        try {
            $data = $request->all();
            if(isset($data["menu"])) {
                $data["menu"] = json_decode($data["menu"],true);
            }
            \App\Models\config::first()->update($data);
            return ["status"=>"success","message"=>"Data berhasil disimpan."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage(),"line"=>$th->getLine()];
        }
    }

    public function olah_kompas_ternak_kabupaten(Request $request) {
        try {
            $list_kabupaten = DB::table('kompas_ternak_kabupaten')
            ->where('kabupaten_id',0)
            ->get();
            foreach ($list_kabupaten as $key => $value) {
                $p = json_decode( json_encode($value), true );
                $search = $p['COL 3'] ?? "";
                $val = DB::table('kabupaten_kotas')
                ->where('nama','like',"%$search%")
                ->first();
                if(!empty($val)){
                    DB::update(
                        "UPDATE `kompas_ternak_kabupaten` SET `kabupaten_id`= ? WHERE `id` = ?",
                        [$val->id,$value->id]
                    );
                }
            }
            return ["status"=>"success","message"=>"Data berhasil disimpan."];
        } catch (\Throwable $th) {
            return ["status"=>"error","message"=>$th->getMessage(),"line"=>$th->getLine()];
        }
    }

}
