<?php
if(defined(IN_JOC)) die("Direct access not allowed!");
class AdminLogin extends Form
{
	function __construct()
	{
		Form::__construct($this);
		
	}
	function on_submit()
	{
		require_once 'system/utils/validate.php';
		require_once 'application/main/includes/user.php';
		$userObj = SystemIO::createObject('User');
		$user_name = SystemIO::post('user_name','def','');
		$password= SystemIO::post('password','def','');
		$password_log=$password;
		if(Validate::isUserName($user_name) && Validate::isPassword($password))
		{
			$password=UserCurrent::encodePassword($password);
			$cond="user_name='{$user_name}' AND password='".$password."' AND active=1";
			$user=$userObj->readData('',$cond);
			if(count($user))
			{
				UserCurrent::logIn($user);
				@header('Location:'.ROOT_URL."?app=news&page=admin_news");
			}
		}
	}
	function index()
	{
		Page::setHeader("Đăng nhập hệ thống", "Đăng nhập hệ thống", "Đăng nhập hệ thống");
		joc()->set_file('AdminLogin', Module::pathTemplate()."admin_login.htm");
		//Page::registerFile('AdminLogin', Module::pathJS().'AdminLogin.js' , 'footer', 'js');
		joc()->set_var('begin_form' , Form::begin( false, "POST"));
		joc()->set_var('end_form' 	, Form::end());
		if($_SERVER['HTTP_HOST']=='congly.com.vn')  @header('Location:http://cms.congly.com.vn/?app=main&page=admin_login');
		$cmd=SystemIO::get('cmd','def','login');
		if($cmd=='logout') {
			UserCurrent::logOut();
			@header('Location:?app=main&page=admin_login');
		}
		$html= joc()->output("AdminLogin");
		joc()->reset_var();
		return $html;
	}
}
?>