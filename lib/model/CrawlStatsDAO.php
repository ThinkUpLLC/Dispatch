<?php
/**
 *
 * lib/model/CrawlStatsDAO.php
 *
 * Copyright (c) 2009-2013 Mark Wilkie
 *
 * LICENSE:
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
 * CrawlStatsDAO
 *
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2009-2013 Mark Wilkie
 * @author Mark Wilkie
 */

namespace thinkup\model;
 
class CrawlStatsDAO extends \thinkup\model\PDODAO {

    /**
     * Save a crawl status
     * @param Array A hash of crawl stats data
     * @return int last instertid
     */
    public function saveStatus($args) {
        if(! isset($args['crawl_start']) || ! preg_match('/^\d{10}$/', $args['crawl_start'])) {
            throw new \thinkup\exceptions\InvalidArgumentException('crawl_start must be a valid unix timestamp');
        }
        if(! isset($args['crawl_finish']) || ! preg_match('/^\d{10}$/', $args['crawl_finish'])) {
            throw new \thinkup\exceptions\InvalidArgumentException('crawl_finish must be a valid unix timestamp');
        }
        if($args['crawl_finish'] < $args['crawl_start']) {
            throw new \thinkup\exceptions\InvalidArgumentException('crawl_finish must be greater or equal to crawl_start');
        }
        if(! isset($args['crawl_time']) || ! preg_match('/^\d+$/', $args['crawl_time'])) {
            throw new \thinkup\exceptions\InvalidArgumentException('crawl_time must be a valid seconds integer');
        }
        if(! isset($args['install_name']) || $args['install_name'] == '') {
            throw new \thinkup\exceptions\InvalidArgumentException('requires an install_name');
        }
        if(! isset($args['log']) || $args['log'] == '') {
            throw new \thinkup\exceptions\InvalidArgumentException('requires a log');
        }
        if(! isset($args['crawl_status']) || preg_match('/\D/', $args['crawl_status'])) {
            throw new \thinkup\exceptions\InvalidArgumentException('requires a valid status');
        }

        // remove log to avoid pdo to many args error
        $log = $args['log'];
        unset($args['log']);

        // record crawl status
        $sql = "insert into crawl_status (install_name, crawl_time, crawl_start, crawl_finish, crawl_status) "
        . "values (:install_name, :crawl_time, FROM_UNIXTIME(:crawl_start), FROM_UNIXTIME(:crawl_finish), :crawl_status)";
        $stmt = $this->execute($sql, $args);
        $insertid = $this->getInsertId($stmt);

        // record crawl log
        $sql = "insert into crawl_log (crawl_status_id, crawl_log) "
        . "values (:crawl_status_id, :crawl_log)";
        $stmt = $this->execute($sql, array('crawl_status_id' => $insertid, 'crawl_log' => $log ));

        return $insertid;
    }

    /**
     * get crawl stats
     * @param String Optional install name
     * @return Array - a hash of status data
     */
     public function getCrawlStats($install_name = false) {
         $where = $install_name ? 'where install_name = :install_name AND ' : 'where ';
         $sql = "select crawl_status, round(avg(crawl_time)) as average, max(crawl_time) as max, " .
         "min(crawl_time) as min, count(id) as count from crawl_status " .
         $where . 'crawl_start > date_sub(now(), INTERVAL 1 DAY) ' .
         "group by crawl_status order by crawl_status";
        $binds = array();
        if($install_name) {
            $binds[':install_name'] = $install_name;
        }
        $sth = $this->execute($sql, $binds);
        $stats = $this->getDataRowsAsArrays($sth);
        return $stats;
     }

     /**
      * get crawl stats
      * @param String Optional install name
      * @return Array - a hash of crawl data - last 200 records
      */
      public function getCrawlData($install_name = false) {
          $where = $install_name ? 'where install_name = :install_name ' : ' ';
          $sql = "select id, install_name, crawl_status, crawl_time, crawl_start, crawl_finish " .
          "from crawl_status " .
          $where .
          "order by id desc limit 200";
         $binds = array();
         if($install_name) {
             $binds[':install_name'] = $install_name;
         }
         $sth = $this->execute($sql, $binds);
         $stats = $this->getDataRowsAsArrays($sth);
         return $stats;
      }

     /**
      * Get failed crawls
      * @return Array - a hash of crawl data - last 200 records
      */
      public function getFailedCrawlData() {
          $sql = "select id, install_name, crawl_status, crawl_time, crawl_start, crawl_finish " .
          "from crawl_status WHERE crawl_status > 0 order by id desc limit 200";
         $sth = $this->execute($sql);
         $stats = $this->getDataRowsAsArrays($sth);
         return $stats;
      }



      /**
       * get crawl stats
       * @param String Optional install name
       * @return Array - a hash of crawl data - last 200 records
       */
       public function getCrawlLog($crawl_status_id) {
          $sql = "select id, crawl_status_id, crawl_log from crawl_log where crawl_status_id = :crawl_status_id";
          $binds = array(':crawl_status_id' => $crawl_status_id);
          $sth = $this->execute($sql, $binds);
          $log = $this->getDataRowAsArray($sth);
          return $log;
       }

}
