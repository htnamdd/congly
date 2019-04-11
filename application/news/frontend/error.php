<?php
ini_set('display_errors',0);
class Error
{
	function __construct()
	{
		
	}
	function index()
	{		
		joc()->set_file('Error', Module::pathTemplate('news')."frontend".DS."error.htm");	
		Page::setHeader('Congly.com.vn - Trang không tôn tại','Congly.com.vn - Trang không tồn tại','Congly.com.vn - Trang không tồn tại');
		global $LIST_CATEGORY;		
		$html= joc()->output("Error");
		joc()->reset_var();
		return $html;
	}
}
?>