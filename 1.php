<?php 
    header("content-type:text/html;charset=gbk");
    $link = mysqli_connect("localhost","root","123456","quyou")or die("数据库连接失败");
    mysqli_set_charset($link,"utf-8");
    $U = $_POST['username'];
    $S = $_POST['sopt'];
    $E = $_POST['eva'];
    $rate = $_POST['rate'];
    switch ($rate) {
      case "1":
        $rate = 1;
        break;
      case "2":
        $rate = 2;
        break;
      case "3":
        $rate = 3;
        break;
      case "4":
        $rate = 4;
        break;
      case "5" :
        $rate = 5;
        break;
    }

	$sql = "INSERT INTO `evaluation`(`date`,`U_ID`, `comment`, `S_ID`, `rate`) VALUES (current_timestamp,'$U','$E','$S',$rate)";
    $result = mysqli_query($link,$sql);

    if($result){
        echo"<script>alert('comment!');
            window.location.href='./ll.php';
               </script>";
    }else{
        echo "<script>alert('fault！');
             window.location.href='./ll.php';
               </script>";
    }
?>