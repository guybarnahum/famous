<?php namespace App\Components;
    
class StringUtils{
    
    public static function normalize_str( $str, $whitespace = "" )
    {
        // Additional Swedish filters
        $str = str_replace(array("ä", "Ä"), "a", $str);
        $str = str_replace(array("å", "Å"), "a", $str);
        $str = str_replace(array("ö", "Ö"), "o", $str);
            
        // Remove any character that is not alphanumeric or white-space
        $str = preg_replace("/[^a-z0-9\s]/i", "", $str);
        // Replace multiple instances of white-space with a single space
        $str = preg_replace("/\s\s+/", " ", $str);
        // Replace all spaces with $whitespace
        $str = preg_replace("/\s/", $whitespace, $str);
        // Remove leading and trailing $whitespace
        $str = trim($str, $whitespace);
        // Lowercase the URL
        $str = strtolower($str);
        
        return $str;
    }
    
    // ............................................................. get_version
    
    public static function getBuildVersion()
    {
        $ver = env('BUILD_VER_STRING');
        return $ver;
    }
    
    // .............................................................. getDevGuid
    
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
 
    // ...................................................... getDeviceSignature
    
    public static function getDeviceSignature( $seed )
    {
        $dev_guid   = self::getDevGuid();
        $seed       = self::normalize_str( $seed );
        $signature  = $dev_guid . '.' . $seed;
        
        return $signature;
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