<?php
//ini_set('display_errors',1);
require_once 'application/news/frontend/includes/frontend.news.php';
$newsObj = new FrontendNews();
$list_category=$newsObj->getCategory('','arrange asc',200,true);
$LIST_CATEGORY=$list_category;
$LIST_CATEGORY_ALIAS=SystemIO::arrayToOption($LIST_CATEGORY,'id','alias');
//$list_news=$newsObj->getNews('store_home','nw_id AS id,title,description,img1,time_public,time_created,cate_id',' ( ( property & 1 ) = 1)  AND ( time_public < '.time().' )','time_public DESC','15','id');
$list_news = array();
$list_news_all2=$newsObj->getNews('store','id,title,description,time_public,cate_id,time_created,img1,img2','cate_id NOT IN (338)','time_public DESC',8,'id',true);
$list_news_all = array_merge($list_news_all2,$list_news);

foreach($list_news_all as $row)
{
	$href = Url::Link(array("id" => $row['id'],"title" => $row['title'],'cate_alias'=>$LIST_CATEGORY_ALIAS[$row['cate_id']]), "news", "congly_detail");
	$arr_news[$row['id']]['title']=$row['title'];
	$arr_news[$row['id']]['description']=$row['description'];
	$arr_news[$row['id']]['img']=IMG::thumb($row,'cnn_210x140');
	$arr_news[$row['id']]['href']=$href;
}
sort($arr_news);
header('Content-Type: application/json');
$txt=json_encode($arr_news);
echo $txt;