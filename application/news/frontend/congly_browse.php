<?php
ini_set('display_errors', 0);
require_once(APPLICATION_PATH . 'news' . DS . 'frontend' . DS . 'includes' . DS . 'frontend.news.php');
require_once UTILS_PATH . 'pagination.php';

class Congly_browse {

    public function __construct() {
        
    }

    public function index() {
        return $this->showCate();
    }

    public function showIcon($row, $var) {

        joc()->set_var('icon_video_' . $var, $row['is_video'] ? ' <img src="webskins/skins/news/images/video.gif" />' : '');
        joc()->set_var('icon_img_' . $var, $row['is_video'] ? ' <img src="webskins/skins/news/images/image.gif" />' : '');
        joc()->set_var('icon_new_' . $var, date('d/m/Y', $row['time_public']) == date('d/m/Y', time()) ? ' <img src="webskins/skins/news/images/new1.gif" />' : '');
    }

    public function showCate() {
        joc()->set_file('Congly_browse', Module::pathTemplate('news') . "frontend" . DS . "cate_center.htm");
        $cate_id = SystemIO::get('cate_id', 'int', 1);
        $frontendObj = new FrontendNews();
        global $list_category;
        $page_no = SystemIO::get('page_no', 'int', 0);
        $item_per_page = 25;
        if ($page_no < 1) {
            $page_no = 1;
        }
        if ($page_no == 1) {
            $limit = '0,22';
        } else {
            $item_start = ($page_no - 1) * $item_per_page;
            $limit = $item_start . ',' . $item_per_page;
        }
        $cate_id_parent = 0;
        // Navi
        if ($list_category[$cate_id]['cate_id1'] == 0) {
            joc()->set_var('cate_parent_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_parent_href', $list_category[$cate_id]['alias']);
            joc()->set_var('cate_child_name', '');
            $cate_id_parent = $cate_id;
            $display_cate_child = 'none';
            joc()->set_var('class_cate_end_1','breadcrumble_title_end');
            joc()->set_var('class_cate_end_2','');
        } else {
            $cate_id_parent = $list_category[$cate_id]['cate_id1'];
            joc()->set_var('cate_parent_name', $list_category[$cate_id_parent]['name']);
            joc()->set_var('cate_parent_href', $list_category[$cate_id_parent]['alias']);
            joc()->set_var('cate_child_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_child_href', $list_category[$cate_id]['alias']);
            joc()->set_var('class_cate_end_2','breadcrumble_title_end');
            joc()->set_var('class_cate_end_1','');
            $display_cate_child = 'block';
        }
        joc()->set_var('href_rss',$list_category[$cate_id_parent]['alias'].'.rss');
        joc()->set_var('display_cate_child', $display_cate_child);
        /*Set Cate mobile*/
        joc()->set_block('Congly_browse','Cate','Cate');
        $text_cate = '';
        foreach($list_category as $cate){
            if($cate['cate_id1'] == $cate_id_parent && ($cate['property'] & 1 == 1)){
                joc()->set_var('cate_name',$cate['name']);
                joc()->set_var('cate_href',$cate['alias']);
                $text_cate .= joc()->output('Cate');
            }

        }
        joc()->set_var('Cate',$text_cate);

        if ($page_no > 1)
            Page::setHeader($list_category[$cate_id]['title'] . '- Trang ' . $page_no, $list_category[$cate_id]['keyword'], $list_category[$cate_id]['description'] . ' Trang ' . $page_no);
        else
            Page::setHeader($list_category[$cate_id]['title'], $list_category[$cate_id]['keyword'], $list_category[$cate_id]['description']);
        // Set meta
        page::setMeta('
            <meta name="dc.title" content="Tin tuc moi nhat 24h Đọc báo tin tức pháp luật Công lý" />
            <meta name="dc.source" CONTENT="http://congly.vn/">
            <meta name="dc.subject" content="' . $list_category[$cate_id]['name'] . '"/>
            <meta name="dc.keywords" CONTENT="' . $list_category[$cate_id]['keyword'] . '" />
            <meta name="dc.description" content="' . $list_category[$cate_id]['description'] . '" />
            <meta name="dc.identifier" content="//congly.vn" /><link rel="alternate" href="http://congly.vn" hreflang="vi-vn" />'
        );

        joc()->set_var('cate_desc', $list_category[$cate_id]['title']);
        joc()->set_var('cate_desc_href', $list_category[$cate_id]['alias']);
        // Danh mục con
        if ($list_category[$cate_id]['cate_id1']) {
            $list_news = $frontendObj->getNews('store', 'id,title,description,img1,time_public,time_created,cate_id,is_video,is_img', 'cate_id=' . $cate_id, 'time_public DESC', $limit, 'id');
            $totalRecord = $frontendObj->countRecord('store', 'cate_id=' . $cate_id);
        } else {
            $list_news = $frontendObj->getNews('store', 'id,title,description,img1,time_public,time_created,cate_id,is_video,is_img', 'cate_path LIKE "%,' . $cate_id . ',%"', 'time_public DESC', $limit, 'id');
            $totalRecord = $frontendObj->countRecord('store', 'cate_path LIKE "%,' . $cate_id . ',%"');
        }
        $list_news_top = array_slice($list_news, 0, 6);
        $k = 1;
        foreach ($list_news_top as $row) {
            joc()->set_var('title_' . $k, $row['title']);
            if($k == 1)
                joc()->set_var('img_' . $k, IMG::show($row));
            else
                joc()->set_var('img_' . $k, IMG::thumb($row,'cnn_320x215'));
            joc()->set_var('html_title_' . $k, htmlspecialchars($row['title']));
            $this->showIcon($row, $k);
            joc()->set_var('description_' . $k, $row['description']);
            joc()->set_var('error_img_' . $k,  IMG::show($row));
            joc()->set_var('href_' . $k, Url::link_detail($row, $list_category));
            ++$k;
        }
        $list_news_seconde = array_slice($list_news, 6, count($list_news));
        joc()->set_block('Congly_browse', 'Row', 'Row');
        $txt_html = '';
        $k = 1;
        foreach ($list_news_seconde as $row) {
            joc()->set_var('title', $row['title']);
            $this->showIcon($row, '3');
            joc()->set_var('html_title', htmlspecialchars($row['title']));
            joc()->set_var('public', date('H:i d/n/Y', $row['time_public']));
            joc()->set_var('description', SystemIO::strLeft(strip_tags($row['description']), 250, ''));
            joc()->set_var('href', Url::link_detail($row, $list_category));
            joc()->set_var('img',IMG::thumb($row, 'cnn_320x215'));
            $txt_html .= joc()->output('Row');
            ++$k;
        }
        joc()->set_var('Row', $txt_html);
        $paging = new Pagination();
        $paging->total = $totalRecord;
        $paging->per_page = $item_per_page;
        $paging->number = 2;
        $paging->preview = 'Trang trước';
        $paging->next = 'Trang sau';
        $paging->page = $page_no - 1;
        joc()->set_var('paging', $paging->create1_rewrite(trim($list_category[$cate_id]['alias'], '/')));
        /* Liên kết danh mục */
        $cate_change = array(
            '269' => array('273', '288'),
            '273' => array('269', '283'),
            '279' => array('288', '273'),
            '288' => array('314', '273'),
            '283' => array('293', '273'),
            '293' => array('283', '298'),
            '298' => array('288', '283'),
            '352' => array('349', '298'),
            '349' => array('283', '283'),
            '314' => array('307', '283'),
            '350' => array('314', '288'),
            '303' => array('307', '273'),
            '307' => array('303', '283'),
            '341' => array('348', '303'),
            '348' => array('341', '307'),
            '338' => array('359', '283'),
            '359' => array('338', '288'),
            '1' => array('288', '293')
        );

        if (array_key_exists($cate_id_parent, $cate_change)) {
            $cate_show = $cate_change[$cate_id_parent];
        } else {
            $cate_show = $cate_change['1'];
        }

        $list_news_cate1 = $frontendObj->getNews('store', 'id,title,time_public,cate_id,img1,time_created,is_video,is_img', 'cate_path LIKE "%,' . $cate_show['0'] . ',%" AND time_public > 0', 'time_public DESC', '0,6');
        $list_news_cate2 = $frontendObj->getNews('store', 'id,title,time_public,cate_id,img1,time_created,is_video,is_img', 'cate_path LIKE "%,' . $cate_show['1'] . ',%" AND time_public > 0', 'time_public DESC', '0,6');
        joc()->set_var('cate1_change_name', $list_category[$cate_show['0']]['name']);
        joc()->set_var('cate1_change_href', $list_category[$cate_show['0']]['alias']);
        $k = 1;
        foreach ($list_news_cate1 as $row) {
            joc()->set_var('title_cate1' . $k, $row['title']);
            joc()->set_var('html_title_cate1' . $k, htmlspecialchars($row['title']));
            joc()->set_var('href_cate1' . $k, Url::link_detail($row, $list_category));
            joc()->set_var('img_cate1' . $k, IMG::thumb($row, 'cnn_320x215'));
            ++$k;
        }

        joc()->set_var('cate2_change_name', $list_category[$cate_show['1']]['name']);
        joc()->set_var('cate2_change_href', $list_category[$cate_show['1']]['alias']);
        $k = 1;
        foreach ($list_news_cate2 as $row) {
            joc()->set_var('title_cate2' . $k, $row['title']);
            joc()->set_var('html_title_cate2' . $k, htmlspecialchars($row['title']));
            joc()->set_var('href_cate2' . $k, Url::link_detail($row, $list_category));
            joc()->set_var('img_cate2' . $k, IMG::thumb($row, 'cnn_320x215'));
            ++$k;
        }

        $html = joc()->output("Congly_browse");
        joc()->reset_var();
        return $html;
    }

}

?>