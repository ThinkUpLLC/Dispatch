<?php

/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();

//$logger = \thinkup\util\Logger::get()->debug('Init JobQueueController');
 
$job_queue_ctl = new \thinkup\api\JobQueueController();

header('Content-type: application/json');
print $job_queue_ctl->execute();

