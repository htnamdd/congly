<?php
//ini_set('display_errors',1);
require_once(APPLICATION_PATH . 'news'. DS . 'frontend' . DS . 'includes' . DS . 'frontend.news.php');
require_once UTILS_PATH.'pagination.php';
class Congly_News
{
	function __construct()
	{
	}

	function showIcon($row,$var){

		joc()->set_var('icon_video_'.$var,$row['is_video'] ? ' <img src="webskins/skins/news/images/video.gif" />' :'');
		joc()->set_var('icon_img_'.$var,$row['is_video'] ? ' <img src="webskins/skins/news/images/image.gif" />' :'');
		joc()->set_var('icon_new_'.$var,date('d/m/Y',$row['time_public']) ==date('d/m/Y',time()) ? ' <img src="webskins/skins/news/images/new1.gif" />' : '');
	}
	function index(){
		joc()->set_file('Congly_News', Module::pathTemplate('news')."frontend".DS."congly_news.htm");
		$frontendObj=new FrontendNews();
		global $list_category;
		$type=SystemIO::get('type','int',1);
		$page_no = SystemIO::get('page_no', 'int', 0);
		$item_per_page=25;
		if ($page_no < 1) $page_no=1;
		if($page_no ==1){
			$limit= '0,25';
		}
		else
		{
			$item_start=($page_no-1)*$item_per_page;
			$limit=$item_start.','.$item_per_page;
		}
		$ga = '';
		if($type==1)
		{
			Page::setHeader('24h - tin tức 24h mới nhất trong ngày, tin 24h hay nhất hôm nay','Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất','Tin tuc 24h mới nhất trong ngày, tổng hợp đầy đủ tin tức 24h trong nước và Thế giới, những tin tức mới nhất trong 24h qua, sự kiện nổi bật trên Báo congly.com.vn');
			$list_news=$frontendObj->getNews('store','*','time_public > 0 AND cate_path NOT LIKE "%,269,%"','time_public DESC',$limit);
			$totalRecord=$frontendObj->countRecord('store','time_public > 0 AND cate_path NOT LIKE "%,269,%"');
			$ga= "<script>
				    ga('create', 'UA-53202552-19', 'congly.com.vn', {'name': 'newTracker'});
				    ga('newTracker.send', 'pageview');
				  </script>";
		}
		elseif($type==2)
		{
			Page::setHeader('Tin nhanh 24h - Đưa tin nhanh nhất, mới nhất trong ngày','Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất','Tin tức mới nhất trong ngày, tin tức cập nhất 24h các lĩnh vực đời sống pháp luật. Đọc báo mới nhất hôm này, thông tin thời sự mới nhất trong ngày');
			$list_news=$frontendObj->getNews('store','*','time_public > 0 AND cate_path NOT LIKE "%,269,%" AND time_public > '.(time()-2*60*60),'time_public DESC',$limit);
			$totalRecord=$frontendObj->countRecord('store','time_public > 0 AND cate_path NOT LIKE "%,269,%" AND time_public > '.(time()-2*60*60));
			$ga= "<script>
					ga('create', 'UA-53202552-20', 'congly.com.vn', {'name': 'newTracker'});
					ga('newTracker.send', 'pageview');
				</script>";
		}

		elseif($type==3)
		{
			Page::setHeader('Tin tuc - Tin tức mới nhất trong ngày trong nước và thế giới','Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất','Tin tuc - Đọc Báo Công Lý cập nhật tin tức mới nhất 24h qua, tin tức sự kiện trong ngày,tin tức trong nước, tin tức thế giới về tất cả các lĩnh vực đời sống xã hội.');
			$list_news=$frontendObj->getNews('store_home','nw_id as id, title,time_public,time_created,img1,description,cate_id,cate_path,is_img,is_video','cate_path NOT LIKE "%,269,%"','time_public DESC',$limit);
			$totalRecord=$frontendObj->countRecord('store_home','cate_path NOT LIKE "%,269,%"');
			$ga= "<script>
				ga('create', 'UA-53202552-19', 'congly.com.vn', {'name': 'newTracker'});
				ga('newTracker.send', 'pageview');
			</script>";
		}
        elseif($type==4)
        {
            Page::setHeader('Tin tức được đọc nhiều nhất','Tin tức được đọc nhiều nhất','Tin tức được đọc nhiều nhất');
            $list_news=$frontendObj->getNews('store','*','is_read_most = 1 AND time_public > 0','time_public DESC','0,30');
            $totalRecord= '30';
            $ga= "<script>
				ga('create', 'UA-53202552-19', 'congly.com.vn', {'name': 'newTracker'});
				ga('newTracker.send', 'pageview');
			</script>";
        }

		joc()->set_var('ga',$ga);

		joc()->set_block('Congly_News','ListRow','ListRow');
		$txt_html='';
        $k=1;
		foreach($list_news as $row)
		{

    		joc()->set_var('title',$row['title']);
    		$this->showIcon($row,'3');
    		joc()->set_var('html_title',htmlspecialchars($row['title']));
    		joc()->set_var('public',date('H:i d/n/Y',$row['time_public']));
    		joc()->set_var('description',SystemIO::strLeft(strip_tags($row['description']),250,''));
    		joc()->set_var('href',Url::link_detail($row,$list_category));
    		$img = IMG::Thumb($row,'cnn_185x140');
    		joc()->set_var('img',$img);
    		$txt_html.=joc()->output('ListRow');
          	++$k;
		}
		joc()->set_var('ListRow',$txt_html);
		$this->news('Congly_News');
		$paging = new Pagination();
		$paging->total = $totalRecord;
		$paging->per_page = $item_per_page;
		$paging->number=2;
		$paging->preview='Trang trước';
		$paging->next='Trang sau';
		$paging->page = $page_no-1;

		if($type==1)
			joc()->set_var('paging',$paging->create1_rewrite('tin-tuc-24h'));
		elseif($type==2)
			joc()->set_var('paging',$paging->create1_rewrite('tin-nhanh'));
		elseif($type==3)
			joc()->set_var('paging',$paging->create1_rewrite('tin-tuc'));
		else
            joc()->set_var('paging','');

		$html= joc()->output("Congly_News");
		joc()->reset_var();
		return $html;

	}
	public function news($hander){
		$frontendObj = new FrontendNews();
		global $list_category;
		//Các tin mới cùng danh mục
		$newsest = $frontendObj->getNews("store", "id,title,cate_id,img1,time_public,time_created", null, "time_public DESC", "0,8", "id", true);
		joc()->set_block($hander, 'Other', 'Other');
		$html_newest = '';
		if (count($newsest) > 0) {
			foreach ($newsest as $key => $n) {

				$link = Url::link_detail($n, $list_category);
				$img = IMG::thumb($n, 'cnn_150x100');
				joc()->set_var('other_link', $link);
				joc()->set_var('other_title', $n['title']);
				joc()->set_var('other_html_title', htmlspecialchars($n['title']));
				joc()->set_var('other_img', $img);
				joc()->set_var('other_date', date('d/n', $n['time_public']));
				$html_newest .= joc()->output('Other');
			}
		}
		joc()->set_var('Other', $html_newest);
		/*Cate change*/
		$cate_change = array(
			'269' => array('273', '288'),
			'273' => array('269', '283'),
			'279' => array('288', '273'),
			'288' => array('279', '269'),
			'283' => array('293', '273'),
			'293' => array('283', '298'),
			'298' => array('288', '279'),
			'352' => array('349', '298'),
			'349' => array('283', '283'),
			'314' => array('307', '250'),
			'350' => array('314', '288'),
			'303' => array('307', '273'),
			'307' => array('303', '283'),
			'341' => array('348', '303'),
			'348' => array('341', '307'),
			'338' => array('359', '283'),
			'359' => array('338', '288'),
			'1' => array('288', '298')
		);

		$cate_id_parent = array_rand($cate_change,1);
		$cate_show = $cate_change[$cate_id_parent];
		$list_news_cate1 = $frontendObj->getNews('store', 'id,title,time_public,cate_id,img1,time_created,is_video,is_img', 'cate_path LIKE "%,' . $cate_show['0'] . ',%" AND time_public > 0', 'time_public DESC', '0,6');
		$list_news_cate2 = $frontendObj->getNews('store', 'id,title,time_public,cate_id,img1,time_created,is_video,is_img', 'cate_path LIKE "%,' . $cate_show['1'] . ',%" AND time_public > 0', 'time_public DESC', '0,6');
		joc()->set_var('cate1_change_name', $list_category[$cate_show['0']]['name']);
		joc()->set_var('cate1_change_href', $list_category[$cate_show['0']]['alias']);
		$k = 1;
		foreach ($list_news_cate1 as $row) {
			joc()->set_var('title_cate1' . $k, $row['title']);
			joc()->set_var('html_title_cate1' . $k, htmlspecialchars($row['title']));
			joc()->set_var('href_cate1' . $k, Url::link_detail($row, $list_category));
			joc()->set_var('img_cate1' . $k, IMG::thumb($row, 'cnn_185x140'));
			++$k;
		}

		joc()->set_var('cate2_change_name', $list_category[$cate_show['1']]['name']);
		joc()->set_var('cate2_change_href', $list_category[$cate_show['1']]['alias']);
		$k = 1;
		foreach ($list_news_cate2 as $row) {
			joc()->set_var('title_cate2' . $k, $row['title']);
			joc()->set_var('html_title_cate2' . $k, htmlspecialchars($row['title']));
			joc()->set_var('href_cate2' . $k, Url::link_detail($row, $list_category));
			joc()->set_var('img_cate2' . $k, IMG::thumb($row, 'cnn_185x140'));
			++$k;
		}
	}


}
?>