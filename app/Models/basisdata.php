<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class basisdata extends Model
{
    use HasFactory;
    protected $fillable = ["tipe","filepath","icon"];
}
