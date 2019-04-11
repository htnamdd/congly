<?php
ini_set('display_errors',0);
require_once('application/news/includes/define.php');
require_once 'application/news/frontend/includes/baogiay.news.php';
class Baogiay
{
	function __construct()
	{
		
	}
	function index()
	{
		
		joc()->set_file('Baogiay', Module::pathTemplate("news")."frontend".DS."baogiay.htm");	
		Page::registerFile('jquery.min.js','webskins/skins/news/js/jquery.min.js', 'header', 'js');	
		Page::registerFile('style_baogiay.css','webskins'.DS.'skins'.DS.'news'.DS.'css'.DS.'style_baogiay.css' , 'header', 'css');
		$newsObj=new BaogiayNews();
		$cate_id=SystemIO::get('cate_id','int',0);
		$page_id=SystemIO::get('page_id','int',1);
		$list_cate = $newsObj ->getListData('cate_baogiay','id,name','1=1','id DESC','0,100','id',false);
		//print_r($list_cate);
		$cate_first=current($list_cate);
		if($cate_id==0)
			$cate_id=(int)$list_cate[$cate_first['id']]['id'];
		
		joc()->set_var('cate_id_page',$cate_id);
		
		$list_filed='id,page,img,status,cate_id,time_created,user_id';
		$list_news = $newsObj ->getListData('baogiay',$list_filed,'cate_id='.$cate_id,'page ASC','0,100','page',false);
		$total_page=count($list_news);
		
		joc()->set_block('Baogiay','List_Page','List_Page');
		joc()->set_var('page_current',$page_id);
		joc()->set_var('cate_current',$list_cate[$cate_id]['name']);
		$txt_html='';
		foreach($list_news as $row)
		{
			if($row['page']==$page_id) continue;
			joc()->set_var('page',$row['page']);
			$txt_html.=joc()->output('List_Page');	
			
		}
		if($page_id==1)
			$page_show=array_shift($list_news);
		else
			$page_show=$list_news[$page_id];
	
		if($page_id < $total_page)
			joc()->set_var('page_next',$page_id+1);
		else
			joc()->set_var('page_next',$page_id);

		if($page_id > 1)
			joc()->set_var('page_pre',$page_id-1);
		else
			joc()->set_var('page_pre',$page_id);								
						
		joc()->set_block('Baogiay','Cate','Cate');
		$txt_cate='';
		foreach($list_cate as $cate)
		{
			if($cate['id'] == $cate_id) continue;
			joc()->set_var('name',$cate['name']);
			joc()->set_var('cate_id',$cate['id']);
			$txt_cate.=joc()->output('Cate');
			
		}	
		joc()->set_var('Cate',$txt_cate);	
		joc()->set_var('img','data/baogiay/'.$page_show['img']);
		joc()->set_var('List_Page',$txt_html);
		$html= joc()->output("Baogiay");
		joc()->reset_var();
		return $html;
	}
	
}
?>