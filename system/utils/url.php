<?php

class Url
{
    /***
     *    Function name buildUrlAll
     *    description Ham build url voi tat ca cac tham so dang co hien thoi tren url dong thoi loai nhung param nam trong mang $arrExpelParam va them chuoi cac param duoc truyen vao $strAddParam
     * @param $arrExpelParam - Mang cac param muon loai tru trong duong dan url
     * @param $strAddParam - xau cac param them vao url co kieu (&param1=value1&param2=value2)
     * @return - string url
     * @note Bat buoc phai truyen du 2 doi so $varName, $type
     ***/
    public static function buildUrlAll($arrExpelParam = array(), $strAddParam = '')
    {
        $url = '';
        $portal = '';
        $page = '';

        if (is_array($_GET))
            foreach ($_GET as $key => $value) {
                if (!in_array($key, $arrExpelParam)) {
                    if ($key == 'app')
                        $portal = $value;
                    elseif ($key == 'page')
                        $page = $value;
                    else if (!$url) {
                        $url = '?' . urlencode($key) . '=' . urlencode($value);
                    } else {
                        $url .= '&' . urlencode($key) . '=' . urlencode($value);
                    }
                }
            }
        if ($portal == '')
            $portal = $_GET ['app'];
        if ($page == '')
            $page = $_GET ['page'];
        $arr = array("?");

        if ($url != '')
            $url = str_replace($arr, "?app=" . $portal . "&page=" . $page . "&", $url);
        else
            $url = "?app=" . $portal . "&page=" . $page;

        if ($strAddParam) {
            if ($url) {
                $url .= '&' . $strAddParam;
            } else {
                $url .= '?' . $strAddParam;
            }
        }
        unset ($portal);
        unset ($page);
        return $url;
    }

    static public function urlDenied()
    {
        @header('Location:?app=main&page=page_denied');
    }

    static public function buildUrlRewrite($arrParam = array(), $portal = '', $page = '', $page_no = false)
    {
        if (count($arrParam) > 0 && is_array($arrParam))
            foreach ($arrParam as $key => $ar)
                $ext .= "&$key=" . $ar;

        return "?app=$portal&page=$page" . $ext;
    }

    static function news_congly_cate($param)
    {
        if (isset($param['page_no']) && !isset($param['province_id']))
            return '/' . $param['title'] . '/trang-' . $param['page_no'] . '/';
        return '/' . $param['title'] . '/';
    }

    static function news_congly_detail($param)
    {
        return '/' . trim($param['cate_alias'], '/') . '/' . $param['title'] . '-' . $param['id'] . '.html';
    }

    static function news_detail($param)
    {
        return '/' . trim($param['cate_alias'], '/') . '-' . $param['cate_id'] . '/' . $param['title'] . '-' . $param['id'] . '.html';
    }

    static function news_congly_home($param)
    {
        return '/' . trim($param['title'], '/') . '-' . $param['cate_id'] . '/' . $param['title'] . '-' . $param['id'] . '.html';
    }

    static public function redirectUrl($params = array(), $url = false, $page = false, $portal = '')
    {
        if ($url) {
            if ((!@header('HTTP/1.1 303 See Other', TRUE, 303) and !@header('Location:' . $url))) {
                echo '<head><meta http-equiv="Refresh" content="0;url=\'' . $url . '\'"/>';
                echo '<script language="javascript">window.location.href="' . $url . '";</script></head>';
            }
            exit ();
        } else {
            @header('Location:' . Url::buildUrlRewrite($params, $portal, $page));
            exit ();
        }
    }

    static function link($param = array(), $app = "", $page = "")
    {
        include_once UTILS_PATH . 'convert.php';
        if (isset($param['title']))
            $param['title'] = Convert::convertLinkTitle($param['title']);
        if (isset($param['name']))
            $param['name'] = Convert::convertLinkTitle($param['name']);
        if (isset($param['alias']))
            $param['alias'] = str_replace(',', "", $param['alias']);
        $app = $app == "" ? $_GET['app'] : $app;
        $page = $page == "" ? $_GET['page'] : $page;
        $page = str_replace('-', '_', $page);
        $function = $app . '_' . $page;


        if (REWRITE)
            return Url::$function($param);
        else {
            $link = "?app=$app&page=$page";
            if (count($param) > 0 && is_array($param)) {
                foreach ($param as $key => $p)
                    $link .= "&$key=$p";
            }
            return $link;
        }
    }

    static function redirect_current()
    {
    }

    static function news_search($param)
    {
        if ($param['tag']) {
            //$param['q']=Convert::convertLinkTitle($param['q']);
            if (isset($param['page_no']))
                return 'tag/' . $param['q'] . '/trang-' . $param['page_no'] . '/';
            return 'tag/' . $param['q'] . '/';
        }
        if (isset($param['page_no']))
            return 'search/' . $param['q'] . '/trang-' . $param['page_no'] . '/';
        return 'search/' . $param['q'] . '/';
    }

    static function news_admin_video()
    {
        return 'news_admin_video';
    }

    static function news_topic($param)
    {

        return '/' . $param['topic_id'] . '/' . $param['name'] . '/';
    }

    static function link_detail($row,$list_category)
    {
        $data = array('id' => isset($row['nw_id']) ? $row['nw_id'] : $row['id'], 'title' => $row['title'], 'cate_id' => $row['cate_id'], 'cate_alias' => $list_category[$row['cate_id']]['alias']);
        return self::link($data, 'news', 'congly_detail');
    }

    static function link_cate($row)
    {
        return Url::link(array('cate_id' => $row['id'], 'title' => $row['alias']), 'news', 'congly_cate');
    }
}

?>