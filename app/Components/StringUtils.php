<?php namespace App\Components;
    
class StringUtils{
        
    // ............................................................. get_version
    
    public static function getBuildVersion()
    {
        $ver = env('BUILD_VER_STRING');
        return $ver;
    }
    
    public static function getDevGuid()
    {
        // look if we have one in cookie
        $dev_guid = \Cookie::get('dev_guid');
        
        if (empty($dev_guid)){
            $dev_guid = uniqid('dev',true);
            $c = \Cookie::forever('dev_guid', $dev_guid);
            \Cookie::queue( $c );
        }
        
        return $dev_guid;
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