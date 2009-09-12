<?
/*
THIS FILE IS SO F**KING DIRTY.....
*/
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?echo $title;?> </title>
<link rel="stylesheet" type="text/css" href="/jscss/bg.css">
<style type="text/css">
th{text-align:center; background-color:#ccc;}
.text{
	width:100%;
	border:none 0;
	background:#ddd;
}
.select{
	width:100%;
	border:none 0;
	background:#ddd;
	text-align:center;
}
</style>
<?
if(admin()){
?>
<script type="text/javascript" src="/jscss/enable_tab.js"></script>
<script type="text/javascript">
var delta=new Object();
var lock=false;

function getelm(id){
	return document.getElementById(id);
}

function htmlspecialchars(s) {
	s = s.replace(/&/g,'&amp;');
	s = s.replace(/\"/g,'&quot;');
	s = s.replace(/\'/g,'&#039;');
	s = s.replace(/</g,'&lt;');
	s = s.replace(/>/g,'&gt;');
	return s;
}

function delRow(rid,obj){
	getelm('list').deleteRow(obj.parentNode.parentNode.rowIndex);
	var tbl = getelm('del');
	var newRow = tbl.insertRow(-1);
	var newCell0 = newRow.insertCell(0);
	var newInput = document.createElement('input');
	newInput.type = 'hidden';
	newInput.name = 'trash[]';
	newInput.value = rid;
	newCell0.appendChild(newInput);
}

function saabmeat(){
	for( var rid in delta){
		for( var fid in delta[rid]){
			for( var oid in delta[rid][fid]){
				var tbl = getelm('delta');
				var newRow = tbl.insertRow(-1);
				var newCell0 = newRow.insertCell(0);
				var newInput = document.createElement('input');
				newInput.type = 'hidden';
				newInput.name = 'node[]';
				newInput.value = rid+'-'+fid+'-'+oid;
				newCell0.appendChild(newInput);

				var newCell1 = newRow.insertCell(1);
				var newInput = document.createElement('input');
				newInput.type = 'hidden';
				newInput.name = 'data[]';
				newInput.value = delta[rid][fid][oid];
				newCell1.appendChild(newInput);
			}
		}
	}
	getelm('form').submit();
}

function toggle(id,a,b){
	var obj=getelm(id);
	if(obj.innerHTML==a){
		obj.innerHTML=b;
		return b;
	}else{
		obj.innerHTML=a;
		return a;
	}
}

function beg(rid,fid,oid){
	if(lock){
		return;
	}
	lock=true;
	if(typeof(delta[rid])=='undefined'){
		delta[rid]=new Object();
	}
	if(typeof(delta[rid][fid])=='undefined'){
		delta[rid][fid]=new Object();
	}
	switch(fid){
<?			foreach($fields as $f){
			echo 'case '.$f['fieldid'].":\n";
			if($f['type']=='checkbox'){
				echo 'switch(oid){';
					foreach($options[$f['fieldid']] as $k=>$v){
						echo 'case '.$k.":\n";
						echo 'delta[rid][fid][oid]=toggle(rid+\'-\'+fid+\'-\'+oid,\'\',\'&#711;\');';
						echo 'lock=false;';
						echo "break;\n";
					}
				echo '}';
			}elseif($f['type']=='radio'){
				echo 'var bak=getelm(rid+\'-\'+fid+\'-0\').innerHTML;';
				echo 'var str=\'<select id="input" onBlur="lock=false; getelm(\\\'\'+rid+\'-\'+fid+\'-0\\\').innerHTML=htmlspecialchars(this.value); delta[\'+rid+\'][\'+fid+\'][\'+oid+\']=this.value;">';
				if(!isset($options[$f['fieldid']])){
					$options[$f['fieldid']]=array();
				}
				foreach($options[$f['fieldid']] as $v){
					echo '<option value="'.$v.'"\'+(\''.$v.'\'==bak?\' selected="selected"\':\'\')+\'>'.$v.'</option>';
				}
				echo '</select>\';';
				echo 'getelm(rid+\'-\'+fid+\'-\'+oid).innerHTML=str;';
				echo 'getelm(\'input\').focus();';
			}elseif($f['type']=='line'){
				echo 'var bak=getelm(rid+\'-\'+fid+\'-0\').innerHTML;';
				echo 'var str=\'<input type="text" id="input" onBlur="lock=false; getelm(\\\'\'+rid+\'-\'+fid+\'-0\\\').innerHTML=htmlspecialchars(this.value); delta[\'+rid+\'][\'+fid+\'][\'+oid+\']=this.value;" />\';';
				echo 'getelm(rid+\'-\'+fid+\'-\'+oid).innerHTML=str;';
				echo 'getelm(\'input\').value=bak;';
				echo 'getelm(\'input\').focus();';
			}else{
				echo 'var bak=getelm(rid+\'-\'+fid+\'-0\').innerHTML.replace(/<br[^>]*>/gi,\'\\n\');';
				echo 'var str=\'<textarea id="input" onBlur="lock=false; getelm(\\\'\'+rid+\'-\'+fid+\'-0\\\').innerHTML=htmlspecialchars(this.value.replace(/\\\\n/g,\\\'<br />\\\')); delta[\'+rid+\'][\'+fid+\'][\'+oid+\']=this.value;"></textarea>\';';
				echo 'getelm(rid+\'-\'+fid+\'-\'+oid).innerHTML=str;';
				echo 'getelm(\'input\').value=bak;';
				echo 'getelm(\'input\').focus();';
			}
			echo "break;\n";
		}?>
	}
}
</script>
<?}?>
</head>
<body>
<?
if(admin()){?>
<a href="//<?=$_SERVER['SERVER_NAME'];?>:8051/signup/list.php?eventid=<?=$_GET['eventid'];?>&mode=fakexls">下載清單</a>
<?}
?>
<h2><?echo $title;?></h2>
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
if(admin()){
	$th.='<th rowspan="2">#</th>';
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
			echo admin()?'<td id="'.$e['recordid'].'-'.$fid.'-'.$opt_pat[$idx].'" onClick="beg('.$e['recordid'].','.$fid.','.$opt_pat[$idx].')">':'<td>';
			echo str_replace("\n",'<br />',$data[$e['recordid']][$fid]);
		}elseif($pat[$idx]===false){
		#radio
			echo admin()?'<td align="center" id="'.$e['recordid'].'-'.$fid.'-'.$opt_pat[$idx].'"" onClick="beg('.$e['recordid'].','.$fid.','.$opt_pat[$idx].')">':'<td align="center">';
			echo $data[$e['recordid']][$fid];
		}else{
		#checkbox
			echo admin()?'<td align="center" id="'.$e['recordid'].'-'.$fid.'-'.$opt_pat[$idx].'" onClick="beg('.$e['recordid'].','.$fid.','.$opt_pat[$idx].')">':'<td align="center">';
			if(!isset($data[$e['recordid']][$fid])){
				$data[$e['recordid']][$fid]=array();
			}
			echo in_array($pat[$idx],$data[$e['recordid']][$fid])?'&#711;':'';
		}
		echo '</td>';
	}
	if(admin()){
		echo '<td align="center"><input type="button" onClick="delRow('.$e['recordid'].',this);" value="X" /></td>';
	}
	echo '</tr>'."\n";
}
$width=count($id_pat);
if(admin()){
	++$width;
}
echo '<tr><td colspan="'.$width.'" align="center">共有'.$i.'個人報名</td></tr>';
?>
</table>
<?php
if(admin()){
?>
<form action="engine.php" method="POST" id="form">
<input type="hidden" name="action" value="adddelta" />
<input type="hidden" name="eventid" value="<?echo $eventid;?>" />
<table id="delta" style="display:none;">
</table>
<table id="del" style="display:none;">
</table>
<input type="button" onclick="saabmeat(); this.disabled=true;" value="儲存變更" />
</form>
<?
}
?>
</body>
</html>