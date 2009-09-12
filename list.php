<?php
include('config.php');
$eventid=$_GET['eventid'];
if($e=sql('SELECT * FROM `events` WHERE `eventid`="'.$eventid.'"')){
	$title=condom($e['title']);
}else{
	redirect('events.php');
}
$opt_pat=$id_pat=$pat=$data=$options=$checkbox=$fields=array();
while($e=sql('SELECT `optionid`,`fieldid`,`caption` FROM `options` WHERE `eventid`="'.$eventid.'" ORDER BY `optionid` ASC')){
	$options[$e['fieldid']][$e['optionid']]=condom($e['caption']);
}
while($e=sql('SELECT * FROM `fields` WHERE `eventid`="'.$eventid.'"'.(admin()?'':' AND public="1"').' ORDER BY `order` ASC')){
	$e['title']=condom($e['title']);
	$fields[$e['fieldid']]=$e;
}
if($_GET['mode']=='fakexls'){
	include('list.fakexls.php');
}else{
	include('list.web.php');
}
?>