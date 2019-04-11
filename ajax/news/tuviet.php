<style>
li {
	list-style-type: decimal;
}
</style>
<?php
//ini_set('display_errors',1);
require_once 'application/news/frontend/includes/frontend.news.php';
require_once 'application/main/includes/user.php';
$newsObj = new FrontendNews();
$userObj = new User();
$list_user = $userObj->userIdToNameAll();
$list_category = $newsObj->getCategory ( '', 'arrange asc', 500, true );
$LIST_CATEGORY = $list_category;
$LIST_CATEGORY_ALIAS = SystemIO::arrayToOption ( $LIST_CATEGORY, 'id', 'alias' );
$LIST_CATEGORY_NAME = SystemIO::arrayToOption ( $LIST_CATEGORY, 'id', 'name' );


$date_s = $_GET['date_s'];
$date_e = $_GET['date_e'];
if($date_s){
	$date_s=strtotime($date_s);
	$date_e=strtotime($date_e);
}
else
{
	$date_s = strtotime(date('d-m-Y'));
	$date_e = $date_s + 86399;
	$_GET['date_s'] = date('d-m-Y');
}
$list_news = $newsObj->getNews('store', 'id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id', 'time_public > ' . $date_s . ' AND time_public < ' . $date_e, 'time_public DESC', '3000', 'id' );
$list_user_total = array();

foreach($list_user as $id=>$name) {
	foreach($list_news as $row) {
		if($id == $row['user_id'])
		{
			$list_user_total[$id]['total'] = (int)$list_user_total[$id]['total'] + 1;
			$list_user_total[$id]['title'][] = $row['title'];
			$list_user_total[$id]['cate_id'][] = $LIST_CATEGORY_NAME[$row['cate_id']];
		}
	}
}

echo 'Tống số bài xuất bản được trong ngày '.$_GET['date_s'].(isset($_GET['date_e']) ? ' đến ngày '.$_GET['date_e'] : '').' được: '.count($list_news).' Bài<br/>';
foreach($list_user_total as $user_id => $vals){
	echo '<b>'.$list_user[$user_id]['user_name'].'</b> số bài làm được: '.$vals['total'].' bài<br/>';
	$k = 1;
	foreach($vals['title'] as $index => $title) {
		echo $k.'. '.$title. ' ( '.$vals['cate_id'][$index].' )<br/>';
		++$k;
	}
	echo '<br/><br/>';
}
//print_r($list_user_total);



