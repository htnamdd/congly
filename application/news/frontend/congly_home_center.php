<?php
if (defined(IN_JOC)) die("Direct access not allowed!");

class Congly_Home_Center
{
    function index()
    {
        joc()->set_file('Congly_Home_Center', Module::pathTemplate() . "frontend/home_center.htm");
        Page::setHeader('Tin tuc moi nhat 24h Đọc báo tin tức pháp luật Công lý', 'Tin tuc, tin mới, tin tức mới nhất, tin tuc moi, tin tuc 24h, doc bao, đọc báo, báo, tin mới nhất', 'Tin tức mới nhất trong ngày, tin tức cập nhất 24h các lĩnh vực đời sống pháp luật. Đọc báo mới nhất hôm này, thông tin thời sự mới nhất trong ngày');
        //Page::registerFile('slides.jquery.js', 'webskins/skins/news/js/jquery.jcarousellite.js', 'header', 'js');
        global $list_category;
        $frontendObj = new FrontendNews();
        $news_home_all = $frontendObj->getNewsHome('*', 'time_public > 0 AND time_public <' . time(), null);
        page::setMeta('
            <meta name="dc.title" content="Tin tuc moi nhat 24h Đọc báo tin tức pháp luật Công lý" />
            <meta name="dc.source" CONTENT="http://congly.vn/">
            <meta name="dc.subject" content=" Tin tức, tin tuc, tin mới, tin tức trong ngày, đọc báo, tin tức online,tin tức 24h,pháp luật"/>
            <meta name="dc.keywords" CONTENT="Tin tuc, tin moi, tin tức mới nhất, tin tuc moi, tin tuc 24h, VN, doc bao, đọc báo, báo, tin mới nhất" />
            <meta name="dc.description" content="Tin tức mới nhất trong ngày được báo Công lý đưa tin nhanh nhất 24h hàng ngày. Đọc báo tin tức online cập nhật tin nóng thời sự pháp luật, giải trí..." />
            <meta name="dc.identifier" content="//congly.vn" /><link rel="alternate" href="http://congly.vn" hreflang="vi-vn" />'
        );
        /*Tin nổi bật trang chủ*/
        $news_home_highlights = $frontendObj->getNewsHome('*', 'time_public < ' . time() . ' AND  time_public > 0 AND property & 1 = 1', 4);
        $row = array_shift($news_home_highlights);
        joc()->set_var('href_0', Url::link_detail($row, $list_category));
        $this->showIcon($row, '0');
        joc()->set_var('title_0', $row['title']);
        joc()->set_var('html_title_0', htmlspecialchars($row['title']));
        joc()->set_var('description_0', SystemIO::strLeft(strip_tags($row['description']), 300));
        joc()->set_var('img_0', IMG::show($row));
        $k = 1;
        foreach ($news_home_highlights as $row) {
            joc()->set_var('href_' . $k, Url::link_detail($row, $list_category));
            $this->showIcon($row, $k);
            joc()->set_var('title_' . $k, $row['title']);
            joc()->set_var('html_title_' . $k, htmlspecialchars($row['title']));
            joc()->set_var('description_' . $k, SystemIO::strLeft(strip_tags($row['description']), 300));
            joc()->set_var('img_' . $k, IMG::thumb($row, 'cnn_150x100'));
            ++$k;
        }
        /*Tin tiêu điểm trang chủ*/
        $news_home_focus = $frontendObj->getNewsHome('*', 'time_public < ' . time() . ' AND  time_public > 0 AND property & 4 = 4', 12);
        joc()->set_block('Congly_Home_Center', 'Focus', 'Focus');
        $txt_focus = '';
        $l = 1;
        foreach ($news_home_focus as $row) {
            joc()->set_var('title', $row['title']);
            joc()->set_var('title_cut', SystemIO::strLeft($row['title'], 250));
            $this->showIcon($row, 'f');
            joc()->set_var('html_title', htmlspecialchars($row['title']));
            $href = Url::link_detail($row, $list_category);
            joc()->set_var('href', $href);
            $txt_focus .= joc()->output('Focus');

        }
        joc()->set_var('Focus', $txt_focus);
        /*Tin mới nhất trang chủ*/
        //$news = $frontendObj->getNews('store', '*','cate_path NOT LIKE "%,269,%" AND cate_path NOT LIKE "%,338,%" AND cate_path NOT LIKE "%,288,%"', 'time_public DESC', 7);
        $news = $frontendObj->getNewsHome('*', 'time_public < ' . time() . ' AND cate_path NOT LIKE "%,269,%" AND cate_path NOT LIKE "%,338,%" AND  time_public > 0 AND property & 2 = 2', 8);
        joc()->set_block('Congly_Home_Center', 'New', 'New');
        $txt_new = '';
        foreach ($news as $row) {
            joc()->set_var('title', $row['title']);
            joc()->set_var('title_cut', SystemIO::strLeft($row['title'], 250));
            $this->showIcon($row, 'f');
            joc()->set_var('html_title', htmlspecialchars($row['title']));
            joc()->set_var('desc', SystemIO::strLeft($row['description'], 70));
            $href = Url::link_detail($row, $list_category);
            joc()->set_var('img', IMG::thumb($row, 'cnn_210x140'));
            $number_m = round((time() - $row['time_public']) / 60);
            if($number_m / 60 > 1) $number_m = ceil($number_m/60) - 1 . ' giờ trước';
            else $number_m = $number_m.' phút trước';
            joc()->set_var('cate_name',$list_category[$row['cate_id']]['name']);
            joc()->set_var('cate_href',$list_category[$row['cate_id']]['alias']);
            joc()->set_var('number_m', $number_m);
            joc()->set_var('href', $href);
            $txt_new .= joc()->output('New');
        }
        joc()->set_var('New', $txt_new);
        /*Tâm điểm dư luận - góc nhìn*/
        $list_goc_nhin = $frontendObj->getNews('store', 'id,cate_id,description,title,time_created,img1,time_public', 'cate_id = 359', 'time_public DESC', '1');
        foreach ($list_goc_nhin as $row) {
            joc()->set_var('title_gocnhin', $row['title']);
            joc()->set_var('html_title_gocnhin', htmlspecialchars($row['title']));
            joc()->set_var('desc_gocnhin', htmlspecialchars(SystemIO::strLeft($row['description'], 100)));
            joc()->set_var('img_gocnhin', IMG::thumb($row, 'cnn_185x140'));
            joc()->set_var('href_gocnhin', Url::link_detail($row, $list_category));
        }
        /*Thời sự*/
        $this->setVarCate(269, 'ts', $news_home_all);
        /*Tòa án*/
        $this->setVarCate(338, 'ta', $news_home_all);
        /*Xã hội*/
        $this->setVarCate(283, 'xh', $news_home_all);
        /*Pháp đình*/
        $this->setVarCate(273, 'pd', $news_home_all);
        /*Pháp luật*/
        $this->setVarCate(288, 'pl', $news_home_all);
        /*Thế giới*/
        $this->setVarCate(303, 'tg', $news_home_all);
        /*Sức khỏe*/
        $this->setVarCate(384, 'sk', $news_home_all);
        /*Giáo dục*/
        $this->setVarCate(386, 'gd', $news_home_all);
        /*Kinh doanh*/
        $this->setVarCate(298, 'kd', $news_home_all);
        /*Thể thao*/
        $this->setVarCate(314, 'tt', $news_home_all);
        /*Công nghệ*/
        $this->setVarCate(307, 'cn', $news_home_all);
        /*Nhắc tin*/
        $this->setVarCate(292, 'nt', $news_home_all);
        /*Nhắc tin*/
        $this->setVarCate(398, '24h', $news_home_all);
        /*Bạn đọc*/
        $this->setVarCate(414, 'bd', $news_home_all,200);
        /*Công lý 24h*/
        //$this->setVarCateOther('24h.congly.vn');
        /*Truyền hình Công lý*/
        $this->setVarCateOther('tv.congly.com.vn');
        $this->setVarCateOther('tv.congly.vn.toaan');
        /*Giải trí*/
        $this->setVarCate(293, 'gt', $news_home_all);
        /*Dau tu*/
        $this->setVarCate(416, 'dt', $news_home_all);
        /*BDS*/
        $this->setVarCate(300, 'bds', $news_home_all);
        /*Đọc nhiều nhất*/
        joc()->set_block('Congly_Home_Center', 'Hit', 'Hit');
        $list_hit = $frontendObj->getNews('store_hit', 'nw_id,hit', 'cate_path NOT LIKE "%,269,%" AND time_created >' . (time() - 3 * 86400), 'hit DESC', '8');
        $news_ids = '';
        foreach ($list_hit as $_tmp) {
            $news_ids .= $_tmp['nw_id'] . ',';
        }
        $news_ids = trim($news_ids, ',');
        $list_news_hit = array();
        if ($news_ids) {
            $list_news_hit = $frontendObj->getNews('store', 'id,cate_id,title,time_created,img1,time_public', 'id IN (' . $news_ids . ')', 'time_public DESC', '5');
        }
        $txt_hit = '';
        foreach ($list_news_hit as $row) {
            joc()->set_var('title_hit', $row['title']);
            joc()->set_var('html_title_hit', htmlspecialchars($row['title']));
            joc()->set_var('img_hit', IMG::thumb($row, 'cnn_150x100'));
            joc()->set_var('href_hit', Url::link_detail($row, $list_category));
            $txt_hit .= joc()->output('Hit');
        }

        joc()->set_var('Hit', $txt_hit);
        global $linkEsn;
        joc()->set_var('link_esn', $linkEsn);
        $html = joc()->output("Congly_Home_Center");
        joc()->reset_var();
        return $html;
    }

