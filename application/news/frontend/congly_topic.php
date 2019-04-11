<?php
class Congly_Topic
{

	function topic_news()
	{
		//ini_set('display_errors',1);
		include_once UTILS_PATH.'convert.php';
		joc()->set_file('Topic', Module::pathTemplate()."frontend/topic.htm");
		require_once UTILS_PATH.'pagination.php';
		global $LIST_CATEGORY_ALIAS;
		global $LIST_CATEGORY;
		global $TOTAL_ROWCOUNT;
		$frontendObj=new FrontendNews();;
		$item_per_page=12;
		$page_no=SystemIO::get('page_no','int',1);
		$topic_id=SystemIO::get('topic_id','int',1);
		if ($page_no<1) $page_no=1;
		$stt=($page_no-1)*$item_per_page+1;
		$limit=(($page_no-1)*$item_per_page).','.$item_per_page;
		$list_news=$frontendObj->getNews('store','id,title,description,img2,time_public,time_created,cate_id','topic_id = '.$topic_id.' AND time_public > 0','time_public DESC',$limit);
		$list_topic = $frontendObj->getNews('topic','id,name,title,description,img,property,keyword,time_created','id = '.$topic_id,null,'0,1');
		$obj_topic = current($list_topic);
		joc()->set_var('topic_name',$obj_topic['name']);

		if($obj_topic['title'])
			Page::setHeader($obj_topic['title']	,$obj_topic['keyword']?$obj_topic['keyword']:$obj_topic['description'], $obj_topic['description']);
		else
			Page::setHeader($obj_topic['name']	,$obj_topic['keyword']?$obj_topic['keyword']:$obj_topic['description'], $obj_topic['description']);
		Page::setMeta('<meta property="og:site_name" content="Công Lý" />');
		Page::setMeta('<meta property="og:locale" content="vi_VN" />');
		Page::setMeta('<meta property="og:type" content="article" /> ');
		joc()->set_var('name',$obj_topic['name']);
		joc()->set_var('href_topic',Url::Link(array('topic_id'=>$obj_topic['id'],'name'=>$obj_topic['name']),'news','topic'));
		$totalRecord=$frontendObj->countRecord('store','topic_id = '.$topic_id,'id');
		joc()->set_block('Topic','ListRow','ListRow');
		$text_html='';
		if(count($list_news))
		{
			$k=1;
			foreach($list_news as $row)
			{
				if($row["id"] && $row['time_public'] > 0 && $LIST_CATEGORY_ALIAS[$row['cate_id']])
				{
					if($k == 1) $class=' first';
					else $class='';
					joc()->set_var('class',$class);
					joc()->set_var('title',$row['title']);
					joc()->set_var('html_title',htmlspecialchars($row['title']));
					joc()->set_var('description',SystemIO::strLeft($row['description'],300));
					joc()->set_var('img',IMG::thumb($row['time_created'],$row['img2'],'cnn_210x126'));
					joc()->set_var('href',Url::Link(array('id'=>$row['id'] ,'title'=>$row['title'],'cate_alias'=>$LIST_CATEGORY_ALIAS[$row['cate_id']]),'news','detail'));
					joc()->set_var('date',date('d/m/Y H:i',$row['time_public']));
					++$k;
					$text_html.=joc()->output("ListRow");
				}
			}
		}
		else
		{
			$text_html='<p style="margin-top: 10px; text-align: center">Không tồn tại dữ liệu</p>';
		}
		joc()->set_var('ListRow',$text_html);
		joc()->set_var('total_rowcount',$totalRecord);
		$paging = new Pagination();
		$paging->total = $totalRecord;
		$paging->per_page = $item_per_page;
		$paging->number=5;
		$paging->preview='Trang trước';
		$paging->next='Trang sau';
		$paging->page = $page_no-1;

		print_r($obj_topic);
		die;

		joc()->set_var('paging',$paging->create1_rewrite($topic_id."/".Convert::convertLinkTitle($obj_topic['name'])));
		$html= joc()->output("Topic");
		joc()->reset_var();
		return $html;
	}
	function list_topic(){
		include_once UTILS_PATH.'convert.php';
		joc()->set_file('Topic', Module::pathTemplate()."frontend/list_topic.htm");
		require_once UTILS_PATH.'pagination.php';
		$frontendObj=new FrontendNews();;
		$item_per_page=50;
		$page_no=SystemIO::get('page_no','int',1);
		$topic_id=SystemIO::get('topic_id','int',1);
		if ($page_no<1) $page_no=1;
		$stt=($page_no-1)*$item_per_page+1;
		$limit=(($page_no-1)*$item_per_page).','.$item_per_page;
		$list_topic = $frontendObj->getNews('topic','id,name,title,description,img,property,keyword,time_created','property > 0','id DESC',$limit);
		$totalRecord = $frontendObj->countRecord('topic','property > 0');
		Page::setHeader('Dách sách sự kiện trong ngày','Dách sách các sự kiện','Dách sách các sự kiện');
		Page::setMeta('<meta property="og:site_name" content="Công Lý" />');
		Page::setMeta('<meta property="og:locale" content="vi_VN" />');
		Page::setMeta('<meta property="og:type" content="article" /> ');
		joc()->set_block('Topic','ListRow','ListRow');
		$text_html='';
		if(count($list_topic))
		{
			foreach($list_topic as $row)
			{
				joc()->set_var('title',$row['name']);
				joc()->set_var('html_title',htmlspecialchars($row['name']));
				if($row['img'])
					joc()->set_var('img','data/topic/'.$row['img']);
				else
					joc()->set_var('img','webskins/skins/news/images/noimg.jpg');
				joc()->set_var('href',Url::Link(array('topic_id'=>$row['id'],'name'=>$row['name']),'news','topic'));
				$text_html.=joc()->output("ListRow");

			}
		}
		else
		{
			$text_html='<p style="margin-top: 10px; text-align: center">Không tồn tại dữ liệu</p>';
		}
		joc()->set_var('ListRow',$text_html);
		joc()->set_var('total_rowcount',$totalRecord);
		$paging = new Pagination();
		$paging->total = $totalRecord;
		$paging->per_page = $item_per_page;
		$paging->number=5;
		$paging->preview='Trang trước';
		$paging->next='Trang sau';
		$paging->page = $page_no-1;
		joc()->set_var('paging',$paging->create1_rewrite($topic_id."/".Convert::convertLinkTitle($obj_topic['name'])));
		$html= joc()->output("Topic");
		joc()->reset_var();
		return $html;
	}
	function index(){
		$cmd=SystemIO::get('cmd','def','');
		if($cmd=='list_topic')
			return $this->list_topic();
		else
			return $this->topic_news();
	}
}