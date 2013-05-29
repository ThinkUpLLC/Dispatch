<?php
/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();

$client_queue = new \thinkup\queue\Client();

if (! isset($argv[1])  ) {
    usage();
}

$client_queue->queueCrawlJobs( array( json_decode($argv[1], true) ) );

function usage() {
    echo "\n    Usage: " . (__FILE__) . " {valid_job_json}\n\n";
    exit(256);
}