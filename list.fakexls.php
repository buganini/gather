<?php
if(!admin()){
	exit;
}
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="'.$title.'.xls"');
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<table id="list" width="100%">
<?php
$th2=$th='<tr>';
foreach($fields as $e){
	if($e['type']=='checkbox'){
		$checkbox[$e['fieldid']]=true;
		if(count($options[$e['fieldid']])==1){
			$th.='<th rowspan="2">'.$e['caption'].'</th>';
		}else{
			$th.='<th colspan="'.count($options[$e['fieldid']]).'">'.$e['caption'].'</th>';
			$th2.='<th>'.implode('</th><th>',$options[$e['fieldid']]).'</th>';
		}
		foreach($options[$e['fieldid']] as $k=>$v){
			$opt_pat[]=$k;
			$pat[]=$v;
		}
		$id_pat=array_pad($id_pat,count($id_pat)+count($options[$e['fieldid']]),$e['fieldid']);
	}else{
		if($e['type']=='radio'){
			$pat[]=false;
		}else{
			$pat[]=true;
		}
		$opt_pat[]=0;
		$th.='<th rowspan="2">'.$e['caption'].'</th>';
		$id_pat[]=intval($e['fieldid']);
	}
}
$th.='</tr>'."\n".$th2.'</tr>'."\n";
while($e=sql('SELECT `recordid`,`fieldid`,`value` FROM `pool` WHERE `eventid`="'.$eventid.'" ORDER BY `recordid` DESC')){
	$e['value']=condom($e['value']);
	if($checkbox[$e['fieldid']]){
		$e['value']=linebreak($e['value']);
	}
	$data[$e['recordid']][$e['fieldid']]=$e['value'];
}
$i=0;
echo $th;
while($e=sql('SELECT `recordid` FROM `records` WHERE `eventid`="'.$eventid.'" ORDER BY `recordid` DESC')){
	++$i;
	if($i%30==0){
		echo $th;
	}
	echo '<tr style="background:#'.($i%2==0?'fff':'eee').';">';
	foreach($id_pat as $idx=>$fid){
		if($pat[$idx]===true){
		#other
			echo '<td>';
			echo str_replace("\n",'<br />',$data[$e['recordid']][$fid]);
		}elseif($pat[$idx]===false){
		#radio
			echo '<td align="center">';
			echo $data[$e['recordid']][$fid];
		}else{
		#checkbox
			echo '<td align="center">';
			if(!isset($data[$e['recordid']][$fid])){
				$data[$e['recordid']][$fid]=array();
			}
			echo in_array($pat[$idx],$data[$e['recordid']][$fid])?'v':'';
		}
		echo '</td>';
	}
	echo '</tr>'."\n";
}
?>
</table>
</body>
</html>
