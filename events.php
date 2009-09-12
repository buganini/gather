<?php
include('config.php');
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/jscss/bg.css">
<style type="text/css">
th{text-align:center; background-color:#ccc;}
</style>
</head>
<body>
<?
if(admin()){
echo '<a href="event.php">新增報名表</a>';
}
?>
<table>
<tr><th>#</th><th>活動名稱</th><th>報名開始</th><th>報名截止</th><th>#</th></tr>
<?
$i=0;
while($e=sql('SELECT * FROM `events`'.(admin()?'':' WHERE `expose`="1"').' ORDER BY `eventid` DESC')){
	echo '<tr style="background:#'.($i%2==0?'eee':'fff').';"><td>'.$e['eventid'].'</td><td><a href="event.php?eventid='.$e['eventid'].'">'.($e['title']==''?'&nbsp;':condom($e['title'])).'</a></td><td>'.$e['beg_date'].'</td><td>'.$e['end_date'].'</td><td><a href="list.php?eventid='.$e['eventid'].'">報名清單</a></td></tr>';
	++$i;
}
if($i==0){
	echo '<tr><td colspan="5" align="center">目前沒有活動</td></tr>';
}
?>
</table>
</body>
</html>