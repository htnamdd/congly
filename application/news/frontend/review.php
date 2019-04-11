<?php
ini_set('display_errors', 0);
require_once 'application/news/frontend/includes/frontend.news.php';
require_once 'application/news/includes/comment_model.php';
require_once 'application/user/includes/comment.php';
include_once UTILS_PATH . 'convert.php';

class review
{
    function __construct()
    {
    }

    function index()
    {
        $ipConnection = base64_decode(SystemIO::get('ip'));
        $ipReview = $_SERVER['REMOTE_ADDR'];
        if ($ipConnection == '' || $ipConnection != $ipReview) {
            echo '<h1 style="text-align: center">';
            echo 'Bạn đang truy cập không hợp lệ! ';
            echo 'Click <a href="http://congly.vn">Vào đây</a> để trở về trang chủ!';
            echo '</h1>';
            die;
        }
        joc()->set_file('Detail', Module::pathTemplate() . "frontend" . DS . "detail.htm");
        global $list_category;
        $frontendObj = new FrontendNews();
        $id = SystemIO::get('id', "int", 0);
        $from = SystemIO::get('from', 'def');
        if ($from == 'store') {
            $detail = $frontendObj->newsOne($id);
            $detail ['content'] = $frontendObj->detail($id);
        } else
            $detail = $frontendObj->getNewsReview($id);

        joc()->set_var('id', $id);
        $cate_id_current = $detail['cate_id'];
        $cate_id_parent = $list_category[$cate_id_current]['cate_id1'] == 0 ? $cate_id_current : $list_category[$cate_id_current]['cate_id1'];
        $cate_id = $detail['cate_id'];
        /* Navigation */
        $cate_id_parent = 0;
        if ($list_category[$cate_id]['cate_id1'] == 0) {
            joc()->set_var('cate_parent_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_parent_href', $list_category[$cate_id]['alias']);
            joc()->set_var('cate_child_name', '');
            $cate_id_parent = $cate_id;
            $display_cate_child = 'none';
            joc()->set_var('class_cate_end_1', 'breadcrumble_title_end');
            joc()->set_var('class_cate_end_2', '');
        } else {
            $cate_id_parent = $list_category[$cate_id]['cate_id1'];
            joc()->set_var('cate_parent_name', $list_category[$cate_id_parent]['name']);
            joc()->set_var('cate_parent_href', $list_category[$cate_id_parent]['alias']);
            joc()->set_var('cate_child_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_child_href', $list_category[$cate_id]['alias']);
            joc()->set_var('class_cate_end_2', 'breadcrumble_title_end');
            joc()->set_var('class_cate_end_1', '');
            $display_cate_child = 'block';
        }
        joc()->set_var('display_cate_child', $display_cate_child);
        joc()->set_var('href_rss', $list_category[$cate_id_parent]['alias'] . '.rss');
        /*Set Cate mobile*/
        joc()->set_block('Detail', 'Cate', 'Cate');
        $text_cate = '';
        foreach ($list_category as $cate) {
            if ($cate['cate_id1'] == $cate_id_parent && $cate['property'] & 1) {
                joc()->set_var('cate_name', $cate['name']);
                joc()->set_var('cate_href', $cate['alias']);
                $text_cate .= joc()->output('Cate');
            }

        }
        joc()->set_var('Cate', $text_cate);
        $seo_info = $frontendObj->readSeo($detail['id']);
        if ((int)$seo_info['id']) {
            Page::setHeader($seo_info['title_seo'], $seo_info['keyword_seo'], $seo_info['description_seo']);
        } else {
            Page::setHeader($detail['title'], $detail['description'], $detail['description']);
        }
        /*Set meta */
        Page::setMeta(
            '<meta property="og:title" content="' . str_replace(array('"', '', '\\', '/', ')', '('), array('', '', '', '', '', ''), $detail["title"]) . '" />
        ');
        Page::setMeta('
            <meta property="og:description" content="' . htmlspecialchars(str_replace(array('"', '', '\\', '/'), array('', '', '', ''), $detail["description"])) . '" />
		');
        Page::setMeta('
            <meta itemprop="datePublished" content="' . date('D d-m-Y H:i:s', $detail['time_public']) . '" />
        ');
        Page::setMeta('
            <meta name="news_keywords" content="' . trim(trim($detail['tag']), ',') . '" />
        ');
        Page::setMeta('
            <meta property="og:url" content="' . trim(ROOT_URL, '/') . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail") . '" />
        ');
        Page::setMeta('<meta  property="og:image" content="http://congly.vn/data/news/' . date('Y/n/d/', $detail['time_created']) . $detail['img1'] . '" id="idOgImg"/>');
        Page::setMeta('
            <link rel="alternate" media="only screen and (max-width: 640px)" href="http://m.congly.com.vn/' . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_id' => $detail['cate_id'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail") . '" />
		');
        /* Set data news*/
        joc()->set_var('title', $detail['title']);
        joc()->set_var('time_public', date('d/n/Y H:i', $detail['time_public']) . ' UTC+7');
        if ($detail['type_post'] == 3 OR $detail['type_post'] == 2 OR $detail['type_post'] == 5 OR $detail['type_post'] == 1 OR $detail['type_post'] == 6)
            $detail['description'] = '(Công lý) - ' . $detail['description'];

        joc()->set_var('description', $detail['description']);
        joc()->set_var('img', IMG::show($frontendObj->getPathNews($detail['time_created']), $detail['img1']));
        $detail['content'] = str_replace(array('font-size', 'font-family', 'alt=""'), array('', '', 'alt="' . htmlspecialchars($detail['title']) . '" title="' . htmlspecialchars($detail['title']) . '"'), $this->showContent($detail['content']));
        joc()->set_var('content', $frontendObj->showContent(str_replace('src="data/news', 'src="http://congly.vn/data/news', $detail['content'])));
        joc()->set_var('link_detail', trim(ROOT_URL, '/') . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_id' => $detail['cate_id'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail"));
        joc()->set_var('author', ($detail['author'] != '' ? $detail['author'] : '') . ($detail['origin'] != '' ? 'Theo ' . $detail['origin'] : ''));
        if ($detail['type_post'] == 4) {
            joc()->set_var('author', $detail['author'] . '(TH)');
        }
        /* Set poll */
        joc()->set_var('show_poll', $this->showPoll($detail, $frontendObj));
        /* Set sự kiện */
        if ($detail['topic_id']) {
            $list_topic = $frontendObj->getNews('topic', 'id,name,title', 'id=' . $detail['topic_id'], 'id DESC', 1);
            $topic = current($list_topic);
            $tp = '<span>Sự kiện:</span> <a class="title4" href="' . Url::Link(array('topic_id' => $topic['id'], 'name' => $topic['name']), 'news', 'topic') . '">' . $topic['name'] . '</a>';
            joc()->set_var('topic_name', $tp);
        } else {
            joc()->set_var('topic_name', '');
        }

        /* Set tin liên quan */
        $relate = array();
        if ($detail['relate'] != '') {
            $relate = $frontendObj->getNews("store", "id,title,cate_id,time_public", "id IN(" . rtrim($detail['relate'], ",") . ")", "time_public DESC", "0,5", "id", true);
        }

        $html_r = '';
        if (count($relate) > 0) {
            foreach ($relate as $r) {
                if ($list_category[$r['cate_id']]['alias'] != "") {
                    $html_r .= '<li><a class="title5" href="' . Url::link_detail($r, $list_category) . '" title="' . htmlspecialchars($r['title']) . '">' . $r['title'] . '</a></li>';
                }
            }
        }
        if ($html_r != "") {
            joc()->set_var('related', '<div class="news-related"><ul>' . $html_r . '</ul></div>');
        } else {
            joc()->set_var('related', "");
        }
        joc()->set_block('Detail', 'Tag', 'Tag');
        $tag = '';
        if ($detail['tag']) {
            $arr_tag_temp = explode('[]', $detail['tag']);
            $arr_tag = explode(',', $arr_tag_temp['0']);
            for ($k = 1; $k < count($arr_tag); ++$k) {
                joc()->set_var('tag_name', $arr_tag[$k]);
                joc()->set_var('tag_link', Url::link(array('tag' => 1, 'q' => str_replace(' ', '_', Convert::convertLinkTitle(trim($arr_tag[$k])))), 'news', 'search'));
                $tag .= joc()->output('Tag');
            }
        }
        joc()->set_var('Tag', trim($tag, '&nbsp;/&nbsp;'));

        $file = '';
        if (trim($detail['file'], ',')) {
            $arr_file = explode(',', trim($detail['file'], ','));
            for ($i = 0; $i < count($arr_file); ++$i) {
                $file .= '<a href="data/file/' . $arr_file[$i] . '">Tải file: ' . $arr_file[$i] . '</a><br>';
            }
        }

        joc()->set_var('file', $file);
        //Các tin mới cùng danh mục
        $detail['cate_id'] = isset($detail['cate_id']) ? $detail['cate_id'] : 1;
        $newsest = $frontendObj->getNews("store", "id,title,cate_id,img1,time_public,time_created", "cate_id!=" . (int)$detail['cate_id'], "time_public DESC", "0,8", "id", true);
        joc()->set_block('Detail', 'Other', 'Other');
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
        /* Liên kết danh mục */
        $cate_change = array(
            '269' => array('273', '288'),
            '273' => array('269', '283'),
            '279' => array('288', '273'),
            '288' => array('279', '269'),
            '283' => array('293', '273'),
            '293' => array('283', '298'),
            '298' => array('288', '273'),
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
        /* Set Comment */
        joc()->set_block('Detail', 'Comment', 'Comment');
        $comment = new Comment();
        $comments = $comment->getList("nw_id = $id and status = 1", 'time_post desc', 100);
        $html_comment = '';
        $i = 1;
        if (count($comments)) {
            joc()->set_var('display', 'block');
            foreach ($comments as $c) {
                joc()->set_var('full_name', $c['full_name']);
                joc()->set_var('number_like', (int)$c['number_like']);
                joc()->set_var('content_cm', strip_tags($c['content']));
                joc()->set_var('time_post', date('H:i, d/m/Y', $c['time_post']));
                $html_comment .= joc()->output('Comment');
            }
        } else {
            joc()->set_var('display', 'none');
        }

        global $TOTAL_ROWCOUNT;
        joc()->set_var('total', $TOTAL_ROWCOUNT);
        joc()->set_var('Comment', $html_comment);

        $html = joc()->output('Detail');
        joc()->reset_var();
        return $html;
    }

    function showContent($content)
    {
        $arr_cate = array(
            //'K&yacute; sự ph&aacute;p đ&igrave;nh' => '<a href="phap-dinh/ky-su-phap-dinh">Ký sự pháp đình</a>',
            //'T&ograve;a tuy&ecirc;n &aacute;n' => '<a href="phap-dinh/sau-vanh-mong-ngua">Tòa tuyên án</a>',
            //'B&uacute;t k&yacute; điều tra' => '<a href="phong-su/but-ky-dieu-tra">Bút ký điều tra</a>',
            //'Ghi ch&eacute;p' => '<a href="phong-su/ghi-chep">Ghi chép</a>',
            //'Thời sự' => '<a href="thoi-su">Thời sự</a>',
            //'Đời sống' => '<a href="xa-hoi/doi-song">Đời sống</a>',
            //'Hồ sơ vụ &aacute;n' => '<a href="phap-luat/ho-so-vu-an">Hồ sơ vụ án</a>',
            //'Doanh nghiệp' => '<a href="kinh-doanh/doanh-nghiep">Doanh nghiệp</a>',
            //'Tin nhanh' => '<a href="the-gioi/tin-nhanh">Tin nhanh</a>',
            //'B&oacute;ng đ&aacute;' => '<a href="the-thao/bong-da">Bóng đá</a>',
            //'Ho&agrave;n cảnh kh&oacute; khăn' => '<a href="nhan-ai/hoan-canh-kho-khan">Hoàn cảnh khó khăn</a>',
            //'70 năm Truyền thống TAND' => '<a href="hoat-dong-toa-an/70-nam-truyen-thong-tand">70 năm truyền thống tand</a>',
            //'Ph&aacute;p đ&igrave;nh' => '<a href="phap-dinh">Pháp đình</a>',
            //'An ninh h&igrave;nh sự' => '<a href="an-ninh-hinh-su">An ninh hình sự</a>',
            //'Vụ &aacute;n nổi tiếng' => '<a href="the-gioi/vu-an-noi-tieng">Vụ án nổi tiếng</a>',
            //'Thời trang - L&agrave;m đẹp' => '<a href="thoi-trang-lam-dep">Thời trang - làm đẹp</a>',
            //'Tennis' => '<a href="the-thao/tennis">Tennis</a>',
            //'Chứng kho&aacute;n' => '<a href="kinh-doanh/chung-khoan">Chứng khoán</a>',
            //'Nh&agrave; hảo t&acirc;m' => '<a href="nhan-ai/nha-hao-tam">Nhà hảo tâm</a>',
            //'Ti&ecirc;u điểm' => '<a href="hoat-dong-toa-an/tieu-diem">Tiêu điểm</a>',
            //'Gi&aacute;o dục' => '<a href="xa-hoi/giao-duc">Giáo dục</a>',
            //'Chuyện lạ' => '<a href="the-gioi/chuyen-la">Chuyện lạ</a>',
            //'Golf ' => '<a href="the-thao/golf"> Golf </a>',
            //'Nhạc' => '<a href="giai-tri/nhac">Nhạc</a>',
            //'Nhạc,Ng&acirc;n h&agrave;ng' => '<a href="kinh-doanh/ngan-hang">Ngân hàng</a>',
            //'Tin 141' => '<a href="tin-141">Tin 141</a>',
            //' tin ' => '<a href="/tin-tuc/"> tin </a>',
            //' Tin ' => '<a href="/tin-tuc/"> Tin </a>',
            'tin tức' => '<a href="/tin-tuc/">tin tức</a>',
            //'Trao đổi' => '<a href="doanh-nghiep/trao-doi">Trao đổi</a>',
            //'Chương tr&igrave;nh-Sự kiện' => '<a href="nhan-ai/chuong-trinh-su-kien">Chương trình-sự kiện</a>',
            //'Nghiệp vụ' => '<a href="hoat-dong-toa-an/nghiep-vu">Nghiệp vụ</a>',
            //'Sức khỏe ' => '<a href="xa-hoi/suc-khoe">Sức khỏe </a>',
            //'Ph&aacute;p luật' => '<a href="phap-luat">Pháp luật</a>',
            //'Cầu l&ocirc;ng' => '<a href="the-thao/cau-long">Cầu lông</a>',
            //'Phim' => '<a href="giai-tri/phim">phim</a>',
            //'Gi&aacute; v&agrave;ng' => '<a href="kinh-doanh/gia-vang">Giá vàng</a>',
            //'Tư vấn' => '<a href="doanh-nghiep/tu-van">Tư vấn</a>',
            //'Phong tr&agrave;o thi đua' => '<a href="hoat-dong-toa-an/phong-trao-thi-dua">Phong trào thi đua</a>',
            //'X&atilde; hội' => '<a href="xa-hoi">Xã hội</a>',
            //'Việc l&agrave;m' => '<a href="xa-hoi/viec-lam">Việc làm</a>',
            //'Nhắn tin' => '<a href="phap-luat/nhan-tin">Nhắn tin</a>',
            //'Bất động sản' => '<a href="kinh-doanh/bat-dong-san">Bất động sản</a>',
            //'Hoa hậu' => '<a href="giai-tri/hoa-hau">Hoa hậu</a>',
            //'Hội nhập' => '<a href="doanh-nghiep/hoi-nhap">Hội nhập</a>',
            //'Cải c&aacute;ch tư ph&aacute;p' => '<a href="hoat-dong-toa-an/cai-cach-tu-phap">Cải cách tư pháp</a>',
            //'Du lịch' => '<a href="giai-tri/du-lich">Du lịch</a>',
            //'Các môn khác' => '<a href="the-thao/cac-mon-khac">Các môn khác</a>',
            //'Nhật ký vành móng ngựa' => '<a href="phap-dinh/nhat-ky-vanh-mong-ngua">Nhật ký vành móng ngựa</a>',
            //'Gia đ&igrave;nh' => '<a href="xa-hoi/gia-dinh">Gia đình</a>',
            //'Giao th&ocirc;ng' => '<a href="xa-hoi/giao-thong">Giao thông</a>',
            //'Giải tr&iacute;' => '<a href="giai-tri">Giải trí</a>',
            //'Nhịp cầu công lý' => '<a href="nhip-cau-cong-ly">Nhịp cầu công lý</a>',
            //'Bảo vệ người ti&ecirc;u d&ugrave;ng' => '<a href="bao-ve-nguoi-tieu-dung">Bảo vệ người tiêu dùng</a>',
            //'Thể thao' => '<a href="the-thao">Thể thao</a>',
            //'Kinh doanh' => '<a href="kinh-doanh">Kinh doanh</a>',
            //'Thế giới' => '<a href="the-gioi">Thế giới</a>',
            //'C&ocirc;ng nghệ' => '<a href="cong-nghe">Công nghệ</a>',
            //'Giới trẻ' => '<a href="gioi-tre">Giới trẻ</a>',
            //'Thư giãn' => '<a href="thu-gian">Thư giãn</a>',
            //'Hoạt động Tòa án' => '<a href="hoat-dong-nganh">Hoạt động tòa án</a>',
            //'Góc nhìn' => '<a href="goc-nhin">Góc nhìn</a>',
            //'Video' => '<a href="video">Video</a>',
            //'Ng&acirc;n h&agrave;ng' => '<a href="kinh-doanh/ngan-hang">Ngân hàng</a>',
            //'C&ocirc;ng l&yacute;' => '<a href="http://congly.com.vn">Công lý</a>',
            //'K&yacute; sự ph&aacute;p đ&igrave;nh' => '<a href="phap-dinh/ky-su-phap-dinh">ký sự pháp đình</a>',
            't&ograve;a tuy&ecirc;n &aacute;n' => '<a href="phap-dinh/sau-vanh-mong-ngua">tòa tuyên án</a>',
            'b&uacute;t k&yacute; điều tra' => '<a href="phong-su/but-ky-dieu-tra">bút ký điều tra</a>',
            'ghi ch&eacute;p' => '<a href="phong-su/ghi-chep">ghi chép</a>',
            'thời sự' => '<a href="thoi-su">thời sự</a>',
            'đời sống' => '<a href="xa-hoi/doi-song">đời sống</a>',
            'hồ sơ vụ &aacute;n' => '<a href="phap-luat/ho-so-vu-an">hồ sơ vụ án</a>',
            'doanh nghiệp' => '<a href="kinh-doanh/doanh-nghiep">doanh nghiệp</a>',
            'tin nhanh' => '<a href="the-gioi/tin-nhanh">tin nhanh</a>',
            'b&oacute;ng đ&aacute;' => '<a href="the-thao/bong-da">bóng đá</a>',
            'ho&agrave;n cảnh kh&oacute; khăn' => '<a href="nhan-ai/hoan-canh-kho-khan">hoàn cảnh khó khăn</a>',
            '70 năm Truyền thống TAND' => '<a href="hoat-dong-toa-an/70-nam-truyen-thong-tand">70 năm truyền thống tand</a>',
            'ph&aacute;p đ&igrave;nh' => '<a href="phap-dinh">pháp đình</a>',
            'an ninh h&igrave;nh sự' => '<a href="an-ninh-hinh-su">an ninh hình sự</a>',
            'vụ &aacute;n nổi tiếng' => '<a href="the-gioi/vu-an-noi-tieng">vụ án nổi tiếng</a>',
            'thời trang - L&agrave;m đẹp' => '<a href="thoi-trang-lam-dep">thời trang - làm đẹp</a>',
            'tennis ' => '<a href="the-thao/tennis">tennis</a>',
            'chứng kho&aacute;n' => '<a href="kinh-doanh/chung-khoan">chứng khoán</a>',
            'nh&agrave; hảo t&acirc;m' => '<a href="nhan-ai/nha-hao-tam">nhà hảo tâm</a>',
            'ti&ecirc;u điểm' => '<a href="hoat-dong-toa-an/tieu-diem">tiêu điểm</a>',
            'gi&aacute;o dục' => '<a href="xa-hoi/giao-duc">giáo dục</a>',
            'chuyện lạ' => '<a href="the-gioi/chuyen-la">chuyện lạ</a>',
            'golf ' => '<a href="the-thao/golf">golf</a>',
            //'nhạc' => '<a href="giai-tri/nhac">nhạc</a>',
            'nhạc,Ng&acirc;n h&agrave;ng' => '<a href="kinh-doanh/ngan-hang">ngân hàng</a>',
            'tin 141' => '<a href="tin-141">tin 141</a>',
            //'trao đổi' => '<a href="doanh-nghiep/trao-doi">trao đổi</a>',
            'chương tr&igrave;nh-Sự kiện' => '<a href="nhan-ai/chuong-trinh-su-kien">chương trình-sự kiện</a>',
            'nghiệp vụ' => '<a href="hoat-dong-toa-an/nghiep-vu">nghiệp vụ</a>',
            'sức khỏe ' => '<a href="xa-hoi/suc-khoe">sức khỏe </a>',
            'ph&aacute;p luật' => '<a href="phap-luat">pháp luật</a>',
            'cầu l&ocirc;ng' => '<a href="the-thao/cau-long">cầu lông</a>',
            //'phim ' => '<a href="giai-tri/phim">phim </a>',
            'gi&aacute; v&agrave;ng' => '<a href="kinh-doanh/gia-vang">giá vàng</a>',
            //'tư vấn' => '<a href="doanh-nghiep/tu-van">tư vấn</a>',
            'phong tr&agrave;o thi đua' => '<a href="hoat-dong-toa-an/phong-trao-thi-dua">phong trào thi đua</a>',
            //'x&atilde; hội' => '<a href="xa-hoi">xã hội</a>',
            'việc l&agrave;m' => '<a href="xa-hoi/viec-lam">việc làm</a>',
            'nhắn tin' => '<a href="phap-luat/nhan-tin">nhắn tin</a>',
            'bất động sản' => '<a href="kinh-doanh/bat-dong-san">bất động sản</a>',
            'hoa hậu' => '<a href="giai-tri/hoa-hau">hoa hậu</a>',
            //'hội nhập' => '<a href="doanh-nghiep/hoi-nhap">hội nhập</a>',
            'cải c&aacute;ch tư ph&aacute;p' => '<a href="hoat-dong-toa-an/cai-cach-tu-phap">cải cách tư pháp</a>',
            'du lịch' => '<a href="giai-tri/du-lich">du lịch</a>',
            //'các môn khác' => '<a href="the-thao/cac-mon-khac">các môn khác</a>',
            'nhật ký vành móng ngựa' => '<a href="phap-dinh/nhat-ky-vanh-mong-ngua">nhật ký vành móng ngựa</a>',
            'gia đ&igrave;nh' => '<a href="xa-hoi/gia-dinh">gia đình</a>',
            'giao th&ocirc;ng' => '<a href="xa-hoi/giao-thong">giao thông</a>',
            'giải tr&iacute;' => '<a href="giai-tri">giải trí</a>',
            'nhịp cầu công lý' => '<a href="nhip-cau-cong-ly">nhịp cầu công lý</a>',
            'bảo vệ người ti&ecirc;u d&ugrave;ng' => '<a href="bao-ve-nguoi-tieu-dung">bảo vệ người tiêu dùng</a>',
            'thể thao' => '<a href="the-thao">thể thao</a>',
            'kinh doanh' => '<a href="kinh-doanh">kinh doanh</a>',
            'thế giới' => '<a href="the-gioi">thế giới</a>',
            'c&ocirc;ng nghệ' => '<a href="cong-nghe">công nghệ</a>',
            'giới trẻ' => '<a href="gioi-tre">giới trẻ</a>',
            'thư giãn' => '<a href="thu-gian">thư giãn</a>',
            'hoạt động Tòa án' => '<a href="hoat-dong-nganh">hoạt động tòa án</a>',
            'góc nhìn' => '<a href="goc-nhin">góc nhìn</a>',
            //'video' => '<a href="video">video</a>',
            'nh&acirc;n &aacute;i' => '<a href="nhan-ai">nhân ái</a>',
            'ng&acirc;n h&agrave;ng' => '<a href="kinh-doanh/ngan-hang">ngân hàng</a>',
            'b&aacute;o' => '<a href="/">b&aacute;o</a>',
            //'B&aacute;o' => '<a href="/">B&aacute;o</a>',
        );

        foreach ($arr_cate as $key => $val) {
            $content = $this->str_replace_first($key, $val, $content);
        }
        return $content;
    }

    function str_replace_first($from, $to, $subject)
    {
        $from = '/' . preg_quote($from, '/') . '/';
        return preg_replace($from, $to, $subject, 1);
    }

    function showPoll($detail, $frontendObj)
    {
        $html_poll = '';
        $poll_id = $detail["poll_id"];
        $poll_cookie = isset($_COOKIE['poll']) ? $_COOKIE['poll'] : '0,';
        if ($poll_cookie == '0,') {
            $poll_cookie = isset($_SESSION['poll']) ? $_SESSION['poll'] : '0,';
        }
        //neu chua chua vote thi hien thi poll
        if ($poll_id > 0) {
            $polls = $frontendObj->getPoll($detail['poll_id']);
            $poll = $polls["poll"];
            $options = $polls["option"];
            if (strpos($poll_cookie, ",$poll_id,") === false) {
                $html_poll = '<div class="ct-poll" style="float:none;width:448px; margin:0 auto 20px;">';
                $html_poll .= ' <h4 class="poll_title">BÌNH CHỌN</h4><div class="ct-poll-content">
                    <h3>' . $poll["name"] . '</h3>
                   <ul>';
                if (count($options)) {
                    foreach ($options as $k => $op) {
                        if ($poll["type"] == 1)
                            $html_poll .= '<li><input class="poll_option" type="checkbox" value="' . $op["id"] . '" name="poll_option"> ' . $op["text"] . '</li>';
                        else
                            $html_poll .= '<li><input class="poll_option" type="radio" value="' . $op["id"] . '" name="poll_option"> ' . $op["text"] . '</li>';
                    }
                }
                $html_poll .= '</ul></div><div class="ct-poll-bottom">';

                $html_poll .= '<input type="button" value="Bình chọn" name="poll_voted" id="poll_voted" onclick="poll(' . $poll_id . ')" class="as"><input type="button" rel="' . $detail["poll_id"] . '" value="Kết quả" name="poll_result" id="poll_result" onclick="poll_result(' . $poll_id . ')">
		    	</div></div>';
            }//show ket qua
            else {
                $html_poll .= '<div class="ct-poll" style="float:none;width:455px; margin:0 auto 20px;">';
                $html_poll .= '<table class="fb-table" width="100%"><tr><th colspan="2">Kết quả bình chọn</th></tr><tr><td colspan="2" class="td-ask"><strong>' . $poll["name"] . '</strong> </td></tr>';
                if (count($options)) {
                    $total = 0;
                    foreach ($options as $op)
                        $total += $op["voted"];

                    $color = array("#FF3300", "#009900", "#0C186D", "#cccccc", "#ffee00", "#ff0088", "#000");
                    $tcolor = array("#000", "#000", "#fff", "#000", "#000", "#000", "#fff");

                    $i = 0;
                    foreach ($options as $op) {
                        $v = ($total > 0 ? (($op["voted"] / $total) * 100) : 0);
                        $html_poll .= '<tr><td>' . $op["text"] . '</td><td class="td-label"><p style="color:' . $tcolor[$i] . ';padding:2px 5px;margin-right:5px;width:' . $v . '%;background:' . $color[$i] . '"> ' . round($v) . '%</p></td></tr>';//<td>'.$op["voted"].' phiếu</td></tr>';
                        $i++;
                    }
                    //$html_poll .= '<tr><td colspan="2" class="td-end">Tổng cộng: '.$total.' Phiếu </td></tr>';
                }
                $html_poll .= '</table>';
                $html_poll .= '</div>';
            }
        }
        return $html_poll;
    }

}

?>