    public function setVarCate($cate_id, $prefix, $news_home_all, $length_desc = 200)
    {
        global $list_category;
        $arr_news_cate = $this->getNewsCate($cate_id, $news_home_all);
        ksort($arr_news_cate);
        joc()->set_var('cate_child_' . $prefix, $this->showListCateChild($cate_id));
        $k = 1;
        foreach ($arr_news_cate as $row) {
            joc()->set_var('href_' . $prefix . $k, Url::link_detail($row, $list_category));
            joc()->set_var('title_' . $prefix . $k, $row['title']);
            joc()->set_var('desc_' . $prefix . $k, SystemIO::strLeft(strip_tags($row['description']), $length_desc));
            $this->showIcon($row, $prefix . $k);
            joc()->set_var('html_title_' . $prefix . $k, htmlspecialchars($row['title']));
            if ($cate_id == 293) // Giải trí
            {
                if ($k == 1 || $k == 2) {
                    joc()->set_var('img_' . $prefix . $k, IMG::thumb($row, 'cnd_220x315'));
                } else {
                    joc()->set_var('img_' . $prefix . $k, IMG::thumb($row, 'cnn_150x100'));
                }
            } else {
                if ($k == 1) {
                    joc()->set_var('img_' . $prefix . $k, IMG::show($row));
                } else {
                    joc()->set_var('img_' . $prefix . $k, IMG::thumb($row, 'cnn_150x100'));
                }
            }
            ++$k;
        }
    }

