<?php
require(APPLICATION_PATH . 'news'. DS . 'frontend' . DS . 'includes' . DS . 'frontend.news.php');
$frontendObj=new FrontendNews();
$id= (int)$_GET['id'];
$cate=$_GET['cate'];
if($id)
{
	
	$detail= $frontendObj->newsOne($id);
    $detail_content =$frontendObj->detail($id);
    $detail['content'] = $detail_content;
    echo  json_encode($detail);	
}
elseif($cate)
{
	$list_category=$frontendObj->getCategory();
	echo json_encode($list_category);
}
else
{
	$wh ='time_public >0  AND ( cate_path LIKE "%,269,%" OR cate_path LIKE "%,273,%" OR cate_path LIKE "%,288,%" OR cate_path LIKE "%,303,%" OR cate_path LIKE "%,283,%" OR cate_path LIKE "%,338,%")';		
	//$wh='time_public > 0';
	$list_news=$frontendObj->getNews('store','*',$wh,'time_public DESC',100);
	echo json_encode($list_news);
}