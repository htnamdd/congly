<?php
//ini_set('display_errors', 1);
include_once UTILS_PATH . 'convert.php';
require_once UTILS_PATH . 'pagination.php';

class News_Search
{
    function __construct()
    {
    }

    function index()
    {
        joc()->set_file('News_Search', Module::pathTemplate() . "frontend" . DS . "news_search.htm");
        global $list_category;
        $frontendObj = new FrontendNews();
        $item_per_page = 20;
        $page_no = SystemIO::get('page_no', 'int', 1);
        if ($page_no < 1) $page_no = 1;
        $limit = (($page_no - 1) * $item_per_page) . ',' . $item_per_page;
        $q = trim(SystemIO::get('q', 'def', ''), '/');

        $topic_id = SystemIO::get('topic_id', 'int', '0');
        if ($q) {
            $navigation = '<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><h4 class="bar_title"><a itemprop="url"  class="bar_title" href="http://congly.com.vn" title="Trang chủ, Báo Công lý"><span itemprop="title">Trang chủ</span></a></h4></div><div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><h1><a itemprop="url"  href="/tag/' . $q . '/" title="' . trim($q, '/') . '"><span class="brc" itemprop="title" >' . str_replace(array('_'), array(' '), trim($q, '/')) . '</span></a></h1></div>';
            joc()->set_var('navi', $navigation);
        }
        $tag_unikey = $temp_k = str_replace(array('"', "'"), array('', ''), trim($q, '/'));
        
        if ($topic_id) {
            $topic = current($frontendObj->getNews('topic', '*', 'id=' . $topic_id));
            $navigation = '<div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><h4 class="bar_title"><a itemprop="url"  class="bar_title" href="/su-kien-trong-ngay/" title="su kien trong ngay"><span itemprop="title">Sự kiện</span></a></h4></div><div itemscope="" itemtype="http://data-vocabulary.org/Breadcrumb"><h1 style="font-size:14px; display:block;margin-top:0px"><a itemprop="url"  href="' . Url::Link(array('topic_id' => $topic['id'], 'name' => $topic['name']), 'news', 'topic') . '"><span class="brc" itemprop="title" >' . $topic['name'] . '</span></a></h1></div>';
            joc()->set_var('navi', $navigation);
            $list_search = $frontendObj->getNews('store', 'id,title,description,img1,time_public,time_created,cate_id', 'topic_id=' . $topic_id . ' AND time_public > 0', 'time_public DESC', $limit, 'id');
            $total_record = $frontendObj->countRecord('store', 'topic_id=' . $topic_id);
            Page::setHeader($topic['title'] ? $topic['title'] . ' Trang -' . $page_no : $topic['name'] . ' Trang -' . $page_no, $topic['keyword'], SystemIO::strLeft($topic['description'],160));
            joc()->set_var('content_topic', $topic['content']);
            $tag_unikey = $topic['name'];
        } else {
            joc()->set_var('content_topic', '');
            $wh = 'tag LIKE "%' . $temp_k . '%"';
            $list_search = $frontendObj->getNews('store', 'id,title,tag,cate_id,img1,description,time_created,time_public', $wh, 'time_public DESC', $limit);
            $total_record = $frontendObj->countRecord('store', $wh);
            /*Get once row*/
            if (count($list_search)) {
                $one = current($list_search);
                $tag_data = explode('[]', $one['tag']);
                $tag_data_uniky = explode(',', $tag_data['0']);
                foreach ($tag_data_uniky as $key => $value) {
                    if (Convert::convertLinkTitle($value) == $tag_unikey || $value == $tag_unikey) {
                        $tag_unikey = $value;
                    }
                }
            }

            Page::setHeader(ucfirst(trim(trim(str_replace(array('_','-'), array(' ',' '), $q), '/')) . ' - Tin tức ' . trim(str_replace('_', ' ', $q), '/')) . ' mới nhất 24h', 'Báo Công lý cập nhật tin tức mới nhất về ' . trim(str_replace('-', ' ', $q), '/') . ' nhanh chóng chính xác nhất về chủ đề '.trim(str_replace('-', ' ', $q), '/'), 'Báo Công lý cập nhật tin tức mới nhất, ' . trim(str_replace('-', ' ', $q), '/') . ' nhanh chóng, chính xác nhất về chủ đề '.trim(str_replace('-', ' ', $q), '/').' trong 24h qua');
        }


        if (SystemIO::get('tag')) {
            $tag_name = str_replace(array('tag', '/'), array('', ''), trim(SystemIO::get('q', 'def', ''), '/'));
            $meta = current($frontendObj->getNews('tag_meta', 'id,tag,meta', 'tag="' . $tag_name . '"'));
            $title_keyword_desc = explode('||', $meta['meta']);
            if ($title_keyword_desc['0']) {
                Page::setHeader($title_keyword_desc['0'], $title_keyword_desc['1'], $title_keyword_desc['2']);
            }
        }
        joc()->set_var('url_tag',$_SERVER['REQUEST_URI']);
        //joc()->set_var('q', strtolower($tag_unikey));
        joc()->set_var('q', '');
        joc()->set_block('News_Search', 'Row', 'Row');
        $text_html = '';
        if (count($list_search)) {
            foreach ($list_search as $row) {
                if ($row['id'] && $row['time_public'] > 0) {
                    joc()->set_var('title', $row['title']);
                    joc()->set_var('html_title', htmlspecialchars($row['title']));
                    joc()->set_var('description', SystemIO::strLeft($row['description'], 300));
                    joc()->set_var('img', IMG::thumb($row, 'cnn_225x150'));
                    joc()->set_var('href', Url::link_detail($row, $list_category));
                    joc()->set_var('public', date('d/m/Y H:i', $row['time_public']));
                    $text_html .= joc()->output("Row");
                }
            }
        } else {
            $text_html = '<p style="margin-top: 10px; text-align: center">Không có kết quả phù hợp với từ khóa: <strong>' . str_replace(array('<', '>', '/'), array('&lt', '&gt', ''), ucfirst($tag_unikey)) . '</strong>.</p>';
        }
        joc()->set_var('Row', $text_html);
        global $TOTAL_ROWCOUNT;
        joc()->set_var('total_row', $TOTAL_ROWCOUNT ? $TOTAL_ROWCOUNT : $total_record);
        $paging = new Pagination();
        $paging->total = $total_record;
        $paging->per_page = $item_per_page;
        $paging->page = $page_no - 1;
        if ($topic_id) {
            joc()->set_var('paging', $paging->create1_rewrite($topic_id . "/" . Convert::convertLinkTitle($topic['name'])));
        } else {
            joc()->set_var('paging', $paging->create1_rewrite("tag/" . urlencode(trim($temp_k, "/"))));
        }
        /*Tin tức mới nhất*/
        $newsest = $frontendObj->getNews("store", "id,title,cate_id,img1,time_public,time_created", null, "time_public DESC", "0,8", "id", true);
        joc()->set_block('News_Search', 'Other', 'Other');
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
            '288' => array('314', '269'),
            '283' => array('293', '273'),
            '293' => array('283', '298'),
            '298' => array('288', '273'),
            '352' => array('349', '298'),
            '349' => array('283', '273'),
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

        $html = joc()->output("News_Search");
        joc()->reset_var();
        return $html;
    }
}

?>