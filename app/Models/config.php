<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class config extends Model
{
    use HasFactory;

    protected $casts = [
        "menu" => 'array',
    ];

    protected $fillable = [
        "menu",
        "video",
        "judul",
    ];
}
