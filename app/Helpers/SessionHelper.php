<?php

class SessionHelper {
    private static $started = false;

    public static function start() {
        if (!self::$started && session_status() === PHP_SESSION_NONE) {
            session_start();
            self::$started = true;
        }
    }
} 