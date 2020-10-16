<?php

if(isset($_POST['username'])){
include('Model.php');

$config = include 'config.php';
$m = new Model($config);

//登录value中 要设置name为submit
//if(!isset($_POST['submit'])){
//    exit("非法访问");
//}

$username = $_POST['username'];
$password = $_POST['password'];
//$pwd = $_POST['pwd'];

//检测用户名以及密码是否正确
$che = $m->table('user')->where("U_ID='".$username."' and Password ='".$password."'")->select();
//var_dump($m->sql);
if(!$che){
    exit('登录失败，请重试。 <a href="javascript:history.back(-1);">返回</a>');
}
}

if(isset($_GET['username'])){
    $username = $_GET['username'];
}
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>主页</title>

</head>
<body>
<h1 align="center">趣游查询系统</h1>
<div class="ID" >
<?php echo $username?>

    <a href="../dengluxiugai/index.php" >注销账号
  
    </div>

   <form align="center" action='homepage.php?username=<?php echo $username;?>' method='post'>
<select name="spottype" >
<option value="全部分类">全部分类</option>
<option value="自然风光">自然风光</option>
<option value="展馆展览">展馆展览</option>
<option value="名胜古迹">名胜古迹</option>
<option value="公园游乐场">公园游乐场</option>
</select>
<select name="distance" >
    <option value=">0">全部距离</option>
        <option value="<20">距离<20km</option>
        <option value=">20&&<30">距离>20并且<30km</option>
        <option value=">30&&<50">距离>30并且<50km</option>
        <option value=">50">距离>50km</option>
        </select>
        <input type="text" name="spotname" placeholder = "请输入地点名">
   <input type="submit" name="submit" value="查询"></form>
<p>
    <img src="https://img.taopic.com/uploads/allimg/120520/159368-120520191P753.jpg" alt="" width="200" height="200">
</p>

</body>
</html>


<?php
if(!(isset($_POST['spottype'])||isset($_POST['distance'])||isset($_POST['spotname']))){
    exit;
}else{
    $spottype = $_POST['spottype'];
    $distance = $_POST['distance'];
    $spotname = $_POST['spotname'];
}
if($spottype == '全部分类'){
    $spottype = '';
}else{
    $spottype = ' and spot.S_kind="'.$spottype.'"';
}
$distance = trim($distance);
if(strlen($distance)<=3){
     $distance = ' and place.distance'.$distance;
}else{
    $s = substr($distance, 0, 2);
    $s2 = substr($distance, 5, 7);
    $distance = ' and place.distance'.$s.' and place.distance'.$s2;
}

if($spotname == ''||$spotname == '请输入地点名'){
    $spotname = '';
}else{
    $spotname = ' and spot.S_name like "%'.$spotname.'%"';
}

include('Model.php');
$config = include 'config.php';
$m = new Model($config);
//var_dump($spotname,$spottype);
$che = $m->field('S_name')->table('spot,place')->where('spot.S_ID=place.S_ID'.$spottype.
$spotname.$distance)->select();
//foreach ($che as $key => $value) {
//    var_dump($value);
  //}
//var_dump($m->sql);
//var_dump($che);
if($che){
foreach ($che as $key => $value) {
    echo '<a href=subpage.php?username='.$username.'&name='.$value['S_name'].'>'.$value['S_name'].'</a><br />';
}
}else{
    echo '结果不存在';
}





