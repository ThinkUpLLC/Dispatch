<?php

/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();

$monitor_ctl = new \thinkup\api\MonitorController();
header('Content-type: application/json');
print $monitor_ctl->execute();