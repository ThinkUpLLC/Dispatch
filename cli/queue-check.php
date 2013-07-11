<?php
/* include DispatchParent */
require_once substr(dirname(__FILE__), 0, -4) . '/lib/DispatchParent.php';
\thinkup\DispatchParent::init();

$monitor = new \thinkup\queue\Monitor();
$monitor_ctl = new \thinkup\api\MonitorController();

$queue_status = $monitor->getStatus();
if(! $monitor_ctl->nagiosCheck($queue_status)) {
    // our queue or workers are not running, email
    $mail_header = "From: \"Queue Monitor\" <notifications@thinkup.com>\r\n";
    $mail_header .= "X-Mailer: PHP/".phpversion();
    $message = '';
    if($queue_status == null) {
        $message = "\n[ERROR] Gearman server not running";
    } else {
        $message = "\n[ERROR] Not enough workers running\n\n" . print_r($queue_status, true);
    }
    #echo $message . "\n\n";
    mail($monitor->config('ALERT_EMAILS'), "[FAIL] Queue Monitor", $message, $mail_header);
}