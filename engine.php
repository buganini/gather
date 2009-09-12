<?php
ignore_user_abort(true);
include('config.php');
$act=$_POST['action'];
$eventid=intval($_POST['eventid']);
if($act==='fillout'){
	$now=sprintf('%04d%02d%02d',date('Y'),date('n'),date('j'));
	$e=sql('SELECT * FROM `events` WHERE `eventid`="'.$eventid.'"');
	if($now<$e['beg_date']){
		redirect('events.php');
	}elseif($now>$e['end_date']){
		redirect('events.php');
	}
	
	sql_lock();
	$_SESSION['cache']=$fields=$keys=$options=array();
#Save form
	foreach($_POST as $k=>$v){
		$_SESSION['cache'][$k]=$v;
	}

#Preload fields information
	while($e=sql('SELECT * FROM `fields` WHERE `eventid`="'.$eventid.'" AND `admin`="0"')){
		$fields[$e['fieldid']]=$e;
		if($e['key']>0){
			$keys[$e['key']][]=$e['fieldid'];
		}
	}

#Checkbox field preprocess
	while($e=sql('SELECT `fieldid`,`caption` FROM `options` WHERE `eventid`="'.$eventid.'" ORDER BY `optionid` ASC')){
		$options[$e['fieldid']][]=$e['caption'];
	}
	foreach($fields as $k=>$v){
		if($v['type']=='checkbox'){
			post_fix('f'.$k);
			$a=array();
			if(!is_array($options[$k])){
				$options[$k]=array();
			}
			foreach($_POST['f'.$k] as $opt){
				if(in_array($opt,$options[$k]))
				$a[]=$opt;
			}
			$_POST['f'.$k]=implode("\n",$a);
		}
	}
#Validation
	foreach($fields as $k=>$v){
		$func='valid_'.$v['valid'];
		if(!$func($_POST['f'.$k])){
			$_SESSION['msg'][]=$v['caption'].'格式錯誤';
			redirect('event.php?eventid='.$eventid,1);
		}
	}

#Duplication check
	foreach($keys as $k=>$v){
		$ct=0;
		foreach($v as $fid){
			if(sql('SELECT 1 FROM `pool` WHERE `eventid`="'.$eventid.'" AND `fieldid`="'.$fid.'" AND `value`="'.sqlslashes($_POST['f'.$fid]).'"')){
				++$ct;
			}
			if($ct==count($v)){
				$_SESSION['msg'][]='資料重複，您可能已經報名過了喔';
				redirect('event.php?eventid='.$eventid,1);
			}
		}
	}

#Commit
	if($e=sql('SELECT `recordid` FROM `records` WHERE `eventid`="'.$eventid.'" ORDER BY `recordid` DESC LIMIT 0,1')){
		$rid=$e['recordid']+1;
	}else{
		$rid=1;
	}
	sql_insert('records',array('eventid'=>$eventid,'recordid'=>$rid,'time'=>time(),'ip'=>ip()));
	$data=array();
	foreach($fields as $k=>$v){
		$data['eventid'][]=$eventid;
		$data['fieldid'][]=$k;
		$data['recordid'][]=$rid;
		$data['value'][]=$_POST['f'.$k];
	}
	sql_insert('pool',$data);


	unset($_SESSION['cache']);
	redirect('list.php?eventid='.$eventid,1);
}

if(!admin()){
	redirect('events.php');
}

