<?php namespace App\Components;

class DateTimeUtils {

    // ............................................................... nice_time
    public static function nice_time( $seconds )
    {
        if ( ! is_int( $seconds ) ) return false;
        
        $periods = array("second", "minute", "hour", "day",
                         "week", "month", "year", "decade");
        
        $ratios  = array("60","60","24","7","4.35","12","10");
        
        $tense =    ( $seconds > 0 )? "ago":"from now";
        $value = abs( $seconds );
        
        for($j = 0; $value >= $ratios[$j] && $j < count($ratios)-1; $j++) {
            $value /= $ratios[$j];
        }
        
        $value = round($value);
        
        if($value != 1) {
            $periods[$j].= "s";
        }
        
        return "$value $periods[$j] $tense";
    }
}