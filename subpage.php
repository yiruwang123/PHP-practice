<?php
if(!isset($_GET['name'])){
    exit('非法访问');
}
include('Model.php');
$config = include 'config.php';
$m = new Model($config);

$name = $_GET['name'];
$username = $_GET['username'];
//var_dump($username);
$che = $m->table('spot')->where('S_name="'.$name.'"')->select();
$res = $che[0];
//var_dump($res);

$che2 = $m->table('place')->where('S_ID='.$res['S_ID'])->select();
$res2 = $che2[0];
//var_dump($res2);

$imgp = $m->table('introduction')->where('S_ID='.$res['S_ID'])->select();
//var_dump($imgp);
$img = $imgp[0]['picture'];
$img = substr($img, 2)
//var_dump($img);

$root = $m->table('route')->where('S_ID='.$res['S_ID'])->select();
//$root = $root[0];
//var_dump($root);

$avg = $m->table('evaluation')->group('S_ID')->having('S_ID='.$res['S_ID'])->avg('rate');
//var_dump((float)$avg);

$act = $m->table('campaign')->where('S_ID='.$res['S_ID'])->select();
//var_dump($act);

if(isset($_POST['comment'])){
    $comment = $_POST['commentDetails'];
    $data = ['comment'=> $comment];
    $ser = $m->table('evaluation')->where('U_ID='.$username.' and S_ID='.$res['S_ID'])->select();
    if($ser){
        $sql = 'update evaluation set date=NOW() where U_ID="'.$username.'" and S_ID="'.$res['S_ID'].'"';
        $sql2= 'update evaluation set comment="'.$comment.'" where U_ID="'.$username.'" and S_ID="'.$res['S_ID'].'"';
       //var_dump($sql2);
        $r = $m->exec($sql);
        $m->exec($sql2);
    }else{
        $sql = 'insert into evaluation(date,U_ID,comment,S_ID) values(NOW(),"'.$username.'","'.$comment.'","'.$res['S_ID'].'")';
        //var_dump($sql);
        $r = $m->exec($sql ,true);
    }
}

$com = $m->table('evaluation')->where('comment<>""')->order('date asc')->select();
$com = array_reverse($com);
$q = substr($com[0]['date'], 0,19);
//var_dump($com);
?>
<html>
    <head>
        <meta charset="utf-8" />
        <meta name="for" content="#" />
        <title>sample</title><!--这个title能不能改为当前层名值-->
    </head>
    <body>
        <div class="ID" >
           <?php echo $username?>
           <a href="../dengluxiugai/index.php" >注销账号
        </div>
        <div id = "app">
            <section class="detail-wrap" data-reactroot="">
                <div class="content">
                    <div class="breadcrumbs">
                        <span>
                            <!--回首页-->
                            <a href="homepage.php?username=<?php echo $username?>">首页></a>
                        </span>
                        <span>
                            <!--当前层-->
                            <a><?php echo $res['S_name'];?></a><!--调用数据库spotname修改-->
                        </span>
                    </div>
                    <div class = "details clear">
                        <div class="d-left">
                                <div class="name&scores">&nbsp;&nbsp;&nbsp;</div>
                                <div class="score">
                                    <form action='pingfen.php?username=<?php echo $username?>&name=<?php echo $name?>&S_ID=<?php echo $res['S_ID']?>' method='post'>
                                        评分：
                                        <select name="scores" >
                                            <option value="0">0</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                        <input type="submit" name="search" value="提交">
                                    </form>
                                    <p>平均分：<?php echo (float)$avg?></p><!--怎么返回平均值，将平均值赋给a，打印出来-->
                                </div>
                                <div class="details">
                                    <p>地址：<?php echo $res2['location'];?></p>
                                    <p>距离：<?php echo $res2['distance'].'km';?></p>
                                    <p>营业时间：<?php echo $res['B_hour'];?></p>
                                    <p>路线推荐：<?php  
                                    $i=0;
                                    $l=count($root);
                                    echo '<br />';
                                     while($i<$l){
                                        $i=$i+1;
                                        echo '路线'.$i.' : '.$root[$i-1]['Description']."&nbsp;&nbsp;&nbsp;&nbsp;".$root[$i-1]['CostTime'].'<br />'; 
                                     }
                                    ?></p>
                                    <!--路线和对应时间-->
                                </div>
                        </div>
                        <div class="d-right">
                            <div class="imagebox" >
                              <img src="<?php echo $img;?>" style="width: 50%; height: 50%;"/>
                                            <!--picture调用相应的绝对路径，赋给src值-->
                            </div>
                        </div>
                        <div><span></span></div>
                    </div>
                    <div class="btm-left">
                        <div>
                            <h3>活动相关</h3>
                            <div class="campaign">
                                <p>活动内容</p>
                                <p><?php
                                $i=0;
                                $l=count($act);
                                while($i<$l){
                                    $i=$i+1;
                                    echo 'No.'.$i.' '.$act[$i-1]['C_introduction'].'<br />';
                                }
                                ?></p>
                            </div>

                        </div>
                        <div class="comment">
                            <h3>用户评论</h3>
                        </div>
                        <div>
                            <div class="list clear">
                                <?php
                                foreach ($com as $key => $value) {
                                    $date = substr($value['date'],0,19);
                                    echo '学号：'.$value['U_ID'].'<br />';
                                    echo '日期：'.$date.'<br />';
                                    echo '实际评论内容：'.$value['comment'].'<br />';
                                    echo '<br />';

                                }
                                ?>
                            </div>
                        </div>
                        <form action="subpage.php?<?php echo 'username='.$username.'&name='.$name;?>"  method='post'>
                            <textarea name="commentDetails" placeholder="请输入您的评论：" style="width: 500px; height: 100px"></textarea>
                            
                             <input type="submit" name='comment' value="提交">
                            
                        </form>
                        

                    </div>
                </div>
            </section>
        </div>
    </body>
    <script>
        document.getElementsByTagName("title")[0].innerText='当前层名'//php调用数据库数据spotname修改‘’中的值，
    </script>
</html>