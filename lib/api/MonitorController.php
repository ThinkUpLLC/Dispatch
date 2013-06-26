<?php
/**
 *
 * lib/api/MonitorController.php
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
 * Our Queue Monitor Controller
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup\api;

use \thinkup\util\Logger as LOG;

class MonitorController extends \thinkup\api\CrawlDispatcherController {

    private $monitor = false;

    /**
     * @param Object ClientQueue
     * @return MonitorController
     */
    function __construct($monitor = false) {
        if(! $monitor) {
            $this->monitor = new \thinkup\queue\Monitor();
        } else {
            $this->monitor = $monitor;
        }
    }

    /**
     * our main auth controller entry point
     */
    public function auth_execute() {
        if(isset($_GET['nagios_check']) && $_GET['nagios_check'] == 1) {
            $workers_wanted = $this->config('CONNECTED_WORKERS');
            if($this->nagiosCheck($this->getStatus()) == true) {
                return array('status' => $workers_wanted . ' running worker(s) - OK');
            } else {
                return array('status' => $workers_wanted . ' running worker(s) found - NOT OK');
            }
        } else {
            $gearman_status = $this->getStatus();
            $status_response = array();
            //'gearman_status'=> $gearman_status
            if($this->nagiosCheck($gearman_status) == true) {
                $status_response['gearman_ok'] = true;
                $status_response['gearman_status'] = $gearman_status;
            } else {
                $status_response['gearman_ok'] = false;
                if($gearman_status) {
                    $status_response['gearman_status'] = $gearman_status;
                }
            }
            $status_response['workers_wanted'] = $this->config('CONNECTED_WORKERS');
            return $status_response;
        }

    }

    /**
     * get status data
     * @return Array a status hash
     */
     public function getStatus() {
         $gearman_status = $this->monitor->getStatus();
         return $gearman_status;
     }

    /**
     * check running workers for nagios
     * @return Array a nagios status hash
     */
     public function nagiosCheck($status) {
         $workers_wanted = $this->config('CONNECTED_WORKERS');
         if(isset($status) && isset($status['operations'])) {
             if(isset($status['operations']['crawl'])) {
                 $crawl = $status['operations']['crawl'];
                 if(isset($crawl['connectedWorkers'])) {
                     if($crawl['connectedWorkers'] == $workers_wanted) {
                         // status ok!
                         return true;
                     }
                 }
             }
         }
         // status not ok
         return false;
     }
}