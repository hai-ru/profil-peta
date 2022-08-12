<?php
namespace App\Helper;

class Helper {
    
    public function config()
    {
        return \App\Models\config::first();
    }
}