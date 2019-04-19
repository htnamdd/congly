<style>
	li{ list-style-type:decimal;}
</style>
<?php
//ini_set('display_errors',1);
require_once 'application/news/frontend/includes/frontend.news.php';
require_once 'application/main/includes/user.php';
$newsObj = new FrontendNews();
$userObj= new User();
$list_user = $userObj->getList();
$list_category=$newsObj->getCategory('','arrange asc',200,true);
$LIST_CATEGORY=$list_category;
$LIST_CATEGORY_ALIAS=SystemIO::arrayToOption($LIST_CATEGORY,'id','alias');
$d=$_GET['d'];
if($_GET['d'])
$t=time()-(int)$_GET['d']*86400;
else
$t=time()-86400;	
echo $_GET['type'];
if($_GET['type']=='3')
    {
	$list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','( type_post=3 ) AND time_public > '.$t.' AND time_public < '.time(),'time_public DESC','1000','id');
    }	
elseif($_GET['type']=='1') 
   $list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','( type_post is null ) AND time_public > '.(time()-86400).' AND time_public < '.time(),'time_public DESC','200','id');
elseif	($_GET['type']=='2')
	$list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','( type_post=2 ) AND time_public > '.(time()-86400).' AND time_public < '.time(),'time_public DESC','200','id');
elseif($_GET['type']=='4')
	$list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','( type_post=4 ) AND time_public > '.(time()-86400).' AND time_public < '.time(),'time_public DESC','200','id');
else
	$list_news=$newsObj->getNews('store','id,title,description,img1,time_public,time_created,cate_id,user_id,censor_id,editor_id','( type_post=3 ) AND time_public > '.$t.' AND time_public < '.time(),'time_public DESC','500','id');		
echo '<ul>';
$k=1;
$ids='';
foreach($list_news as $row){
    
   $ids.=$row['id'].',';
}
$ids=trim($ids,',');
$list_news_hit=$newsObj->getNews('store_hit','*','nw_id IN ('.$ids.')','nw_id DESC','1000','nw_id');
foreach($list_news as $row)
{
	//if(date('d') ==date('d',$row['time_public'])){
			$href = Url::Link(array("id" => $row['id'],"title" => $row['title'],'cate_alias'=>$LIST_CATEGORY_ALIAS[$row['cate_id']]), "news", "congly_detail");
			$list_editor=explode(',',$row['editor_id']);
			$list_editor=array_unique($list_editor);
			$user_name_editor='';
			for($i = 0; $i < count($list_editor);++$i)
			$user_name_editor.=$list_user[$list_editor[$i]]['user_name'].',';
			if($_GET['btv']){
			
				if(substr_count($user_name_editor,$_GET['btv'])|| $list_user[$row['user_id']]['user_name']==$_GET['btv'])
					echo '<li><a href="'.$href.'" target="brank">'.$row['title'].'</a> - '.$list_user[$row['user_id']]['user_name'].'\\'.$user_name_editor.'\\'.$list_user[$row['censor_id']]['user_name'].' ('.date('H:i d/n/Y',$row['time_public']).')- '.$list_news_hit[$row['id']]['hit'].'</li><br/>';
			}
			else
				echo '<li><a href="'.$href.'" target="brank">'.$row['title'].'</a> - '.$list_user[$row['user_id']]['user_name'].'\\'.$user_name_editor.'\\'.$list_user[$row['censor_id']]['user_name'].' ('.date('H:i d/n/Y',$row['time_public']).')- '.$list_news_hit[$row['id']]['hit'].'</li><br/>';
			
			
	//}	
}
echo '</ul>';
