<?php
/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();

$worker = new \thinkup\queue\Worker();

$worker->start();