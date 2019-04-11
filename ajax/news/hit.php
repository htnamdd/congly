<?php
ini_set('display_errors',0);
include 'application/news/frontend/includes/frontend.news.php';
$memcache = new Memcache;
$memcache->addServer('localhost', 11211);
$content = $memcache->get('log_hit');
function arrNewsHit($content)
{
	$a=explode(',',trim($content));
	$aa=array();
	for($i = 0 ; $i < count($a);++$i)
	{
		if(isset($aa[$a[$i]]))
			$aa[$a[$i]] = $aa[$a[$i]]+1;
		else
			$aa[$a[$i]]	= 1;
	}
	return $aa;
}

$arr_hit_news = arrNewsHit($content);
$newsObj=new FrontendNews();
foreach($arr_hit_news as $id => $hit)
{
	if((int)$id)
	{
		++$k;
		$sql="UPDATE store_hit SET hit=hit+{$hit} WHERE nw_id=".$id;
		$newsObj->updateSql($sql);
	}
}
$memcache->delete('log_hit');
echo 'Done!';


