<?php
//ini_set('display_errors',1);
require_once 'application/news/frontend/includes/frontend.news.php';
$newsObj = new FrontendNews();
$list_category = $newsObj->getCategory('', 'arrange asc', 200, true);
$LIST_CATEGORY = $list_category;
$LIST_CATEGORY_ALIAS = SystemIO::arrayToOption($LIST_CATEGORY, 'id', 'alias');
$cate_id = SystemIO::get('cate_id', 'int', 0);
$list_news_all = $newsObj->getNews('store', 'id,title,description,time_public,cate_id,time_created,img1,img2', ' ( cate_path LIKE "%,' . $cate_id . ',%") AND time_public > 0 AND time_public < ' . time(), 'time_public DESC', 5, 'id', true);
$arr_news = array();
foreach ($list_news_all as $row) {
    $href = 'http://congly.vn' . Url::Link(array("id" => $row['id'], "title" => $row['title'], 'cate_alias' => $LIST_CATEGORY_ALIAS[$row['cate_id']]), "news", "detail");
    $data = array(
        'title' => $row['title'],
        'description' => $row['description'],
        'img' => IMG::thumb($row, 'cnn_210x140'),
        'href' => $href
    );
    $arr_news[] = $data;
}
header('Content-Type: application/json');
$txt = json_encode($arr_news);
echo $txt;