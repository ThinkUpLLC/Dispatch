<?php
 ini_set('error_reporting', E_ALL);
ini_set('display_errors', 'on');
 $free = disk_free_space('/mnt');
 $json = array("free" => $free, "status" => ($free < 10*1024*1024*1024)?"low":"good");
 echo json_encode($json);
?>