    public function setVarCateOther($prefix, $length_desc = 200)
    {
        if ($prefix == 'tv.congly.com.vn') {
            $memcache = new Memcache;
            $memcache->addServer('localhost', 11211);
            $arr_news_cate = json_decode($memcache->get('tv.congly.com.vn'),true);
            $k = 1;
            foreach ($arr_news_cate as $row) {
                joc()->set_var('href_' . $prefix . $k, $row['url_detail']);
                joc()->set_var('title_' . $prefix . $k, $row['title']);
                joc()->set_var('desc_' . $prefix . $k, SystemIO::strLeft($row['introtext'], $length_desc));
                joc()->set_var('html_title_' . $prefix . $k, htmlspecialchars($row['title']));
                joc()->set_var('img_' . $prefix . $k, $row['img_url']);
                ++$k;
            }
        } elseif ($prefix == 'tv.congly.vn.toaan') {
            /*
            $memcache = new Memcache;
            $memcache->addServer('localhost', 11211);
            $arr_news_cate = json_decode($memcache->get('tv.congly.com.vn.toaan'),true);
            */

            $arr_news_cate = json_decode(file_get_contents('http://tv.congly.vn/widget/cat'), true);
            $k = 1;
            foreach ($arr_news_cate as $row) {
                joc()->set_var('href_' . $prefix . $k, $row['url_detail']);
                joc()->set_var('title_' . $prefix . $k, $row['title']);
                joc()->set_var('desc_' . $prefix . $k, SystemIO::strLeft($row['introtext'], $length_desc));
                joc()->set_var('html_title_' . $prefix . $k, htmlspecialchars($row['title']));
                joc()->set_var('img_' . $prefix . $k, $row['img_url']);
                ++$k;
            }
        }

    }

