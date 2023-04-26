<?php

class LogGenerator {

    public static $instance;

    private function __construct() {
        //
    }

    public static function getInstance() {
        if(!isset(self::$instance))
            self::$instance = new LogGenerator();
        
        return self::$instance;
    }

    public function insertLog($message) {
        //
    }
}