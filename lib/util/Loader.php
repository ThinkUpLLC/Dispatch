<?php
/**
 *
 * lib/util/Loader.php
 *
 * Copyright (c) 2013 Mark Wilkie
 *
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
 * Class/lib autoloader
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

class Loader {

    static private $ext_lib_dirs = array('GearmanManager');

    /**
     * Register
     *
     * Registers the autoloader to enable lazy loading
     *
     * @param array $paths Array of additional lookup path strings
     * @return bool
     */
    public static function register(Array $paths=null) {
        spl_autoload_register('\Loader::load');
    }


    /**
     * Load
     *
     * The method registered to run on _autoload. Loads our classes on demand.
     *
     * @param string $class
     * @param bool
     */
    public static function load($class) {
        // check if class is already in scope
        if (class_exists($class, false)) {
            return;
        } else {
            $class_info = preg_split("/\\\/", $class);
            // are we a thinkup package?
            if($class_info[0] == 'thinkup') {
                $lib_root = dirname(__FILE__);
                $lib_root = substr($lib_root, 0, -5);
                array_shift($class_info);
                $php_file = $lib_root;
                foreach($class_info as $path) {
                    $php_file .= '/' . $path;
                }
                $php_file .= '.php';
                if(file_exists($php_file)) {
                    require_once($php_file);
                }

            // we are a third party lib or a test?
            } else {
                $class_name = $class_info[sizeof($class_info) - 1];
                
                // are we running in test mode
                if( preg_match('/\w+Test$/', $class_name) )
                {
                    $test_root = dirname(__FILE__);
                    $php_file = substr($test_root, 0, -9) . '/tests';
                    foreach($class_info as $path) {
                        $php_file .= '/' . $path;
                    }
                    $php_file .= '.php';
                    if(file_exists($php_file)) {
                        require_once($php_file);
                    }
                }
                else {
                    $extlib_root = dirname(__FILE__);
                    $php_file = substr($extlib_root, 0, -9) . '/extlib/' .  $class . '/' . $class . '.php';
                    if(file_exists($php_file)) {
                        require_once($php_file);
                    }
                }
            }
        }
    }
}