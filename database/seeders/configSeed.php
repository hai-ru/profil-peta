<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class configSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $c = \App\Models\config::query();
        $check = $c->first();
        if(!empty($check)) {
            $menus = '[{"text":"Beranda","href":"\/","icon":"","target":"_self","title":""},{"text":"Data Laporan","href":"\/laporan","icon":"","target":"_self","title":""},{"text":"Rekapitulasi","href":"\/recap\/1","icon":"","target":"_self","title":""},{"text":"Web GIS","href":"\/web-gis","icon":"","target":"_self","title":""}]';
            return $check->update([
                "menu"=>\json_decode($menus,true)
            ]);
        }
        $c->create([
            "judul"=>"SISTEM INFORMASI PENELITIAN DAN PENGEMBANGAN",
            "video"=>"https://video.com",
            "menu"=>[
                [
                    "text"=>"Beranda",
                    "link"=>"/"
                ],
                [
                    "text"=>"Data Laporan",
                    "link"=>"/laporan"
                ],
                [
                    "text"=>"Rekapitulasi",
                    "link"=>"/rekapitulasi"
                ],
                [
                    "text"=>"Web GIS",
                    "link"=>"/web-gis"
                ],
            ]
        ]);
    }
}
