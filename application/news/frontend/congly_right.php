<?php
if (defined(IN_JOC))
    die("Direct access not allowed!");
require_once 'application/news/includes/poll_model.php';
require_once 'application/news/includes/poll_option_model.php';

class Congly_right {
    public function index() {
        global $list_category;
        global $infoNews;
        global $linkEsn;
        joc()->set_file('Congly_right', Module::pathTemplate()."frontend/right.htm");
        $frontendObj = new FrontendNews();
        /*Tâm điểm dư luận - góc nhìn*/
        $list_goc_nhin = $frontendObj->getNews('store', 'id,cate_id,description,title,time_created,img1,time_public', 'cate_id = 359', 'time_public DESC', '1');
        foreach ($list_goc_nhin as $row) {
            joc()->set_var('title_gocnhin', $row['title']);
            joc()->set_var('html_title_gocnhin', htmlspecialchars($row['title']));
            joc()->set_var('desc_gocnhin', htmlspecialchars(SystemIO::strLeft($row['description'], 100)));
            joc()->set_var('img_gocnhin', IMG::thumb($row, 'cnn_185x140'));
            joc()->set_var('href_gocnhin', Url::link_detail($row, $list_category));
        }
        /*Đọc nhiều nhất*/
        joc()->set_block('Congly_right','Hit','Hit');
        $list_hit=$frontendObj->getNews('store_hit','nw_id,hit','cate_path NOT LIKE "%,269,%" AND time_created >'.( time() -3*86400),'hit DESC','8');
        $news_ids='';
        foreach($list_hit as $_tmp)
        {
            $news_ids.=$_tmp['nw_id'].',';
        }
        $news_ids=trim($news_ids,',');
        $list_news_hit = array();
        if($news_ids)
        {
            $list_news_hit=$frontendObj->getNews('store','id,cate_id,title,time_created,img1,time_public','id IN ('.$news_ids.')','time_public DESC','5');
        }
        $txt_hit='';
        foreach($list_news_hit as $row)
        {
            joc()->set_var('title_hit',$row['title']);
            joc()->set_var('html_title_hit',htmlspecialchars($row['title']));
            joc()->set_var('img_hit',IMG::thumb($row,'cnn_150x100'));
            joc()->set_var('href_hit',Url::link_detail($row,$list_category));
            $txt_hit.=joc()->output('Hit');
        }

        joc()->set_var('Hit',$txt_hit);
        global $linkEsn;
        joc()->set_var('link_esn', $linkEsn);
        $html = joc()->output("Congly_right");

        joc()->reset_var();
        return $html;
    }

}
