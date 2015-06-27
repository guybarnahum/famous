<?php namespace App\Components;

class DateTimeUtils {

    public static function datetime( $ts = 0, $fmt = false )
    {
        // Our default fmt is the MySQL DATETIME format ( 2015-08-10 17:16:18 )
        if (!$ts ) $ts = time();
        if (!$fmt) $fmt = date("Y-m-d H:i:s");
        return date( $fmt );
    }
    
    // ............................................................... nice_time
    public static function nice_time( $seconds )
    {
        if ( !is_int( $seconds ) ){
            // try our best to convert argument into relative seconds
            $seconds = strtotime( $seconds );
            if ( false === $seconds ) return false;
            $seconds = time() - $seconds;
        }
        
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

class PrettyDateTime
{
    // The constants correspond to units of time in seconds
    const MINUTE = 60;
    const HOUR   = 3600;
    const DAY    = 86400;
    const WEEK   = 604800;
    const MONTH  = 2628000;
    const YEAR   = 31536000;
    /**
     * A helper used by parse() to create the human readable strings. Given a
     * positive difference, corresponding to a date in the past, it appends the
     * word 'ago'. And given a negative difference, corresponding to a date in
     * the future, it prepends the word 'In'. Also makes the unit of time plural
     * if necessary.
     *
     * @param  integer $difference The difference between dates in any unit
     * @param  string  $unit       The unit of time
     * @return string  The date in human readable format
     */
    private static function prettyFormat( $difference, $unit)
    {
        // $prepend is added to the start of the string if the supplied
        // difference is greater than 0, and $append if less than
        $prepend    = ($difference < 0) ? 'In '  : '';
        $append     = ($difference > 0) ? ' ago' : '';
        $difference = floor(abs($difference));
        // If difference is plural, add an 's' to $unit
        if ($difference > 1) {
            $unit = $unit . 's';
        }
        return sprintf('%s%d %s%s', $prepend, $difference, $unit, $append);
    }
    
    /**
     * Returns a pretty, or human readable string corresponding to the supplied
     * $dateTime. If an optional secondary DateTime object is provided, it is
     * used as the reference - otherwise the current time and date is used.
     *
     * Examples: 'Moments ago', 'Yesterday', 'In 2 years'
     *
     * @param  DateTime $dateTime  The DateTime to parse
     * @param  DateTime $reference (Optional) Defaults to the DateTime('now')
     * @return string   The date in human readable format
     */
    public static function parse(\DateTime $dateTime, \DateTime $reference = null)
    {
        // If not provided, set $reference to the current DateTime
        if (!$reference) {
             $time_zone = $dateTime->getTimezone()->getName();
             $reference = new \DateTime(NULL, new \DateTimeZone( $time_zone ));
        }
        
        // Get the difference between the current date and the supplied $dateTime
        $difference = $reference->format('U') - $dateTime->format('U');
        $absDiff    = abs($difference);
        // Get the date corresponding to the $dateTime
        $date       = $dateTime->format('Y/m/d');
        
        // Throw exception if the difference is NaN
        if (is_nan($difference)) {
            throw new Exception('The difference between the DateTimes is NaN.');
        }
        
        // Today
        if ($reference->format('Y/m/d') == $date) {
            
            if (0 <= $difference && $absDiff < self::MINUTE) {
                return 'Moments ago';
            }
            else if ($difference < 0 && $absDiff < self::MINUTE) {
                return 'Seconds from now';
            }
            else if ($absDiff < self::HOUR) {
                return self::prettyFormat($difference / self::MINUTE, 'minute');
            }
            else {
                return self::prettyFormat($difference / self::HOUR, 'hour');
            }
        }
        
        $yesterday = clone $reference;
        $yesterday->modify('- 1 day');
        
        $tomorrow = clone $reference;
        $tomorrow->modify('+ 1 day');
        
        if ($yesterday->format('Y/m/d') == $date) {
            return 'Yesterday';
        }
        else if ($tomorrow->format('Y/m/d') == $date) {
            return 'Tomorrow';
        }
        else if ($absDiff / self::DAY <= 7) {
            return self::prettyFormat($difference / self::DAY, 'day');
        }
        else if ($absDiff / self::WEEK <= 5) {
            return self::prettyFormat($difference / self::WEEK, 'week');
        }
        else if ($absDiff / self::MONTH < 12) {
            return self::prettyFormat($difference / self::MONTH, 'month');
        }
        
        // Over a year ago
        return self::prettyFormat($difference / self::YEAR, 'year');
    }
}