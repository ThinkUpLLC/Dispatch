<?php
/**
 *
 * lib/queue/Worker.php
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

use \thinkup\util\Logger as LOG;

class Worker extends \thinkup\DispatchParent {
    
    static $chameleon_cmd = '';

    /**
     * @return thinkup\queue\Worker
     */
    public function __construct() {
        $crawl_stats_dao = new \thinkup\model\CrawlStatsDAO();
        $path = sprintf("%s%s", $crawl_stats_dao->config('THINKUP_INSTALL_DIR'), '/webapp/crawler/chameleon');
        self::$chameleon_cmd = sprintf("cd %s; %s %s", $path, $crawl_stats_dao->config('PHP'), 'chameleoncrawl.php');

    }

    /**
     * Starts our worker process. It loops and sleeps until a new job is pulled form the queue,
     * the it processes the jobs and loops again.
     */
    public function start() {

        /**
         * Worker function to register for GearmanWorker
         */
        function workerCrawl($job) {
            \thinkup\queue\Worker::processJob($job);
        }

        $worker= new \GearmanWorker();
        # Add default server (localhost).
        $worker->addServers($this->config('GEARMAN_SERVERS'));

        $worker->addFunction("crawl", "thinkup\queue\workerCrawl");

        while(1) {
            LOG::get()->info("Waiting for job");
            $ret = $worker->work();
            if ($worker->returnCode() != GEARMAN_SUCCESS) {
                break;
            }
        }
    }

    /**
     * Processes a gearman crawl job
     * @param Job a Gearman Job to process
     */
    public static function processJob($job) {
        $crawl_start = time();
        $cmo = new \thinkup\model\CrawlStatsDAO();
        
        $workload = $job->workload();
        LOG::get()->debug("Received job: " . $job->handle() );
        LOG::get()->debug("Workload: $workload");
        $job_object = json_decode($workload, true);
        if(!$job_object) {
            throw new \thinkup\exceptions\InvalidCrawlJobList("Invalid workload json: $workload");
        }
        \thinkup\queue\Client::validateJobList(array($job_object));

        $installation_name = $job_object['installation_name'];
        $cmd = self::$chameleon_cmd . " '$workload'";        
        LOG::get()->debug("Running command: $cmd");
        $output = '';
        $return_value = 0;
        $cmdout = exec($cmd, $out, $return_value);
        if($return_value > 0) {
            $output = $cmd . ' Failed: ' . $return_value;
        } else {
            foreach($out as $line) {
                $output .= $line;
            }
        }
        $crawl_finish = time();
        $crawl_time = $crawl_finish - $crawl_start;

        $crawl_stats_dao = new \thinkup\model\CrawlStatsDAO();

        $insertid = $crawl_stats_dao->saveStatus(array(
            'install_name' => $job_object['installation_name'],
            'crawl_time' => $crawl_time,
            'crawl_start' => $crawl_start,
            'crawl_finish' => $crawl_finish,
            'log' => $output,
            'crawl_status' => $return_value
        ));
        LOG::get()->debug(sprintf("Crawl for installation '%s' completed in %s seconds", $job_object['installation_name'], $crawl_time) );
    }

}


