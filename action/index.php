<?php 
$conn = @mysql_connect('192.168.190.91','root','123456');
if(!$conn){
    die("数据库连接失败".mysql_error());
}
mysql_select_db('wish',$conn);
	$sql = 'SELECT * FROM msg';
	$result = mysql_query($sql);
	while($row = mysql_fetch_array($result))
	{
		var_dump( $row);
	}
	
?>