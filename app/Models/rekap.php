<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rekap extends Model
{
    use HasFactory;
    protected $fillable = [
        "sektor",
        "nama",
        "x",
        "y",
        "fungsi",
        "kondisi",
        "akses",
        "tematik_id",
        "desa_id",
    ];

    public function desa()
    {
        return $this->belongsTo(Desa::class,"desa_id","id");
    }

    public function tematik()
    {
        return $this->belongsTo(tematik::class,"tematik_id","id");
    }
}
