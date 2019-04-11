<?php
ini_set('display_errors',0);
include 'application/news/frontend/includes/frontend.news.php';
$memcache = new Memcache;
$memcache->addServer('localhost', 11211);
$id= SystemIO::get('nw_id','int',0);
$ip_client = $_SERVER['HTTP_X_REAL_IP'];
$key_memcache = $ip_client.'.'.$id;
if($memcache->get($key_memcache) || $id == 0 || $ip_client == '42.114.142.136'){
	echo 'Exits:'. $memcache->get($key_memcache);
	return false;
}
$memcache->set($key_memcache,$id,MEMCACHE_COMPRESSED,60);
$memcache->set('log_hit',$memcache->get('log_hit').','.$id);
echo 'Done!';
