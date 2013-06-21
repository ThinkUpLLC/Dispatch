<?php

class MonitorData {

    static $data = array (
      'operations' => 
      array (
        'crawl' => 
        array (
          'function' => 'crawl',
          'total' => '0',
          'running' => '0',
          'connectedWorkers' => '1',
        ),
      ),
      'connections' => 
      array (
        23 => 
        array (
          'fd' => '23',
          'ip' => '127.0.0.1',
          'id' => '-',
          'function' => 'crawl',
        ),
        24 => 
        array (
          'fd' => '24',
          'ip' => '127.0.0.1',
          'id' => '-',
          'function' => '',
        ),
        25 => 
        array (
          'fd' => '25',
          'ip' => '127.0.0.1',
          'id' => '-',
          'function' => '',
        ),
      ),
    );

    public static function getMonitorData() {
        return self::$data;
    }

}