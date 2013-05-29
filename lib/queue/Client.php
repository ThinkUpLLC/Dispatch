<?php
/**
 *
 * lib/queue/Client.php
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
 * Client Queue
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup\queue;

use \thinkup\util\Logger as LOG;

class Client extends \thinkup\DispatchParent {

    private $queue = null;

    /**
     * @param Object GearmanClients
     * @return Client
     */
    public function __construct($queue = false) {
        if($queue !== false) {
            $this->queue = $queue;
        } else {
            $this->queue = new \GearmanClient();
        }
        $this->queue->addServers( $this->config('GEARMAN_SERVERS') );
    }

    /**
     * Queues a list of jobs
     * @param Array a lis of crawl jobs to queue
     * @return int number of jobs processed
     */
    public function queueCrawlJobs($crawl_jobs) {
        self::validateJobList($crawl_jobs);
        $count = 0;
        foreach($crawl_jobs as $job) {
            $this->queue->doBackground("crawl", json_encode($job));
            $count++;
        }
        return $count;
    }

    /**
     * Validates a list of jobs
     * @param Array a list of crawl jobs to queue
     * @throws \thinkup\exceptions\InvalidCrawlJobLis
     */    
    public static function validateJobList($crawl_jobs) {
        if(! is_array($crawl_jobs) || sizeof($crawl_jobs) == 0 || ! isset($crawl_jobs[0])) {
            throw new \thinkup\exceptions\InvalidCrawlJobList("Crawl jobs is not a valid list");
        }
        foreach($crawl_jobs as $job) {
            // {"installation_name":"mwilkie", "timezone":"America/Los_Angeles", "db_host":"localhost", "db_name":"thinkup_20120911", "db_socket":"/tmp/mysql.sock", "db_port":""}
            if(! isset($job['timezone']) || ! isset($job['installation_name']) 
            || ! isset($job['db_name']) || ! isset($job['db_host']) 
            || ! ( isset($job['db_port']) || isset($job['db_socket'])) ) {
                throw new \thinkup\exceptions\InvalidCrawlJobList("Crawl Jobs need a 'installation_name', timezone', 'db_name', 'db_host' and 'db_port' or a 'db_socket'");                    
            }
        }
    }
}