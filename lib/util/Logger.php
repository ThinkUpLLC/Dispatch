<?php
/**
 *
 * lib/util/Logger.php
 *
 * Copyright (c) 2013 Mark Wilkie
 *
 * ThinkUp is free software: you can redistribute it and/or modify it under the terms of the GNU General Public
 * License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any
 * later version.
 *
 * ThinkUp is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with ThinkUp.  If not, see
 * <http://www.gnu.org/licenses/>.
 *
 *
 * Our log4php abstraction class
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */
 
namespace thinkup\util;

class Logger {

    private static $is_configured = false;
    
    private static $logger = null;

    /**
     * init
     *
     * inits out logger
     *
     */
    public static function init() {
        if(self::$is_configured === false) {
            $test_root = dirname(__FILE__);
            $log_config = substr($test_root, 0, -9) . '/config/logger-config.php';
            if(! file_exists($log_config)) {
                die("unable to log logger config: $log_config");
            }
            \Logger::configure($log_config);
            self::$logger = \Logger::getLogger("main");

            $debug = getenv('DEBUG');
            //TODO env logging level working.
            // if(isset($debug) && $debug == 1) {
            //     echo \LoggerLevel::DEBUG . "%%%%%%";
            //     self::$logger->setLevel(\LoggerLevel::getLevelDebug());
            //     $appenders = self::$logger->getAllAppenders();
            //     //[0]->setThreshold(\LoggerLevel::getLevelDebug());
            //     var_dump($appenders);
            // }
            self::$logger->debug("Init Logger");
            self::$is_configured = true;
        }
    }

    /**
     * gets a logger instance
     *
     * @return Logger
     */
    public static function get() {
        self::init();
        return self::$logger;
    }

}