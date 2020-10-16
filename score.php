<?php

include('Model.php');
$config = include 'config.php';
$m = new Model($config);

$username = $_GET['username'];
$name = $_GET['name'];
$S_ID = $_GET['S_ID'];
$sor = $_POST['scores'];
//var_dump($username,$S_ID);
$che = $m->table('evaluation')->where('U_ID='.$username.' and S_ID='.$S_ID)->select();

$data = ['rate'=>(int)$sor];
//var_dump($che);
$sql = 'insert into evaluation(date,U_ID,comment,S_ID,rate) values(NOW(),"'.$username.'","","'.$S_ID.'",'.$sor.')';
//var_dump($sql);
if(!$che){
	$res = $m->exec($sql,true);
	echo '评价成功. &nbsp;&nbsp;&nbsp;&nbsp;<a href="subpage.php?username='.$username.'&name='.$name.'">返回</a>';	
}else{
	//var_dump($S_ID);
	$m->table('evaluation')->where('U_ID="'.$username.'" and S_ID="'.$S_ID.'"')->update($data);
	echo '评价成功. &nbsp;&nbsp;&nbsp;&nbsp;<a href="subpage.php?username='.$username.'&name='.$name.'">返回</a>';
} 