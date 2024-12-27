<?php

class TimeHelper {
    public static function getRelativeTime($timestamp) {
        $time = strtotime($timestamp);
        $now = time();
        $diff = $now - $time;
        
        $intervals = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );
        
        foreach ($intervals as $seconds => $label) {
            $interval = floor($diff / $seconds);
            
            if ($interval >= 1) {
                $plural = $interval > 1 ? 's' : '';
                return "($interval $label$plural ago)";
            }
        }
        
        return "(just now)";
    }
}