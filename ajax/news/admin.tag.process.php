<?php
require_once 'application/news/backend/includes/backend.news.php';
$newsObj=new BackendNews();
$action=SystemIO::post('action','def');
$id=SystemIO::post('id','int');
switch($action)
{
	case 'add':		
		$tag_name=SystemIO::post('tag','def');
		$link = SystemIO::post('link','def');
		$meta=SystemIO::post('meta','def');
		$name=SystemIO::post('name','def');

		if($newsObj->insertData('tag_meta',array('tag'=>$tag_name,'meta'=>$meta,'link'=>$link,'name'=>$name)))
			echo 1;
		else
			echo 0;	
		break;
	case 'edit':
		$meta=SystemIO::post('meta','def');
        $name=SystemIO::post('name','def');
		$link = SystemIO::post('link','def');
		if($newsObj->updateData('tag_meta',array('meta'=>$meta,'link'=>$link,'name'=>$name),'id = '.$id))
			echo 1;
		else
			echo 0;	
		break;				
}

