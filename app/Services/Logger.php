<?php

class Logger {
    private $facility;
    private $appName;

    public function __construct($appName = 'ProjectsApp') {
        $this->facility = LOG_LOCAL0;  // You can change this to another facility if needed
        $this->appName = $appName;
        openlog($this->appName, LOG_ODELAY | LOG_PID, $this->facility);
    }

    public function __destruct() {
        closelog();
    }

    public function error($message, array $context = []) {
        $this->log(LOG_ERR, $message, $context);
    }

    public function info($message, array $context = []) {
        $this->log(LOG_INFO, $message, $context);
    }

    public function warning($message, array $context = []) {
        $this->log(LOG_WARNING, $message, $context);
    }

    private function log($priority, $message, array $context = []) {
        $contextJson = !empty($context) ? ' Context: ' . json_encode($context) : '';
        syslog($priority, $message . $contextJson);
    }
}