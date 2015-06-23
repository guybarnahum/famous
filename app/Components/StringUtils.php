<?php namespace App\Components;
    
class StringUtils{
        
    // ............................................................. get_version
    public static function get_version()
    {
        $ver = env('BUILD_VER_STRING');
        return $ver;
    }
}