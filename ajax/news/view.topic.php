<?php
require_once(APPLICATION_PATH . 'news'. DS . 'frontend' . DS . 'includes' . DS . 'frontend.news.php');
//ini_set('display_errors',1);
$newsObj= new FrontendNews();
$cate_id=SystemIO::post('cate_id','int');
$page=SystemIO::post('page','int',1);
$limit=4*$page .',4';
if($cate_id)
	$list_topic = $newsObj->getNews('topic','*','cate_id='.$cate_id,'property DESC',$limit,'id',false);
else
	$list_topic = $newsObj->getNews('topic','*','1=1','property DESC',$limit,'id',false);	
$txt='';
if(count($list_topic)){
	foreach($list_topic as $row)
	{
		if($row['property'] == 1)
		{
			$txt.='<li class="append" style="display:none">
		              <a class="thumb" href="'.Url::Link(array('topic_id'=>$row['id'],'name'=>$row['name']),'news','topic').'"><img title="'.$row['name'].'" alt="'.$row['name'].'" src="http://img.ngoisao.vn/topic/v_63x63/'.$row['img'].'"></a>
		              <h3><a title="'.$row['name'].'" href="'.Url::Link(array('topic_id'=>$row['id'],'name'=>$row['name']),'news','topic').'">'.$row['name'].'</a></h3>
				  </li>';
		}
		
	}		
		
}
echo $txt;