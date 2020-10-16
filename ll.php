<!DOCTYPE html>
<html>
<head>
	<meta chaset="UTF-8">
	<title>评论功能</title>
</head>
<body>
	<h3 align="center">用户评价</h3>
		<table align="center" width="800" border="1" cellpadding="0" 
             cellspacing="0">
			<tr>
				<th>评论时间</th>
				<th>用户ID</th>
				<th>评 级</th>
				<th>评 价</th>
			</tr>
			<?php
				$link = mysqli_connect("localhost","root","123456","quyou")or die("数据库连接失败");
				$sql = "select * from evaluation";
		    	$result = mysqli_query($link,$sql);
		    	if($result){
		    		while($row = mysqli_fetch_assoc($result)){
		    ?>
		    <tr>
				<td align="center"><?php echo $row["date"];?></td>
				<td align="center"><?php echo $row["U_ID"];?></td>
				<td align="center"><?php echo $row["rate"];?></td>
				<td align="center"><?php echo $row["comment"];?></td>
			</tr>
			<?php 
					}
		    	}
		    	mysqli_close($link);	
			?>
		</table>

	<form action="./1.php" method="post">
		<label>用 户 名：</label>
		<input type="text" size="40" name="username" placeholder="请输用户名"/><br>
		<label>景 点 名：</label>
		<input type="text" size="40" name="sopt" placeholder="请输入景点名"/><br>
        <label>评    级：</label>
        <lable>
			<input type="radio" name="rate" checked="checked"/>1星
		</label>
		<label>
			<input type="radio" name="rate" value="1"/>2星
		</label>
		<label>
			<input type="radio" name="rate" value="2" />3星
		</label>
		<label>
			<input type="radio" name="rate" value="3" />4星
		</label>
		<label>
			<input type="radio" name="rate" value="4" />5星
		</label><br>
        <label>评     价：</label>
        <textarea name="eva" placeholder="请输入评价..."></textarea>  
		<input type="submit" value="提交"value="提交"/>
	</form>
</body>
</html>