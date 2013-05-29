<?php
/**
 *
 * lib/web/CrawlDispatcherController.php
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
 * Our Parent/Root web API controller
 * 
 * LICENSE:
 * @license http://www.gnu.org/licenses/gpl.html
 * @copyright 2013 Mark Wilkie
 * @author Mark Wilkie <mwilkie[at]gmail[dot]com>
 *
 */

namespace thinkup\api;

use \thinkup\util\Logger as LOG;

class CrawlDispatcherController extends \thinkup\DispatchParent {

    /**
     * HTTP status message mapping
     */
    private static $status_codes = array (
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended'
    );

    /**
     * @return String html or json body
     */
    public function execute() {
        $auth_status = $this->auth();
        $output = '';
        if(isset($auth_status['error'])) {
            $this->header('HTTP/1.0 401 Unauthorized');
            $output = json_encode($auth_status);
            LOG::get()->debug('Invalid Auth');
        } else {
            $output = json_encode($this->auth_execute());
        }
        return $output;
    }

    /**
     * Our main auth_execute stub, should be overridden by child class
     * @return json message
     */
    public function auth_execute() {
        return array("message" => "This method should be overridden by a child class");
    }

    /**
     * Our parent class auth mechanism
     */
    private function auth() {
        $bad_auth = array('error' => 'Invalid Auth');
        if(! isset($_POST['auth_token']) && ! isset($_GET['auth_token']) ) {
            return $bad_auth;
        } else {
            $auth_token = (isset($_POST['auth_token'])) ? $_POST['auth_token'] : $_GET['auth_token'];
            if($auth_token != $this->config('API_AUTH_TOKEN')) {
                return $bad_auth;
            } else {
                return array("message" => "Valid Auth");
            }
        }
    }

    /**
     * Sets an http header
     * @param String A http header
     */
    public function header($header) {
        if(! headers_sent() ) {
            header($header);
        }
    }

    /**
     * Sets an http header status via status code
     * @param INt http header status
     */
    public function header_status($header_status, $message = '') {
        if(! headers_sent() ) {
            $status_message = '';
            if(isset(self::$status_codes[$header_status])) {
               $status_message = "$header_status " . self::$status_codes[$header_status];
            }  else {
                die("Invalid status:  $header_status");
            }
            header('HTTP/1.0 ' . $status_message);
        }
        $message = (isset($message)) ? $message : "Status: $header_status";
        return array('message' => $message);
    }
}