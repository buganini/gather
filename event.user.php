<?php
$eventid=$_GET['eventid'];
if((!($e=sql('SELECT * FROM `events` WHERE `eventid`="'.$eventid.'"'))) || ($e['expose']==0)){
	redirect('events.php');
}
$now=sprintf('%04d%02d%02d',date('Y'),date('n'),date('j'));
if($now<$e['beg_date']){
	$dur=-1;
	$not=' disabled="disabled"';
}elseif($now>$e['end_date']){
	$dur=1;
	$not=' disabled="disabled"';
}else{
	$dur=0;
	$not='';
}
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?echo $e['title'];?> </title>
<link rel="stylesheet" type="text/css" href="/jscss/bg.css">
<script type="text/javascript" src="/jscss/enable_tab.js"></script>
<style type="text/css">
input, textarea{
border: solid 1px #555;
}
.board{
border-style:dashed;
border-color:#555;
border-width:5px;
margin:3em;
padding:3em;
background:url('/images/macbg.png');
}
td{
font-size:10pt;
}
</style>
</head>
<body>
<form action="engine.php" method="POST" id="form" class="board">
<input type="hidden" name="action" value="fillout" />
<input type="hidden" name="eventid" value="<?echo $eventid;?>" />
<?
switch($dur){
	case -1:
		echo '<div>報名尚未開始</div>';
		break;
	case 1:
		echo '<div>報名已經結束</div>';
		break;
}
?>
<?
if(count($_SESSION['msg'])>0){
	echo '<div style="color:#f00;">'.implode("<br />\n",$_SESSION['msg']).'</div>';
	unset($_SESSION['msg']);
}
?>
<div><? echo $e['desc']; ?></div>
<hr style="margin:1em;" />
<table>
<?php
while($e2=sql('SELECT * FROM `fields` WHERE `eventid`="'.$eventid.'" AND `admin`="0" ORDER BY `order` ASC')){
	echo '<tr><td>'.$e2['caption'].'</td><td>';
	$func='mk_'.$e2['type'];
	$func($eventid,$e2);
	echo '</td></tr>'."\n";
}
?>
</table>
<br />
<input type="submit" onclick="document.getElemenyById('form').submit(); this.disabled=true;" value="報名表"<?echo $not;?> />
</form>
</body>
</html>
