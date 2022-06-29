<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPetaDesa extends Model
{
    use HasFactory;
    protected $fillable = [
        "desa_id",
        "tematik_id",
        "style",
        "type",
        "icon",
        "data_peta_id",
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class,"desa_id","id");
    }

    public function tematik()
    {
        return $this->belongsTo(tematik::class,"tematik_id","id");
    }

    public function datapeta()
    {
        return $this->belongsTo(DataPeta::class,"data_peta_id","id");
    }

}
