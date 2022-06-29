<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataPeta extends Model
{
    use HasFactory;

    protected $fillable = ["geojson"];

    public function desa()
    {
        return $this->belongsTo(Desa::class,"desa_id","id");
    }
    public function tematik()
    {
        return $this->belongsTo(tematik::class,"tematik_id","id");
    }
}
