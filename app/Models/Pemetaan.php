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

    public function Icon()
    {
        switch ($this->polyline["default"] ?? "") {
            case 'polyline':
                return "<div style='
                height: 10px;
                width: 21px;
                background: {$this->marker['strokeColor']};
                display: inline-block;'></div>";
                break;
            case 'polygon':
                return "<div style='
                height: 10px;
                width: 21px;
                border: {$this->marker['strokeWeight']}px solid {$this->marker['strokeColor']};
                background: {$this->marker['fillColor']};
                opacity: {$this->marker['fillOpacity']};
                display: inline-block;'></div>";
                break;
            
            default:
                $icon = $this->marker["icon"] ?? "";
                return "<img src='$icon' />";
            break;
        }
    }
}
