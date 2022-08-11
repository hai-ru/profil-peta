<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;

    protected $casts = [
        "feature"=>"array",
        "property"=>"array",
        "pemetaan_id"=>"array",
    ];

    protected $fillable = [
        "uid",
        "name",
        "feature",
        "property",
        "pemetaan_id",
    ];

    public function Pemetaan()
    {
        return $this->belongsTo(Pemetaan::class,"pemetaan_id","id");
    }
}
