<?php
/**
 *
 * lib/DispatchParent.php
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
 * This is the root class for the ThinkUp Crawl Manager Project
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup;

class DispatchParent {

    private $redirect_destination = null;

    private $ext_lib_root = null;

    protected static $config = null;

    private static $init = false;
    
    function __construct() {
        $this->lib_root = dirname(__FILE__);
        $this->ext_lib_root = $this->lib_root . '/../extlib';
        self::init();
    }

    public static function isLoaderRegistered() {
        return self::$init;
    }

    public static function init() {
        // reigister our class loader.
        if( self::$init == false ) {
            require_once dirname(__FILE__) . '/util/Loader.php';
            \Loader::register();

            // init our config
            require_once dirname(__FILE__) . '/../config/Config.php';
            self::$config = new \thinkup\config\Config();

            self::$init = true;
        }
    }

    public static function config($key) {
        self::init();
        return self::$config->get($key);
    }
}