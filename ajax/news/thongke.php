<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
require_once 'application/news/frontend/includes/frontend.news.php';
require_once 'application/main/includes/user.php';
$newsObj = new FrontendNews();
$userObj= new User();
$list_user = $userObj->getList();
$list_category=$newsObj->getCategory('','arrange asc',200,true);
$LIST_CATEGORY=$list_category;
$LIST_CATEGORY_ALIAS=SystemIO::arrayToOption($LIST_CATEGORY,'id','alias');
//$list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','1=1','time_public DESC','200','user_id');
$d=$_GET['d'];

//echo $d;
//echo '<br/>';
$time_public = strtotime($d);
/*echo date('H:i:s d-m-Y',$time_public);
echo '|';
echo date('H:i:s d-m-Y','1429368509');
echo '( editor_id LIKE "%,78,%" OR user_id =78 ) AND ( time_public > '.(int)$time_public.')';
*/
//$list_btv=array('66'=>array(),'78'=>array(),'120'=>array(),'53'=>array());//vanttt==78

$list_news_tt=$newsObj->getNews('store','id,title,time_public,time_created,user_id,censor_id,editor_id','time_public > '.$time_public.' AND time_public < '.($time_public + 86399)  ,'time_public DESC','300','id');
$list_news_van=$newsObj->getNews('store','id,title,time_public,time_created,user_id,censor_id,editor_id',' ( editor_id LIKE "%78%"  OR user_id = 78 ) AND ( time_public > '.$time_public .') AND time_public < '.($time_public +86399 ) ,'time_public DESC','30','id');
$list_news_van_tv =$newsObj->countRecord('store','editor_id LIKE "%78%" AND type_post = 3 AND time_public > '.$time_public.' AND time_public < '.($time_public + 86399));

//print_r($list_news_van);
$list_news_loan=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id',' ( editor_id LIKE "%120,%" OR user_id =120 ) AND time_public > '.$time_public.' AND time_public < '.($time_public +86399),'time_public DESC','30','id');
$list_news_thuy=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id',' ( editor_id LIKE "%66%" OR user_id =66)  AND time_public > '.$time_public.' AND time_public < '.($time_public +86399),'time_public DESC','30','id');
$list_news_thuy_tv =$newsObj->countRecord('store','editor_id LIKE "%66%" AND type_post = 3 AND time_public > '.$time_public.' AND time_public < '.($time_public + 86399));
$list_news_nhung=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','(editor_id LIKE "%53%" OR user_id =53 ) AND time_public > '.$time_public.' AND time_public < '.($time_public + 86399),'time_public DESC','30','id');
//print_r($list_news_thuy);
echo 'Tổng bài đã xuất bản: '.count($list_news_tt).'<br/>';
echo 'BTV Vân '.count($list_news_van).' Bài - Biên tập '.$list_news_van_tv.' bài của PV, '.(count($list_news_van)-$list_news_van_tv).' KTV(TH) <br/>';
$i = 1;
foreach ($list_news_van as $row)
    {
        //echo '----'.$i.'. '.$row['title'].' ('.$list_user[$row['user_id']]['user_name'].')<br/>';
        ++$i;
    }
echo 'BTV Thủy '.count($list_news_thuy).' Bài - Biên tập '.$list_news_thuy_tv.' bài của PV, '.(count($list_news_thuy)-$list_news_thuy_tv).' KTV(TH)<br/>';
$k = 1;
foreach ($list_news_thuy as $row)
    {
    
        //echo '----'.$k.'. '.$row['title'].' ('.$list_user[$row['user_id']]['user_name'].')<br/>';
        ++$k;
    }
echo 'KTV Loan '.count($list_news_loan).' Bài<br/>';
echo 'KTV Nhung '.count($list_news_nhung).' Bài<br/>';