if($act==='save' || $act==='tpl_save'){
	sql_lock();
	if($act==='save'){
		$tpl='';
	}else{
		$tpl='tpl_';
	}
	$eventid=intval($_POST[$tpl.'eventid']);
	$new=$del=$mod=$modid=array();
	post_fix('public');
	post_fix('admin');
	post_fix('fieldid');
	$data=array(
		'title'=>$_POST['title'],
		'desc'=>$_POST['desc'],
		'expose'=>(empty($_POST['expose'])?0:1),
	);
	if(empty($tpl)){
		$data['beg_date']=sprintf('%04d%02d%02d',intval($_POST['beg_y']),intval($_POST['beg_m']),intval($_POST['beg_d']));
		$data['end_date']=sprintf('%04d%02d%02d',intval($_POST['end_y']),intval($_POST['end_m']),intval($_POST['end_d']));
	}

	$fieldid_offset=0;
	if(empty($eventid)){
#Insert event
		if($e=sql('SELECT `eventid` FROM `'.$tpl.'events` ORDER BY `eventid` DESC LIMIT 0,1')){
		#Next event
			$eventid=$e['eventid']+1;
		}else{
		#First event
			$eventid=1;
		}
		$data['eventid']=$eventid;
		sql_insert($tpl.'events',$data);
		foreach($_POST['fieldid'] as $i=>$nul){
			$i=intval($i);
			$new[]=$i;
		}
	}else{
#Update event
		sql_update($tpl.'events',$data,array('eventid'=>$eventid));
		foreach($_POST['fieldid'] as $i=>$nul){
			$i=intval($i);
			if(empty($_POST['fieldid'][$i])){
				#List of field to insetr
				$new[]=$i;
			}else{
				#List of field to update
				$mod[]=$i;
				$modid[]=$_POST['fieldid'][$i];
			}
		}
		while($e=sql('SELECT `fieldid` FROM `'.$tpl.'fields` WHERE `eventid`="'.$eventid.'" ORDER BY `fieldid` ASC')){
			if(!in_array($e['fieldid'],$modid)){
				#List of field to delete
				$del[]=$e['fieldid'];
			}
			if($e['fieldid']>$fieldid_offset){
				$fieldid_offset=$e['fieldid'];
			}
		}
	}

	#Commit update
	$options=array();
	foreach($mod as $i){
		$data=array(
			'caption'=>$_POST['caption'][$i],
			'type'=>$_POST['type'][$i],
			'default'=>$_POST['default'][$i],
			'valid'=>$_POST['valid'][$i],
			'pattern'=>$_POST['pattern'][$i],
			'public'=>(in_array($_POST['dummy'][$i],$_POST['public'])?1:0),
			'admin'=>(in_array($_POST['dummy'][$i],$_POST['admin'])?1:0),
			'key'=>$_POST['key'][$i],
			'order'=>$i,
		);
		sql_update($tpl.'fields',$data,array('eventid'=>$eventid,'fieldid'=>$_POST['fieldid'][$i]));
		$opts=linebreak($_POST['option'][$i]);
		if($_POST['type'][$i]=='checkbox' && count($opts)<2){
			$opts=array($_POST['caption'][$i]);
		}
		for($j=0;$j<count($opts);++$j){
			$options['eventid'][]=$eventid;
			$options['fieldid'][]=$_POST['fieldid'][$i];
			$options['optionid'][]=$j;
			$options['caption'][]=$opts[$j];
		}
	}

	#Commit delete
	for($i=0;$i<count($del);$i++){
		$del[$i]='`fieldid`="'.$del[$i].'"';
	}
	if($i>0){
		sql('DELETE FROM `'.$tpl.'fields` WHERE `eventid`="'.$eventid.'" AND ('.implode(' OR ',$del).')');
		sql('DELETE FROM `'.$tpl.'options` WHERE `eventid`="'.$eventid.'" AND ('.implode(' OR ',$del).')');
	}

	#Commit insert
	$data=array();
	foreach($new as $i){
		$data['fieldid'][]=($fieldid=++$fieldid_offset);
		$data['eventid'][]=$eventid;
		$data['caption'][]=$_POST['caption'][$i];
		$data['type'][]=$_POST['type'][$i];
		$data['default'][]=$_POST['default'][$i];
		$data['valid'][]=$_POST['valid'][$i];
		$data['pattern'][]=$_POST['pattern'][$i];
		$data['public'][]=(in_array($_POST['dummy'][$i],$_POST['public'])?1:0);
		$data['admin'][]=(in_array($_POST['dummy'][$i],$_POST['admin'])?1:0);
		$data['key'][]=$_POST['key'][$i];
		$data['order'][]=$i;
		$opts=linebreak($_POST['option'][$i]);
		if($_POST['type'][$i]=='checkbox' && count($opts)<2){
			$opts=array($_POST['caption'][$i]);
		}
		for($j=0;$j<count($opts);++$j){
			$options['eventid'][]=$eventid;
			$options['fieldid'][]=$fieldid;
			$options['optionid'][]=$j;
			$options['caption'][]=$opts[$j];
		}
	}
	sql_insert($tpl.'fields',$data);

	#Commit options
	sql('DELETE FROM `'.$tpl.'options` WHERE `eventid`="'.$eventid.'"');
	sql_insert($tpl.'options',$options);
	
	redirect('events.php',1);
}elseif($act==='delete' || $act==='tpl_delete'){
	sql_lock();
	if($act==='delete'){
		$tpl='';
		$eventid=intval($_POST[$tpl.'eventid']);
		sql('DELETE FROM `pool` WHERE `eventid`="'.$eventid.'"');
		sql('DELETE FROM `records` WHERE `eventid`="'.$eventid.'"');
	}else{
		$tpl='tpl_';
		$eventid=intval($_POST[$tpl.'eventid']);
	}
	sql('DELETE FROM `'.$tpl.'events` WHERE `eventid`="'.$eventid.'"');
	sql('DELETE FROM `'.$tpl.'fields` WHERE `eventid`="'.$eventid.'"');
	sql('DELETE FROM `'.$tpl.'options` WHERE `eventid`="'.$eventid.'"');
	redirect('events.php',1);
}elseif($act=='adddelta'){
	sql_lock();
	$options=$records=$checkbox=$fields=array();
	while($e=sql('SELECT * FROM `fields` WHERE `eventid`="'.$eventid.'"')){
		$fields[$e['fieldid']]=$e;
	}
	post_fix('node');
	foreach($_POST['node'] as $k=>$v){
		$k=intval($k);
		list($rid,$fid,$oid)=explode('-',$v);
		if($fields[$fid]['type']=='checkbox'){
			$records[]=$rid;
			if(empty($_POST['data'][$k])){
				$checkbox[$rid][$fid][$oid]=false;
			}else{
				$checkbox[$rid][$fid][$oid]=true;
			}
			while($e=sql('SELECT `optionid`,`caption` FROM `options` WHERE `eventid`="'.$eventid.'" AND `fieldid`="'.$fid.'"')){
				$options[$fid][$e['optionid']]=$e['caption'];
			}
		}else{
			if(sql('SELECT 1 FROM `pool` WHERE `eventid`="'.$eventid.'" AND `fieldid`="'.$fid.'" AND `recordid`="'.$rid.'"')){
				sql_update('pool',array('value'=>$_POST['data'][$k]),array('eventid'=>$eventid,'fieldid'=>$fid,'recordid'=>$rid));
			}else{
				sql_insert('pool',array('value'=>$_POST['data'][$k]),array('eventid'=>$eventid,'fieldid'=>$fid,'recordid'=>$rid));
			}
		}
	}
	foreach($records as $rid){
		foreach($checkbox[$rid] as $fid=>$list){
			if($e=sql('SELECT `value` FROM `pool` WHERE `eventid`="'.$eventid.'" AND `fieldid`="'.$fid.'" AND `recordid`="'.$rid.'"')){
				$dlist=$nlist=array();
				$olist=linebreak($e['value']);
				foreach($list as $optid=>$opt){
					if($opt){
						$nlist[]=$options[$fid][$optid];
					}else{
						$dlist[]=$options[$fid][$optid];
					}
				}
				foreach($olist as $opt){
					if((!in_array($opt,$dlist)) && (!in_array($opt,$nlist))){
						$nlist[]=$opt;
					}
				}
				sql_update('pool',array('value'=>implode("\n",$nlist)),array('eventid'=>$eventid,'fieldid'=>$fid,'recordid'=>$rid));
			}else{
				$nlist=array();
				foreach($list as $optid=>$opt){
					if($opt){
						$nlist[]=$options[$fid][$optid];
					}
				}
				sql_insert('pool',array('value'=>implode("\n",$nlist),'eventid'=>$eventid,'fieldid'=>$fid,'recordid'=>$rid));
			}
		}
	}
	post_fix('trash');
	foreach($_POST['trash'] as $v){
		sql('DELETE FROM `records` WHERE `eventid`="'.$eventid.'" AND `recordid`="'.$v.'"');
		sql('DELETE FROM `pool` WHERE `eventid`="'.$eventid.'" AND `recordid`="'.$v.'"');
	}
	redirect('events.php',1);
}
?>