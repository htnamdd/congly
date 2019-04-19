<?php
if (defined(IN_JOC))
    die("Direct access not allowed!");
require_once 'application/news/frontend/includes/class.xml.php';
require(APPLICATION_PATH . 'news' . DS . 'frontend' . DS . 'includes' . DS . 'frontend.news.php');

class Congly_Header
{

    function index()
    {
        joc()->set_file('Header', Module::pathTemplate('news') . "frontend/header.htm");
        //Page::registerFile('style.css', 'webskins' . DS . 'skins' . DS . 'news' . DS . 'css' . DS . 'style.css?ver=' . time(), 'header', 'css');
        //Page::registerFile('jquery.menu.css', 'webskins' . DS . 'skins' . DS . 'news' . DS . 'css' . DS . 'jquery.mmenu.all.css?ver=' . time(), 'header', 'css');
        Page::registerFile('tinyscrollbar.css', 'webskins' . DS . 'skins' . DS . 'news' . DS . 'css' . DS . 'tinyscrollbar.css?ver=' . time(), 'header', 'css');
        Page::setMeta('
            <script>
            (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-3880298791450605",
            enable_page_level_ads: true
            });
            </script>');
        if(SystemIO::get('print'))
        {
            joc()->set_var('action_print','window.print();');
        } else {
            joc()->set_var('action_print','');
        }
        joc()->set_var('uri',$_SERVER['REQUEST_URI']);
        $frontendObj = new FrontendNews();
        $page = SystemIO::get('page', 'def', 'congly_home');
        global $list_category;
        global $list_category_alias;
        global $info_news;
        global $linkEsn;
        $linkEsn = $this->showLinkESN();
        $list_category = $frontendObj->getCategory();
        $list_category_alias = SystemIO::arrayToOption($list_category, 'id', 'alias');
        $id = SystemIO::get('id', 'int', 0);
        $cate_id = SystemIO::get('cate_id', 'int', 0);
        if ($id && $cate_id == 0) {
            $detail = $frontendObj->newsOne($id);
            $detail_content = $frontendObj->detail($id);
            $info_news = $detail;
            $info_news['content'] = $detail_content;
            $cate_id = $info_news['cate_id'];
        }
        $cate_current = '';
        if ($cate_id) {
            $cate_current = $list_category[$cate_id]['cate_id1'] ? $list_category[$cate_id]['cate_id1'] : $cate_id;
            if ($cate_id == '269' || $cate_id == '338' || $list_category[$cate_id]['cate_id1'] == '269' || $list_category[$cate_id]['cate_id1'] == '338')
            {
                joc()->set_var('google_adsence', 'display:none');
            }

            else{
                joc()->set_var('google_adsence', 'display:block');
            }

        }

        joc()->set_var('qc_geeni','');
        if ($page == 'congly_home') {
            joc()->set_var('current_home', 'mn-active');
            joc()->set_var('show_menu','display:block');
            joc()->set_var('logo', '<h1 style="margin:0px;padding:0px"><a href="/"><img src="webskins/skins/news/images/logo-congly.png" alt="Báo Công lý" /></a></h1>');
            //joc()->set_var('logo', '<h1 style="margin:0px;padding:0px"><a href="/"><img src="adv/logo_tet2019.png" alt="Báo Công lý" /></a></h1>');
        } else {
            joc()->set_var('show_menu','display:none');
            joc()->set_var('current_home', '');
            //joc()->set_var('logo', '<a href="/"><img src="adv/logo_tet2019.png" alt="Báo Công lý" /></a>');
            joc()->set_var('logo', '<a href="/"><img src="webskins/skins/news/images/logo-congly.png" alt="Báo Công lý" /></a>');
        }
        $class = '';
        $text_menu = '';
        $j = 1;
        foreach ($list_category as $row) {
            if ($row['property'] & 1 == 1) {
                if (($row['property'] & 32) && ($row['cate_id1'] == 0)) {// thuoc tin hien thi tren menu
                    if ($cate_current == $row['id'])
                        $class = 'mn-active';
                    else
                        $class = '';
                    $href_parent = Url::link_cate($row);
                    $text_menu .= ' <li class="' . $class . '">';
                    $text_menu .= '<a href="' . $href_parent . '">' . $row['name'] . '</a><ul>';
                    $k = 0;
                    foreach ($list_category as $_temp) {
                        if ($_temp['property'] & 1) {
                            if (($_temp['cate_id1'] == $row['id']) && $_temp['cate_id2'] == 0 && ($_temp['property'] & 32)) {
                                $href_child = Url::link_cate($_temp);
                                $text_menu .= '<li><a href="' . $href_child . '">' . $_temp['name'] . '</a></li>';
                                ++$k;
                            }
                        }
                    }
                    $text_menu .= '</ul>';
                    $text_menu .= '</li>';
                    ++$j;
                }
            }
        }
        joc()->set_var('text_menu', $text_menu);

        joc()->set_var('text_menu', $text_menu);
        $date = date('w', time());
        $array = array(2 => 'Hai', 3 => 'Ba', 4 => 'Tư', 5 => 'Năm', 6 => 'Sáu', 7 => 'Bảy');
        if ($date == '0')
            $text_date = 'Chủ nhật, ' . date('d/n/Y', time());
        else
            $text_date = 'Thứ ' . ($array[$date + 1]) . ', ' . date('d/n/Y', time());

        joc()->set_var('text_date', $text_date);
        joc()->set_var('root_url', ROOT_URL);
        $this->goolgeAdsense();
        $html = joc()->output("Header");
        joc()->reset_var();
        return $html;
    }

    public function goolgeAdsense()
    {
        return true;
    }
    public function showLinkESN()
    {
        return '';
        global $db_link_esn;
        $text_link = '';
        $k = 0;
        $url_uri = $_SERVER['REQUEST_URI'];
        $box_link = '';
        for ($i = 0; $i < count($db_link_esn); ++$i) {
            if ($url_uri == str_replace('%2F', '/', @$db_link_esn[$i]->url_dest)) {
                ++$k;
                $text_link .= '<a style="font-size:12px;color:#000;text-decoration:none;" href="' . $db_link_esn[$i]->link_url . '" target="_blank">' . $db_link_esn[$i]->link_anchortext . '</a> | ';
                if ($k > 5)
                    break;
            }
        }

        $box_link = trim($text_link, '| ');
        if ($k == 0)
            return '<a style="font-size:12px;color:#000;text-decoration:none;" href="/" target="_blank">Báo Công lý</a>';

        return $box_link;
    }

}
