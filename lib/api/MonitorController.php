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
            return $this->nagiosCheck();
        } else {
            return $this->getStatus();
        }

    }

    /**
     * get status data
     * @return Array a status hash
     */
     public function getStatus() {
         $gearman_status = $this->monitor->getStatus();
         return array('gearman_status'=> $gearman_status);
     }

    /**
     * check running workers for nagios
     * @return Array a nagios status hash
     */
     public function nagiosCheck() {
         $status =  $this->monitor->getStatus();
         $workers_wanted = $this->config('CONNECTED_WORKERS');
         if(isset($status) && isset($status['operations'])) {
             if(isset($status['operations']['crawl'])) {
                 $crawl = $status['operations']['crawl'];
                 if(isset($crawl['connectedWorkers'])) {
                     if($crawl['connectedWorkers'] == $workers_wanted) {
                         // status ok!
                         return array('status' => $workers_wanted . ' running worker(s) - OK');
                     }
                 }
             }
         }
         // status not ok
         return array('status' => $workers_wanted . ' running worker(s) not found - NOT OK');
     }

}