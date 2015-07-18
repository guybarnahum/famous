<?php namespace App\Components;
    
class PhotoUtils{
    
    static public function getSize( $url )
    {
        \Debugbar::info( 'PhotoUtils::getSize(' . $url . ')' );
        
        $size = self::getJpegSize( $url );
        
        if ( !is_array($size) ){
            $size = getimagesize($url);
        }
        
        \Debugbar::info( 'PhotoUtils::getSize(' . print_r($size,true) . ')' );
       
        return $size;
    }
    
    
    // Attept to retrieve JPEG width and height *without*
    // downloading / reading entire image.
    
    static public function getJpegSize( $url )
    {
        
        try{
            $handle = fopen( $url, "rb");
            $ok = $handle !== false;
        }
        catch( \Exception $e )
        {
            $ok = false;
        }
        
        if (!$ok) return false;
        
        $ok = !feof( $handle );
        if (!$ok) return false;
        
        $new_block = fread( $handle, 32);
        $ix = 0;
            
        // inspect JPEG header
        $ok = is_array( $new_block )        &&
              $new_block[ $ix     ]=="\xFF" &&
              $new_block[ $ix + 1 ]=="\xD8" &&
              $new_block[ $ix + 2 ]=="\xFF" &&
              $new_block[ $ix + 3 ]=="\xE0" ;
        
        // Not a JPEG?
        if (!$ok) return false;
        
        $ix += 4;
                
        $ok = $new_block[ $ix + 2 ]=="\x4A" &&
              $new_block[ $ix + 3 ]=="\x46" &&
              $new_block[ $ix + 4 ]=="\x49" &&
              $new_block[ $ix + 5 ]=="\x46" &&
              $new_block[ $ix + 6 ]=="\x00" ;
        
        // Malformed JPEG?
        if (!$ok) return false;
        
        // Read block size and skip ahead to begin cycling through blocks
        // in search of SOF marker
                
        $block_size = unpack("H*", $new_block[ $ix ] . $new_block[ $ix + 1 ]);
        $block_size = hexdec($block_size[ 1 ]);
        
        while( !feof($handle) ) {
            
            $ix += $block_size;
            $new_block .= fread( $handle, $block_size );
            
            $ok = $new_block[ $ix ] =="\xFF";
            if (!$ok) break;
            
            // New block detected, check for SOF marker
            $sof_marker = [ "\xC0", "\xC1", "\xC2", "\xC3", "\xC5", "\xC6",
                            "\xC7", "\xC8", "\xC9", "\xCA", "\xCB", "\xCD",
                            "\xCE", "\xCF"
            ];
                
            if(in_array( $new_block[ $ix + 1 ], $sof_marker)) {
                // SOF marker detected!
                // Width and height information is contained in
                // bytes 4-7 after this byte.
                $size_data = $new_block[ $ix + 2 ] . $new_block[ $ix + 3 ] .
                             $new_block[ $ix + 4 ] . $new_block[ $ix + 5 ] .
                             $new_block[ $ix + 6 ] . $new_block[ $ix + 7 ] .
                             $new_block[ $ix + 8 ] ;
                    
                    $unpacked = unpack("H*", $size_data);
                    $unpacked = $unpacked[1];
                    $height = hexdec( $unpacked[ 6] . $unpacked[ 7] . $unpacked[ 8] . $unpacked[ 9]);
                    $width  = hexdec( $unpacked[10] . $unpacked[11] . $unpacked[12] . $unpacked[13]);
                    
                    return [ $width, $height ];
            }
            
            // Skip block marker and read block size
            $ix += 2;
            $block_size = unpack("H*", $new_block[ $ix ] . $new_block[ $ix + 1 ]);
            $block_size = hexdec( $block_size[1] );
        }
        
        return false;
    }
}