<?php
/**
 *
 * lib/model/PDODAO.php
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
 * PDODAO
 * Parent class for DAOs
 *
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2009-2013 Mark Wilkie
 * @author Mark Wilkie
 */

namespace thinkup\model;
 
class PDODAO  extends \thinkup\DispatchParent {

    /**
     * PDO instance
     * @var PDO Object
     */
    static $PDO = null;

    /**
     * Constructor
     * @return CMDAO
     */
    public function __construct(){
        if(is_null(self::$PDO)) {
            $this->connect();
        }
    }

    /**
     * Connection initiator
     */
    public final function connect(){
        if(is_null(self::$PDO)) {
            self::$PDO = new \PDO(
                self::getConnectString(),
                $this->config('db_user'),
                $this->config('db_password')
            );
            self::$PDO->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            $timezone = $this->config('TIMEZONE');
            if($timezone) {
                $time = new \DateTime("now", new \DateTimeZone($timezone) );
                $tz_offset = $time->format('P');
                try {
                    self::$PDO->exec("SET time_zone = '$tz_offset'");
                } catch (\PDOException $e) {
                    error_log(print_r($e, true));
                }
            }
        }
    }

    /**
     * Generates a connect string to use when creating a PDO object.
     * @return string PDO connect string
     */
    public static function getConnectString() {
        $db_type = 'mysql';
        $cmo = new \thinkup\DispatchParent();
        $db_socket = $cmo->config('db_socket');
        if ( !$db_socket) {
            $db_port = $cmo->config('db_port');
            if (!$db_port) {
                $db_socket = '';
            } else {
                $db_socket = ";port=".$cmo->config('db_port');
            }
        } else {
            $db_socket=";unix_socket=".$db_socket;
        }
        $db_string = sprintf("%s:dbname=%s;host=%s%s", $db_type,$cmo->config('db_name'), $cmo->config('db_host'),$db_socket);
        return $db_string;
    }

    /**
     * Disconnector
     * Caution! This will disconnect for ALL DAOs
     */
    final function disconnect(){
        self::$PDO = null;
    }

    /**
     * Executes the query, with the bound values
     * @param str $sql
     * @param array $binds
     * @return PDOStatement
     */
    final function execute($sql, $binds = array()) {

        $stmt = self::$PDO->prepare($sql);
        if(is_array($binds) and count($binds) >= 1) {
            foreach ($binds as $key => $value) {
                if(is_int($value)) {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, \PDO::PARAM_STR);
                }
            }
        }
        try {
            $stmt->execute();
        } catch (\PDOException $e) {
            $exception_details = 'Database error! ';
            $exception_details .= 'Crawl Manager could not execute the following query: '.
            str_replace(chr(10), "", $stmt->queryString) . ' PDOException: '. $e->getMessage();
            throw new \thinkup\exceptions\DBException ($exception_details);
        }
        return $stmt;
    }

    /**
     * Proxy for getUpdateCount
     * @param PDOStatement $ps
     * @return int Update Count
     */
    protected final function getDeleteCount($ps){
        //Alias for getUpdateCount
        return $this->getUpdateCount($ps);
    }
    /**
     * Gets a single row and closes cursor.
     * @param PDOStatement $ps
     * @return various array,object depending on context
     */
    protected final function fetchAndClose($ps){
        $row = $ps->fetch();
        $ps->closeCursor();
        return $row;
    }
    /**
     * Gets a multiple rows and closes cursor.
     * @param PDOStatement $ps
     * @return array of arrays/objects depending on context
     */
    protected final function fetchAllAndClose($ps){
        $rows = $ps->fetchAll();
        $ps->closeCursor();
        return $rows;
    }
    /**
     * Gets the rows returned by a statement as array of objects.
     * @param PDOStatement $ps
     * @param str $obj
     * @return array numbered keys, with objects
     */
    protected final function getDataRowAsObject($ps, $obj){
        $ps->setFetchMode(\PDO::FETCH_CLASS,$obj);
        $row = $this->fetchAndClose($ps);
        if(!$row){
            $row = null;
        }
        return $row;
    }

    /**
     * Gets the first returned row as array
     * @param PDOStatement $ps
     * @return array named keys
     */
    protected final function getDataRowAsArray($ps){
        $ps->setFetchMode(\PDO::FETCH_ASSOC);
        $row = $this->fetchAndClose($ps);
        if(!$row){
            $row = null;
        }
        return $row;
    }

    /**
     * Returns the first row as an object
     * @param PDOStatement $ps
     * @param str $obj
     * @return array numbered keys, with Objects
     */
    protected final function getDataRowsAsObjects($ps, $obj){
        $ps->setFetchMode(\PDO::FETCH_CLASS,$obj);
        $data = $this->fetchAllAndClose($ps);
        return $data;
    }

    /**
     * Gets the rows returned by a statement as array with arrays
     * @param PDOStatement $ps
     * @return array numbered keys, with array named keys
     */
    protected final function getDataRowsAsArrays($ps){
        $ps->setFetchMode(\PDO::FETCH_ASSOC);
        $data = $this->fetchAllAndClose($ps);
        return $data;
    }

    /**
     * Gets the result returned by a count query
     * (value of col count on first row)
     * @param PDOStatement $ps
     * @param int Count
     */
    protected final function getDataCountResult($ps){
        $ps->setFetchMode(\PDO::FETCH_ASSOC);
        $row = $this->fetchAndClose($ps);
        if(!$row or !isset($row['count'])){
            $count = 0;
        } else {
            $count = (int) $row['count'];
        }
        return $count;
    }

    /**
     * Gets whether a statement returned anything
     * @param PDOStatement $ps
     * @return bool True if row(s) are returned
     */
    protected final function getDataIsReturned($ps){
        $row = $this->fetchAndClose($ps);
        $ret = false;
        if ($row && count($row) > 0) {
            $ret = true;
        }
        return $ret;
    }

    /**
     * Gets data "insert ID" from a statement
     * @param PDOStatement $ps
     * @return int|bool Inserted ID or false if there is none.
     */
    protected final function getInsertId($ps){
        $rc = $this->getUpdateCount($ps);
        $id = self::$PDO->lastInsertId();
        if ($rc > 0 and $id > 0) {
            return $id;
        } else {
            return false;
        }
    }

    /**
     * Proxy for getUpdateCount
     * @param PDOStatement $ps
     * @return int Insert count
     */
    protected final function getInsertCount($ps){
        //Alias for getUpdateCount
        return $this->getUpdateCount($ps);
    }

    /**
     * Get the number of updated rows
     * @param PDOStatement $ps
     * @return int Update Count
     */
    protected final function getUpdateCount($ps){
        $num = $ps->rowCount();
        $ps->closeCursor();
        return $num;
    }

    /**
     * Converts any form of "boolean" value to a Database usable one
     * @internal
     * @param mixed $val
     * @return int 0 or 1 (false or true)
     */
    protected final function convertBoolToDB($val){
        return $val ? 1 : 0;
    }

    /**
     * Converts a Database boolean to a PHP boolean
     * @param int $val
     * @return bool
     */
    public final static function convertDBToBool($val){
        return $val == 0 ? false : true;
    }
}