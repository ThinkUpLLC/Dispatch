<?php
/**
 *
 * lib/queue/Monitor.php
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
 *  Queue Worker abstraction
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup\queue;

class Monitor extends \thinkup\DispatchParent {

    /**
     * Gearman Server Host/IP
     */
     var $host = false;

     /**
      * Gearman Server port
      */
      var $port = false;
     
    /**
     *
     */
    public function __construct(){
        $servers_array = preg_split('/:/', $this->config('GEARMAN_SERVERS'));
        $this->host = $servers_array[0];
        $this->port = $servers_array[1];
    }

    /**
     * @return array | null
     */
    public function getStatus(){
        $status = null;
        $handle = fsockopen($this->host,$this->port,$errorNumber,$errorString,30);
        if($handle!=null){
            fwrite($handle,"status\n");
            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                if( $line==".\n"){
                    break;
                }
                if( preg_match("~^(.*)[ \t](\d+)[ \t](\d+)[ \t](\d+)~",$line,$matches) ){
                    $function = $matches[1];
                    $status['operations'][$function] = array(
                        'function' => $function,
                        'total' => $matches[2],
                        'running' => $matches[3],
                        'connectedWorkers' => $matches[4],
                    );
                }
            }
            fwrite($handle,"workers\n");
            while (!feof($handle)) {
                $line = fgets($handle, 4096);
                if( $line==".\n"){
                    break;
                }
                // FD IP-ADDRESS CLIENT-ID : FUNCTION
                if( preg_match("~^(\d+)[ \t](.*?)[ \t](.*?) : ?(.*)~",$line,$matches) ){
                    $fd = $matches[1];
                    $status['connections'][$fd] = array(
                        'fd' => $fd,
                        'ip' => $matches[2],
                        'id' => $matches[3],
                        'function' => $matches[4],
                    );
                }
            }
            fclose($handle);
        }
        return $status;
    }

}