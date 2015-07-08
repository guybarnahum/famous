<?php namespace App\Components;
    
class StringUtils{
        
    // ............................................................. get_version
    
    public static function get_version()
    {
        $ver = env('BUILD_VER_STRING');
        return $ver;
    }
    
    // ............................................................. getUrlParam
    
    public static function getUrlParam( $url, $param_name )
    {
        $q  = parse_url( $url, PHP_URL_QUERY);
        $ok = !empty($q);
        
        // obtain query members
        if ($ok){
            $query = false;
            parse_str($q, $query);
            $ok = is_array( $query );
        }
        
        $param = false;
        if ( $ok && isset( $query[ $param_name ] ) ){
            $param = $query[ $param_name ];
        }
        
        return $param;
    }
    
    // ............................................................ getUrlParams
    
    public static function getUrlParams( $url )
    {
        $q  = parse_url( $url, PHP_URL_QUERY);
        $ok = !empty($q);
        
        // obtain query members
        if ($ok){
            $query = false;
            parse_str($q, $query);
            $ok = is_array( $query );
        }
        
        return $ok? $query : [];
    }
}