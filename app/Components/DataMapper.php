<?php namespace App\Components;

class DataMapper{
    
    private $obj_map;
    
    function __construct()
    {
        $this->obj_map = array();
    }
    
    public function toString()
    {
        return 'DataMapper:' . print_r( $this->obj_map, true );
    }
    
    // ............................................................. setup_entry

    /*
     * @param $cname src object identifier
     * @param $path the location to 'navigate' into the src object for the value
     * @param $tgt the name of the member to store value in the tgt object
     *
     * @return $this (done for chaining)
     */
    private function setup_entry( $cname, $src_path, $tgt_path, $fmt = null )
    {
        if (!isset( $this->obj_map[ $cname ] ) ){
                    $this->obj_map[ $cname ] = array();
        }
        
        $this->obj_map[ $cname ][ $src_path ] = array( 'path' => $tgt_path,
                                                       'fmt'  => $fmt     );
    }

    public function setup( $map, $fmt_class )
    {
        if ( !is_array( $map ) ){
            $msg  = 'DataMapper::setup - argument is an array!';
            throw new \InvalidArgumentException( $msg );
        }
        
        foreach( $map as $map_entry ){
            
            // remove spaces
            $map_entry = str_replace(' ', '', $map_entry);
            
            $parts = explode( ':', $map_entry );
            
            if ( !is_array( $parts ) || (count($parts) < 3 ) ){
                $msg  = 'DataMapper::setup - invalid map entry ' ;
                $msg .= '(' . $map_entry . ')';
                throw new \InvalidArgumentException( $msg );
            }
            
            $fmt = isset( $parts[3] )? $parts[3] : null ;
            
            if (!empty($fmt)){
                // string string literal mark
                if ($fmt[0] == '!' ){
                    $fmt    = substr($fmt,1);
                }
                // callable
                else{
                    
                    $fmt = array( $fmt_class, $fmt );
                    
                    if ( !is_callable($fmt) ){
                        
                        $msg  = 'DataMapper::setup - invalid fmt in map entry ' ;
                        $msg .= '(' . $map_entry . ')';
                        throw new \InvalidArgumentException( $msg );
                    }
                }
            }
            
            
            $this->setup_entry( $parts[0], $parts[1], $parts[2], $fmt );
        }
        
        return $this;
    }
        
    // ............................................................ navigate_obj
            
    /*
     * @param $obj src object to navigate
     * @param $path the location to 'navigate' into the src object for the value
     *
     * @return $the value found at $path in $obj or false if not found
     */
    private function navigate_obj( $obj, $obj_path )
    {
        // Separate the path into an array of components
        $path_parts = explode('/', $obj_path);

        // Start by pointing at the current object
        $var = $obj;

        // Loop over the parts of the path specified
        foreach( $path_parts as $property )
        {
            // Check for an numerical array?
            if ( is_array  ( $var )                &&
                 is_numeric(         $property )   &&
                 isset( $var[intval( $property )] ) ){
                 // Traverse to the specified property
                  $var = $var[intval( $property )];
            }
            else
            // maybe a associative array?
            if ( is_array  ( $var )                &&
                      isset( $var[ $property ] ) ){
                // Traverse to the specified property
                $var = $var[ $property ];
            }
            else
            // maybe a 'simple' object property?
            if ( isset    (  $var->$property )  ){
                 // Traverse to the specified property
                      $var = $var->$property;
            }
            // path part not found!
            else{
                return null;
            }
        }

        // Our variable has now traversed the specified path
        return $var;
    }
    
    // ............................................................... translate
    
    private function translate( $val, $fmt, $cname, $src_path, $tgt_path )
    {
        if ( is_callable( $fmt ) ){
            return call_user_func( $fmt, $val,
                                   $cname, $src_path, $tgt_path );
        }
        else
        // string literal
        if ( is_string( $fmt ) ){
            return $fmt;
        }
        
        // should not happen .. we tested for this at setup time
        $msg  = 'DataMapper::translate - invalid fmt in map entry ' ;
        $msg .= '(' . print_r($fmt,true) . ')';
        throw new \InvalidArgumentException( $msg );

        // should not get here..
        return null;
    }
    
    // ..................................................................... map
            
    public function map( $cname, $src_obj )
    {
        // do we have a map for cname object?
        if (!is_array( $this->obj_map[ $cname ] ) ){
            return false;
        }
        
        // the map for the src object
        $map = $this->obj_map[ $cname ];
        $tgt_objs = array();

        foreach( $map as $src_path => $tgt ){
            
            $tgt_path = $tgt[ 'path' ];
            $fmt      = $tgt[ 'fmt'  ];
            
            $tgt_parts = explode( '/', $tgt_path );
            
            if ( is_array($tgt_parts) && ( count($tgt_parts)>1 ) ){
                $tgt_name = $tgt_parts[0];
                $tgt_mmbr = $tgt_parts[1];
            }
            else{ // unnamed tgt..
                $tgt_name = '/';
                $tgt_mmbr = $tgt_path;
            }
            
            $ix = 0;
            
            do{
                $path  = str_replace( '*', "$ix", $src_path, $using_wildcard );
                
                $value = $this->navigate_obj( $src_obj ,$path );
            
                // Allow empty value only in case of a string literal and
                // only if the tgt already exists..
                if ( is_string($fmt) && ( $value == null ) ){
                    if (!isset($tgt_objs[ $tgt_name ][ $ix ])) break;
                }
                    
                // We translate even empty values - in case of string literals
                // you can have something from nothing!
                if ( !empty($fmt) ){
                     $value = $this->translate(  $value, $fmt,
                                                $cname, $src_path, $tgt_path );
                }
                
                // we should have a value after translation - even for string
                // literals
                if ( $value == null ) break;
                
                // We have a value to store! Make sure we have tgt objects
                if (!isset($tgt_objs[$tgt_name])){
                           $tgt_objs[$tgt_name]= array();
                }
                
                if (!isset($tgt_objs[ $tgt_name ][ $ix ])){
                           $tgt_objs[ $tgt_name ][ $ix ] = array();
                }
            
                // Now store value in its tgt path
                $tgt_objs[ $tgt_name ][ $ix ][ $tgt_mmbr ] = $value;
                
                // NOTE: Poor's man debug - god help us all!
                //echo "$src_path:$tgt_name.$ix.$tgt_mmbr <= $value";
                //if ( $fmt != null ) echo " with " . print_r( $fmt ) ;
                //echo "\n";
                
                // finnaly increment item for wildcard case
                $ix++;
                
            }while( $using_wildcard );
        }
            
        return $tgt_objs;
    }
}