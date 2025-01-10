<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kecamatan extends Model
{
    use HasFactory;

    protected $fillable = ["name","kabupaten_kotas_id"];

    public function Kabupaten()
    {
        return $this->belongsTo(Kabupaten::class,"kabupaten_kotas_id");
    }
}
