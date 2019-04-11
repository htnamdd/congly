<?php
require_once 'application/news/frontend/includes/frontend.news.php';
class Toasoan
{
	function __construct()
	{

	}
	public function index()
	{
		$cmd = SystemIO::get('cmd','def','toasoan');
		if($cmd == 'toasoan')
			return $this->toasoan ();
        if($cmd == 'weather')
            return $this->weather();
        if($cmd == 'giavang')
            return $this->giavang();

		else
			return $this->giavang();
	}
	public function weather(){
        joc()->set_file('weather', Module::pathTemplate('news')."frontend".DS."weather.htm");
        $this->news('weather');
        $html= joc()->output("weather");
        joc()->reset_var('weather');
        return $html;
    }
    public function giavang(){
        joc()->set_file('giavang', Module::pathTemplate('news')."frontend".DS."giavang.htm");
        $this->news('giavang');
        $html= joc()->output("giavang");
        joc()->reset_var('giavang');
        return $html;
    }

	public function toasoan()
	{
		joc()->set_file('Toasoan', Module::pathTemplate('news')."frontend".DS."toasoan.htm");
		Page::setHeader('Tin tuc moi nhat 24h Đọc báo tin tức pháp luật Công lý','Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất','Tin tức mới nhất trong ngày, tin tức cập nhất 24h các lĩnh vực đời sống pháp luật. Đọc báo mới nhất hôm này, thông tin thời sự mới nhất trong ngày');
        $this->news('Toasoan');
		$html= joc()->output("Toasoan");
		joc()->reset_var('Toasoan');
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
            '348' => array('341', '307'),
            '338' => array('359', '283'),
            '359' => array('338', '288'),
            '1' => array('288', '293')
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