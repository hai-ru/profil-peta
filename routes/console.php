<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
// use DB;
use Illuminate\Support\Facades\DB;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('add_kecamatan_id', function () {
    $ternak = DB::table('sri-ternak')->whereNull('kecamatan_id')->get();
    echo "start";
    foreach ($ternak as $key => $value) {
        $search = $value->kecamatan ?? "";
        $val = DB::table('kecamatans')
        ->where('nama','like',"%$search%")
        ->first();
        if(!empty($val)){
            DB::update(
                "UPDATE `sri-ternak` SET `kecamatan_id`= ? WHERE `id` = ?",
                [$val->id,$value->id]
            );
        }
        echo "Data $key : $value->id";
    }
    echo "end";
})->purpose('add kecamatan_id');


Artisan::command('add_kecamatan_id_siap_mbg', function () {
    $ternak = DB::table('siap_mbg_ternak')->whereNull('kecamatan_id')->get();
    echo "start";
    foreach ($ternak as $key => $value) {
        $search = $value->kecamatan ?? "";
        $val = DB::table('kecamatans')
        ->where('nama','like',"%$search%")
        ->first();
        if(!empty($val)){
            DB::update(
                "UPDATE `siap_mbg_ternak` SET `kecamatan_id`= ? WHERE `id` = ?",
                [$val->id,$value->id]
            );
        }
        echo "MBG $key : $value->id";
    }
    echo "end";
})->purpose('add kecamatan_id');
