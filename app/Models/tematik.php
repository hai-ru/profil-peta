<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tematik extends Model
{
    use HasFactory;
    protected $fillable = ["name","desa_id"];
}
