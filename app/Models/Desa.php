<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Desa extends Model
{
    use HasFactory;
    protected $fillable = ["name","kecamatan_id"];

    public function kecamatan()
    {
        return $this->belongsTo(Kecamatan::class,"kecamatan_id");
    }

    public function datapeta()
    {
        return $this->hasMany(DataPeta::class,"desa_id","id");
    }
}
