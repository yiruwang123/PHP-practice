<?php
include('Model.php');

$config = include 'config.php';
$m = new Model($config);

//登录value中 要设置name为submit
if(!isset($_POST['submit'])){
	exit("非法访问");
}

$username = $_POST['username'];
$password = $_POST['password'];
//$pwd = $_POST['pwd'];

//检测用户名以及密码是否正确
$che = $m->table('user')->where("U_ID='".$username."' and Password ='".$password."'")->select();
//var_dump($m->sql);
if($che){
	//登录成功
	echo '登陆成功，欢迎。 <a href="javascript:history.back(-1);">返回</a>';
	exit;
}else{
	exit('登录失败，请重试。 <a href="javascript:history.back(-1);">返回</a>');
}
