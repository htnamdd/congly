<?php
session_cache_expire(3600);
session_start();
//ini_set('display_errors',1);
$uri = $_SERVER['REQUEST_URI'];
$query_string = $_SERVER['QUERY_STRING'];
$srcipt_name = $_SERVER['SCRIPT_NAME'];

if (!isset($_GET['update_link'])) {
    if (substr_count($uri, '?') && !substr_count($uri, 'review') && !substr_count($uri, 'cms')) {
        if (substr($uri, strpos($uri, '?', 7)) != '?print=1') {
            header('Location:http://congly.vn' . substr($uri, 0, strpos($uri, '?')));
        }
    }
}

ini_set('session.gc-maxlifetime', 3600);
date_default_timezone_set('Asia/Bangkok');
include 'define.php';
include KERNEL_PATH . 'portal.php';
if (IN_DEBUG) {
    Profiler::getInstance()->mark('End script', 'run.index.php');
    echo Profiler::debug();
}

?>