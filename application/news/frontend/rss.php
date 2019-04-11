<?php
ini_set('display_errors',0);
class Rss
{
	function __construct()
	{
		
	}
	function index()
	{		
		joc()->set_file('Rss', Module::pathTemplate('news')."frontend".DS."rss.htm");	
		Page::setHeader('Tin tuc moi nhat 24h Đọc báo tin tức pháp luật Công lý','Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất','Tin tức mới nhất trong ngày, tin tức cập nhất 24h các lĩnh vực đời sống pháp luật. Đọc báo mới nhất hôm này, thông tin thời sự mới nhất trong ngày');
		$frontendObj=new FrontendNews();
		$list_category=$frontendObj->getCategory();
		$txt='<ul> +> <a href="trang-chu.rss"><strong>Trang chủ </strong></a>  <a href="trang-chu.rss">http://congly.vn/trang-chu.rss</a><br/>';
		foreach($list_category as $row)
		{
			if($row['property'] & 1 ==1)
			{
				if($row['cate_id1'] == 0)
				{
					//echo 'RewriteRule ^'.$row['alias'].'.rss?$ rss.php?cate_id='.$row['id'].' [L]<br/>';
					$txt.='+> <a href="'.$row['alias'].'.rss"><strong>'.$row['name'].'</strong></a>  <a href="'.$row['alias'].'.rss">http://congly.vn/'.$row['alias'].'.rss</a><br/>';
					$k = 0 ;
					foreach($list_category as $_temp)
					{
						if($_temp['property'] & 1)
						{
							
							if(($_temp['cate_id1'] == $row['id']) && $_temp['cate_id2'] == 0)
							{	
								//echo 'RewriteRule ^'.$_temp['alias'].'.rss?$ rss.php?cate_id='.$_temp['id'].' [L]<br/>';
								$txt.='  - <a href="'.$_temp['alias'].'.rss"><strong>'.$_temp['name'].'</strong></a>  <a href="'.$_temp['alias'].'.rss">http://congly.vn/'.$_temp['alias'].'.rss</a><br/>';
                            	++$k;    
							}
						}
					}
				}
			}
		}
		$txt.='</ul>';
		joc()->set_var('rss',$txt);
		$this->news('Rss');
		$html= joc()->output("Rss");
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
			'298' => array('288', '250'),
			'352' => array('349', '298'),
			'349' => array('283', '283'),
			'314' => array('307', '250'),
			'350' => array('314', '288'),
			'303' => array('307', '273'),
			'307' => array('303', '283'),
			'341' => array('348', '303'),
			'348' => array('293', '307'),
			'338' => array('359', '283'),
			'359' => array('338', '288'),
			'1' => array('288', '293')
		);

		$cate_id_parent = 1;
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