<?php
defined ( 'IN_JOC' ) or die ( 'Restricted Access' );
$host='localhost';
$hot_reserve='localhost';
return array (
	'default'=>'db',
	'database' => array (
		'db' => array (
			'username' => 'conglyv2', 
			'password' => 'DqUM78J99A8w7Rju', 
			'host' =>$host,
			'host_reserve'=>$hot_reserve, 
			'dbname' => 'conglyv2_platform', 
			'object' => 'MySQLi'),
		'news' => array (
			'username' => 'congly', 
			'password' => 'qTKCMLdxzx7mjX7G', 
			'host' => $host, 
			'host_reserve'=>$hot_reserve,	
			'dbname' => 'congly_news', 
			'object' => 'MySQLi')
)
);
