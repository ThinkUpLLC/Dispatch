<?php
/**
 *
 * lib/api/JobQueueController.php
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
 * Our Job Queue Controller
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup\api;

use \thinkup\util\Logger as LOG;

class JobQueueController extends \thinkup\api\CrawlDispatcherController {

    private $queue = false;

    /**
     * @param Object ClientQueue
     * @return JobQueueController
     */
    function __construct($queue = false) {
        if($queue !== false) {
            $this->queue = $queue;
        } else {
            $this->queue = new \GearmanClient();
        }
        $this->queue->addServers( $this->config('GEARMAN_SERVERS') );
    }

    /**
     * our main auth controller entry point
     */
    public function auth_execute() {
        // get params
        if(! isset($_POST['jobs']) && ! isset($_GET['jobs'])) {
            $message = "Invalid request: no job queue data in request";
            LOG::get()->debug($message);
            return $this->header_status(400, "Invalid request: no job queue data in request");
        } else {
            $jobs_json = isset($_POST['jobs']) ? $_POST['jobs'] : $_GET['jobs'];
            try {
                $obj_array = \thinkup\util\JSONDecoder::decode($jobs_json, true);
            } catch(\thinkup\exceptions\JSONDecoderException $e) {
                $message = "Invalid request: " . $e->getMessage();
                LOG::get()->debug($message);
                return $this->header_status(400, $message);                
            }
            try {
                $queue_client = new \thinkup\queue\Client($this->queue);
                $cnt = $queue_client->queueCrawlJobs($obj_array);
                $message = $cnt . " Job(s) Queued";
                return array( "success" => $message );
            } catch(\thinkup\exceptions\InvalidCrawlJobList $e) {
                $message = "Invalid request: " . $e->getMessage();
                LOG::get()->debug($message);
                return $this->header_status(400, $message);
            }
        }
    }
}