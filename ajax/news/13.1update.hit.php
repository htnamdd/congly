<?php
ini_set('display_errors',1);
include 'application/news/frontend/includes/frontend.news.php';
$id= SystemIO::get('nw_id','int',0);
if($id==0) return false;
$newsObj=new FrontendNews();
/*$id= SystemIO::post('nw_id','int',71298);
$t=news()->query('UPDATE store_hit SET hit=hit+1 WHERE nw_id='.$id);
echo $t;*/
function writeFile($data,$path=LOG_PATH,$mode = 'a+'){
	if ( ! $fp = @fopen($path, $mode)){
		return false;
	}
	flock($fp, LOCK_EX);
	fwrite($fp, $data);
	flock($fp, LOCK_UN);
	fclose($fp);
	return true;
}
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

writeFile($id.',',ROOT_PATH.'log/hit.log');
$file_size = filesize(ROOT_PATH.'log/hit.log');
if($file_size > 1024)
{
	$content = file_get_contents(ROOT_PATH.'log/hit.log');
	$k=0;
	$arr_hit_news = arrNewsHit($content);
	foreach($arr_hit_news as $id => $hit)
	{
		if($id)
		{
			++$k;
			$sql="UPDATE store_hit SET hit=hit+{$hit} WHERE nw_id=".$id;
			$newsObj->updateSql($sql);
		}
	}
	if($k > 0) unlink(ROOT_PATH.'log/hit.log');
}
echo json_encode(array('time'=>time()));