    public function showIcon($row, $var)
    {
        joc()->set_var('icon_video_' . $var, $row['is_video'] ? ' <img src="webskins/skins/news/images/video.gif" />' : '');
        joc()->set_var('icon_img_' . $var, $row['is_video'] ? ' <img src="webskins/skins/news/images/image.gif" />' : '');
        $time = time() - $row['time_public'];
        joc()->set_var('icon_new_' . $var, $time < 18000 ? ' <img src="webskins/skins/news/images/new2.gif" />' : '');
    }

    public function showListCateChild($cate_id)
    {
        global $list_category;
        $array_cate_child = array();
        $txt_list_cate_child = '';
        $k = 0;
        foreach ($list_category as $_tmp) {
            if ($_tmp['cate_id1'] == $cate_id && (($_tmp['property'] & 1) == 1)) {
                $array_cate_child[] = $_tmp;
                ++$k;
            }
        }
        if ($k == 0) return $txt_list_cate_child;

        $j = 0;
        foreach ($array_cate_child as $_tmp) {
            $txt_list_cate_child .= '<li><a class="title13" title="' . $_tmp['name'] . '" href="/' . $_tmp['alias'] . '/">' . $_tmp['name'] . '</a> / </li>';
            if ($cate_id == 298 && $j == 3) break;
            ++$j;
            if ($j > 4) break;
        }

        return '<ul>' . $txt_list_cate_child . '</ul>';
    }

    public function getNewsCate($cate_id, $news_home_all)
    {
        global $list_category;
        $arr_news_cate = array();
        $k = 2;
        $l = 0;
        foreach ($news_home_all as $_tmp) {
            if (($list_category[$_tmp['cate_id']]['cate_id1'] == $cate_id || $_tmp['cate_id'] == $cate_id) && (($_tmp['property'] & 1) != 1)) {
                if ((($_tmp['property'] & 8) == 8) && ($l == 0) && ($_tmp['time_public'] > (time() - 86400 * 7))) {
                    $arr_news_cate['1'] = $_tmp;
                    ++$l;
                } else {
                    $arr_news_cate[$k] = $_tmp;
                    ++$k;
                }
                if ($k >10) break;
            }
        }
        if (!isset($arr_news_cate['1'])) {
            for ($j = 2; $j < count($arr_news_cate); ++$j) {
                $arr_news_cate[$j - 1] = $arr_news_cate[$j];
            }
        }
        return $arr_news_cate;
    }
}