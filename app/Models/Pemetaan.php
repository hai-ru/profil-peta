<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemetaan extends Model
{
    use HasFactory;

    protected $casts = [
        "marker" => 'array',
        "polyline" => 'array',
        "polygon" => 'array',
        "geojson" => 'array',
        "property" => 'array',
    ];

    protected $fillable = [
        "name",
        "marker",
        "polyline",
        "polygon",
        "geojson",
        "property",
    ];

    public function Feature()
    {
        return $this->hasMany(Feature::class,"pemetaan_id","id");
    }
}
