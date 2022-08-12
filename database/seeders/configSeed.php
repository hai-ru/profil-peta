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
        if(!empty($c->first())) return null;
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
