<?php
if (defined(IN_JOC)) die("Direct access not allowed!");
require_once 'application/news/frontend/includes/class.xml.php';

class Congly_footer
{
    function index()
    {
        joc()->set_file('Congly_footer', Module::pathTemplate('news') . "frontend/footer.htm");
        $cate_id = SystemIO::get('cate_id', 'int', 0);
        $page = SystemIO::get('page', 'def', 'congly_home');
        joc()->set_var('page',$page);
        $cmd = SystemIO::get('cmd', 'def', '');
        $frontendObj = new FrontendNews();
        $list_topic = $frontendObj->getNews('topic', '*', 'property =4', 'id DESC', 5);
        joc()->set_block('Congly_footer', 'Topic', 'Topic');
        $text_html = '';
        foreach ($list_topic as $row) {
            joc()->set_var('title', $row['name']);
            joc()->set_var('html_title', htmlspecialchars($row['name']));
            if ($row['img'])
                joc()->set_var('img', 'https://congly.vn/data/topic/' . $row['img']);
            else
                joc()->set_var('img', 'https://congly.vn/webskins/skins/news/images/noimg.jpg');
            joc()->set_var('href', Url::Link(array('topic_id' => $row['id'], 'name' => $row['name']), 'news', 'topic'));
            $text_html .= joc()->output("Topic");
        }
        joc()->set_var('Topic', $text_html);


        $id = SystemIO::get('id', 'int', 0);

        global $infoNews;
        if ($id)
            $cate_id = $infoNews['cate_id'];

        if ($cate_id) {
            Page::setGA($this->showGA($cate_id));
        } else {
            if ($cmd == 'video')
                Page::setGA($this->showGA('353'));
        }
        if($page == 'congly_home')
        {
            joc()->set_var('_link','<a href="https://giadinh.tv/cach-lam-sua-chua-tu-sua-ong-tho/">Cách làm sữa chua</a>|<a href="https://giadinh.tv/cach-nau-bo-kho-10-diem-thom-ngon-ngay-tai-nha-rat-don-gian/">Món bò kho</a>|<a href="https://giadinh.tv/cach-lam-khoai-tay-chien/">Khoai tây chiên</a>');
        }
        else
            joc()->set_var('_link','');

        $html = joc()->output("Congly_footer");
        joc()->reset_var();
        return $html;
    }

    function showGA($cate_id)
    {
        global $list_category;
        $cate_id = $list_category[$cate_id]['cate_id1'] == 0 ? $cate_id : $list_category[$cate_id]['cate_id1'];
        if ($cate_id == '307')
            $ga = "<script>
			ga('create', 'UA-53202552-11', 'congly.com.vn', {'name': 'newTracker'});
					ga('newTracker.send', 'pageview');

		  </script>";
        elseif ($cate_id == '372')
            $ga = "<script>
					ga('create', 'UA-53202552-7', 'congly.com.vn', {'name': 'newTracker'});
					ga('newTracker.send', 'pageview');
				</script>";
        elseif ($cate_id == '293') {
            $ga = "<script>
					ga('create', 'UA-53202552-6', 'congly.com.vn', {'name': 'newTracker'});
					ga('newTracker.send', 'pageview');
			</script>";
        } elseif ($cate_id == '298') {
            $ga = "<script>
	ga('create', 'UA-53202552-9', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";

        } elseif ($cate_id == '288') {
            $ga = "<script>
						ga('create', 'UA-53202552-4', 'congly.com.vn', {'name': 'newTracker'});
						ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '273') {
            $ga = "<script>
						ga('create', 'UA-53202552-3', 'congly.com.vn', {'name': 'newTracker'});
						ga('newTracker.send', 'pageview');

				</script>";
        } elseif ($cate_id == '303') {
            $ga = "<script>
ga('create', 'UA-53202552-10', 'congly.com.vn', {'name': 'newTracker'});
						ga('newTracker.send', 'pageview');

</script>";
        } elseif ($cate_id == '314') {
            $ga = "<script>
 ga('create', 'UA-53202552-8', 'congly.com.vn', {'name': 'newTracker'});
						ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '269') {
            $ga = "<script>
			ga('create', 'UA-53202552-2', 'congly.com.vn', {'name': 'newTracker'});
			ga('newTracker.send', 'pageview');
		</script>";

        } elseif ($cate_id == '283') {
            $ga = "<script>
 ga('create', 'UA-53202552-5', 'congly.com.vn', {'name': 'newTracker'});
						ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '353') {
            $ga = "<script>
   ga('create', 'UA-53202552-12', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '384') {
            $ga = "<script>
   ga('create', 'UA-53202552-14', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '386') {
            $ga = "<script>
   ga('create', 'UA-53202552-15', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == '359') {
            $ga = "<script>
   ga('create', 'UA-53202552-16', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";
        } elseif ($cate_id == 338) {
            $ga = "<script>
   ga('create', 'UA-53202552-18', 'congly.com.vn', {'name': 'newTracker'});
	ga('newTracker.send', 'pageview');
</script>";
        }

        return $ga;
    }
}