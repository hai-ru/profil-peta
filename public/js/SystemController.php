<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class SystemController extends Controller
{
    public function upload_file(Request $request)
    {
        try {
            $path = $request->file_data->store("public/data");
            $path = \str_replace("public","/storage",$path);
            $url = getenv("APP_URL");
            return $url.$path;
        } catch (\Exception $e) {
            // throw $th;
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
            $d = \App\Models\Desa::create($data);
            $sektor = \App\Models\tematik::all();
            $input = [];
            foreach ($sektor as $key => $value) {
                $input[] = ["desa_id"=>$d->id,"tematik_id"=>$value->id];
            }
            \App\Models\DataPetaDesa::insert($input);
            return $d;
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

    public function basisdata_store(Request $request)
    {
        $data = $request->all();
        if($request->has("id") && $request->has("delete")){
            return \App\Models\basisdata::findorfail($request->id)
            ->delete();
        }
        if($request->has("id") && intval($request->id) !== 0){
            return \App\Models\basisdata::findorfail($request->id)
            ->update($data);
        }
        \App\Models\basisdata::create($data);
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
        $t = \App\Models\tematik::create($data);
        $desa = \App\Models\Desa::get();
        foreach($desa as $item){
            $s = [
                "tematik_id"=>$t->id,
                "desa_id"=>$item->id
            ];
            $d = \App\Models\DataPetaDesa::where($s)->first();
            if(empty($d)){
                \App\Models\DataPetaDesa::create($s);
            }
        }
        return $t;
    }

    public function sektor_list(Request $request)
    {
        $data = \App\Models\tematik::orderBy("id","desc");
        return datatables()->of($data)->toJson();
    }

    public function peta_list(Request $request)
    {
        $data = \App\Models\DataPetaDesa::orderBy("id","desc")
        ->with("desa","tematik","datapeta","desa.kecamatan","desa.kecamatan.kabupaten");
        if($request->has("kabupaten_id") && intval($request->kabupaten_id) !== 0){
            $data = $data->whereHas("desa.kecamatan",function($q) use($request){
                return $q->where("kabupaten_id",$request->kabupaten_id);
            });
            // DD($data->count());
        }
        if($request->has("kecamatan_id") && intval($request->kecamatan_id) !== 0){
            $data = $data->whereHas("desa.kecamatan",function($q) use($request){
                return $q->where("id",$request->kecamatan_id);
            });
        }
        if($request->has("desa_id") && intval($request->desa_id) !== 0){
            $data = $data->where("desa_id",$request->desa_id);
        }
        if($request->has("tematik_id") && intval($request->tematik_id) !== 0){
            $data = $data->where("tematik_id",$request->tematik_id);
        }
        // DD($data->count());
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

    public function test()
    {
        $desa = \App\Models\Desa::all();
        $data = [];
        foreach($desa as $item){
            $t = \App\Models\tematik::all();
            foreach($t as $i){
                $s = [
                    "tematik_id"=>$i->id,
                    "desa_id"=>$item->id
                ];
                $d = \App\Models\DataPetaDesa::where($s)->first();
                if(empty($d)){
                    $data[] = $s;
                }
            }
        }
        // DD($data);
        return \App\Models\DataPetaDesa::insert($data);
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
            // return $data;
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

    public function maps_data(Request $request)
    {
        $d = \App\Models\DataPetaDesa::query();
        if($request->has("kabupaten_id")){
            $d = $d->whereHas("desa.kecamatan",function($q) use($request){
                return $q->where("kabupaten_id",$request->kabupaten_id);
            });
        }
        if($request->has("kecamatan_id")){
            $d = $d->whereHas("desa",function($q) use($request){
                return $q->where("kecamatan_id",$request->kecamatan_id);
            });
        }
        if($request->has("desa_id")){
            $d = $d->where("desa_id",$request->desa_id);
        }
        return $d->get();
    }

}
