<?php
//ini_set('display_errors', 1);
require_once 'application/news/frontend/includes/frontend.news.php';
require_once 'application/news/includes/comment_model.php';
require_once 'application/user/includes/comment.php';
include_once UTILS_PATH . 'convert.php';

class Congly_Detail
{

    function __construct()
    {

    }

    function index()
    {

        joc()->set_file('Detail', Module::pathTemplate() . "frontend" . DS . "detail.htm");
        global $list_category;
        global $info_news;
        $id = SystemIO::get('id', "int", 0);
        joc()->set_var('id', $id);
        $frontendObj = new FrontendNews();
        $detail = $info_news;
        $cate_id_current = $detail['cate_id'];
        $cate_id = $detail['cate_id'];
        if ($detail['time_public'] > time() || (int)$detail['id'] == 0 || $detail['time_public'] == 0) {
            @header('Location: http://congly.vn/');
            die;
        }
        /* Navigation */
        $cate_id_parent = 0;
        if ($list_category[$cate_id]['cate_id1'] == 0) {
            joc()->set_var('cate_parent_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_parent_href', $list_category[$cate_id]['alias']);
            joc()->set_var('cate_child_name', '');
            joc()->set_var('cate_footer_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_footer_href', $list_category[$cate_id]['alias']);
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
            joc()->set_var('cate_footer_name', $list_category[$cate_id]['name']);
            joc()->set_var('cate_footer_href', $list_category[$cate_id]['alias']);
            joc()->set_var('class_cate_end_2', 'breadcrumble_title_end');
            joc()->set_var('class_cate_end_1', '');
            $display_cate_child = 'block';
        }
        if ($cate_id_parent == 398) // Congly 24h
        {
            joc()->set_var('sdt_24', 'Mọi thông tin phản hồi về Ban chuyên đề Công lý 24h, Email: <a href="mailto:24hcongly@gmail.com">24hcongly@gmail.com</a>, Hotline 090.328.3333');
        } else {
            if($cate_id_parent == 414) // ban doc
                joc()->set_var('sdt_24', 'Mọi thông tin chia sẻ, phản hồi xin gửi về hòm thư <a href="mailto:conglydientu@congly.vn">conglydientu@congly.vn, Hotline 091.2532.315 - 090.328.3333</a>');
            else
                joc()->set_var('sdt_24', 'Mọi thông tin chia sẻ, phản hồi xin gửi về hòm thư <a href="mailto:conglydientu@congly.vn">conglydientu@congly.vn, Hotline 091.2532.315 - 096.1101.678</a>');

        }

        if ($cate_id == '269' || $cate_id == '338' || $list_category[$cate_id]['cate_id1'] == '269' || $list_category[$cate_id]['cate_id1'] == '338')
        {
            joc()->set_var('qc_geniee', '');
        }
        else {
            joc()->set_var('qc_geniee', '<script type="text/javascript" src="http://js.genieessp.com/t/164/966/a1164966.js"></script>');
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
        $seo_info = $frontendObj->readSeo($info_news['id']);
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

          
        /* Set data news*/
        joc()->set_var('title', $detail['title']);
        joc()->set_var('time_public', date('d/n/Y H:i', $detail['time_public']) . ' UTC+7');
        if ($detail['type_post'] == 3 OR $detail['type_post'] == 2 OR $detail['type_post'] == 5 OR $detail['type_post'] == 1 OR $detail['type_post'] == 6)
            $detail['description'] = '(Công lý) - ' . $detail['description'];

        joc()->set_var('description', $detail['description']);

        joc()->set_var('img', IMG::show($detail));

        $detail['content'] = str_replace(array('font-size', 'font-family', 'alt=""'), array('', '', 'alt="' . htmlspecialchars($detail['title']) . '" title="' . htmlspecialchars($detail['title']) . '"'), $this->showContent($detail['content']));
        /*Is mobile*/
        $useragent = $_SERVER['HTTP_USER_AGENT'];
        if (preg_match('/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $useragent) || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4))) {
            $detail['content'] = $this->showImgContent($detail['content']);
        }

        joc()->set_var('content', $frontendObj->showContent(str_replace('src="data/news', 'src="http://congly.vn/data/news', $detail['content'])));
        joc()->set_var('link_detail', trim(ROOT_URL, '/') . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_id' => $detail['cate_id'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail"));
        Page::setMeta('<link rel="canonical" href="'.trim(ROOT_URL, '/') . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_id' => $detail['cate_id'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail").'" />');
        joc()->set_var('author', ($detail['author'] != '' ? $detail['author'] : '') . ($detail['origin'] != '' ? 'Theo ' . $detail['origin'] : ''));
        if ($detail['type_post'] == 4) {
            joc()->set_var('author', $detail['author'] . '(TH)');
        }

        /*Set search Google*/
        Page::setMeta('
        <script data-schema="Organization" type="application/ld+json">
            {"name":"Tin nhanh Congly - Đọc báo, tin tức online 24h ","url":"http://congly.vn/","logo":"http://congly.vn/webskins/skins/news/images/logo-congly.png","alternateName" : "Thông tin nhanh & mới nhất được cập nhật hàng giờ. Tin tức Việt Nam & thế giới về xã hội, kinh doanh, pháp luật, khoa học, công nghệ, sức khoẻ, đời sống, văn hóa, rao vặt, tâm sự...","sameAs":["https://www.facebook.com/congly.vn/?fref=ts/"],"@type":"Organization","@context":"http://schema.org"}
        </script>
        <script type="application/ld+json">
            {
                "@context"	: "http://schema.org",
                "@type"		: "NewsArticle",
                "mainEntityOfPage"	: {
                    "@type"	:"WebPage",
                    "@id"	:"'.trim(ROOT_URL, '/') . Url::Link(array("id" => $id, "title" => $detail['title'], 'cate_id' => $detail['cate_id'], 'cate_alias' => $list_category[$detail['cate_id']]['alias']), "news", "congly_detail").'"
                },
                "headline"	: "'.$detail['title'].'",
                "description"	: "'.$detail['description'].'",
                "image": {
                    "@type"		: "ImageObject",
                    "url"		: "'.IMG::show($detail).'",
                    "width" 	: 660,
                    "height" 	: 430
                },
                "datePublished"	: "'.date('D d-m-Y H:i:s', $detail['time_public']).'",
                "dateModified"	: "'.date('D d-m-Y H:i:s', $detail['time_public']).'",


                "publisher": {
                    "@type"	: "Organization",
                    "name"	: "Congly.vn",
                    "logo"	: {
                        "@type"	: "ImageObject",
                        "url"	: "http://congly.vn/webskins/skins/news/images/logo-congly.png",
                        "width"	: 225,
                        "height": 101
                    }
                }
            }
            </script>
            <script type="application/ld+json">
            {
                "@context"	: "http://schema.org",
                "@type"		: "BreadcrumbList",
                "itemListElement": [
                    {
                        "@type"	: "ListItem",
                        "position"	: 1,
                        "item"	: {
                            "@id"	: "http://congly.vn/",
                            "name"	: "Trang chủ"
                        }
                    },
                    {
                        "@type": "ListItem",
                        "position"	: 2,
                        "item": {
                            "@id": "'.$list_category[$cate_id_parent]['alias'].'",
                            "name": "'.$list_category[$cate_id_parent]['name'].'"
                        }
                    }
                ]
            }
            </script>'
        );
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
            for ($k = 0; $k < count($arr_tag); ++$k) {
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
        //Các tin mới khác danh mục
        $detail['cate_id'] = isset($detail['cate_id']) ? $detail['cate_id'] : 1;
        $newsest = $frontendObj->getNews("store", "id,title,cate_id,img1,time_public,time_created", "cate_id != '269' AND cate_path NOT LIKE '%,338,%' AND cate_id!=" . (int)$detail['cate_id'], "time_public DESC", "0,8", "id", true);
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
        //Các tin mới cùng danh mục
        $detail['cate_id'] = isset($detail['cate_id']) ? $detail['cate_id'] : 1;
        $newsest_same = $frontendObj->getNews("store", "id,title,cate_id,img1,time_public,time_created", "id != {$detail['id']} AND time_public < " . $detail['time_public'] . " AND cate_id=" . (int)$detail['cate_id'], "time_public DESC", "0,8", "id", true);
        joc()->set_block('Detail', 'Same', 'Same');
        $html_newest = '';
        if (count($newsest) > 0) {
            foreach ($newsest_same as $key => $n) {

                $link = Url::link_detail($n, $list_category);
                $img = IMG::thumb($n, 'cnn_150x100');
                joc()->set_var('same_link', $link);
                joc()->set_var('same_title', $n['title']);
                joc()->set_var('same_html_title', htmlspecialchars($n['title']));
                joc()->set_var('same_img', $img);
                joc()->set_var('same_date', date('d/n', $n['time_public']));
                $html_newest .= joc()->output('Same');
            }
        }
        joc()->set_var('Same', $html_newest);
        /* Liên kết danh mục */
        $cate_change = array(
            '269' => array('273', '288'),
            '273' => array('269', '283'),
            '279' => array('288', '273'),
            '288' => array('314', '269'),
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
        /* Box conglyxaohi.net.vn */
        joc()->set_block('Detail', 'Conglyxahoi', 'Conglyxahoi');
        $memcache = new Memcache;
        $memcache->addServer('localhost', 11211);
        $list_conglyxaoi = json_decode($memcache->get('conglyxahoinetvn'), true);
        $html_conglyxahoi = '';
        $k = 1;
        foreach ($list_conglyxaoi as $key => $n) {
            ++$k;
            $link = $n['href'];
            $img = $n['img'];
            joc()->set_var('conglyxahoi_link', $link);
            joc()->set_var('conglyxahoi_title', $n['title']);
            joc()->set_var('conglyxahoi_html_title', htmlspecialchars($n['title']));
            joc()->set_var('conglyxahoi_img', $img);
            joc()->set_var('conglyxahoi_date', date('d/n', $n['time_public']));
            $html_conglyxahoi .= joc()->output('Conglyxahoi');
            if ($k > 4) break;
        }
        joc()->set_var('Conglyxahoi', $html_conglyxahoi);

        /* Set Comment */
        joc()->set_block('Detail', 'Comment', 'Comment');
        $comment = new Comment();
        $comments = $comment->getList("nw_id = $id and status = 1", 'parent_id desc, time_post desc', 100);
        $html_comment = '';
        $i = 1;
        if (count($comments)) {
            joc()->set_var('display', 'block');
            foreach ($comments as $c) {
                joc()->set_var('full_name', $c['full_name']);
                joc()->set_var('cm_id', $c['id']);
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
        /*
       $str = explode('</p>', $content);
       $total_p = round(count($str) / 2);
       $str[$total_p] = $str[$total_p] . '';
       $content_ag = '';
       for ($i = 0; $i < count($str); ++$i) {
           $content_ag .= $str[$i] . '</p>';
       }
        */
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

    function showImgContent($content, $time_created = 0)
    {
        if (substr_count($content, 'jwplayer')) {
            return $content;
        }
        $partern = '/src=\"([^\"]*)\"/';
        preg_match_all($partern, $content, $m);
        $images = $m[1];
        $leng = count($images);
        if ($leng > 0) {
            for ($i = 0; $i < $leng; $i++) {
                $p_height = '#height="(.*?)"#';
                $p_width = '#width="(.*?)"#';
                $content = preg_replace($p_height, "", $content);
                $content = preg_replace($p_width, "", $content);
                $p_width_style = "#width:(.*?)#";
                $p_height_style = "#height:(.*?)#";
                $content = preg_replace($p_width_style, "", $content);
                $content = preg_replace($p_height_style, "", $content);
               
            }
        }
        
        $content = str_replace('width', '', $content);
        $content = str_replace('height', '', $content);
        //$content = str_replace('http://congly.com.vn/data/', 'http://congly.vn/resize.320x-/data/', $content);
        //$content = str_replace('data/news/', 'resize.320x-/data/news/', $content);

        return $content;
    }
}

?>