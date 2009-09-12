<?php
$dbhost='localhost';
$dbuser='root';
$dbpass='';
$dbname='signup';

############################################################
include('../auth/inc.php');
ini_set('date.timezone', 'Asia/Taipei');
#session_save_path('sessions');
require_once(dirname(__FILE__).'/../session.php');
mysql_pconnect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbname);
mysql_query('SET NAMES \'UTF8\'');

if(get_magic_quotes_gpc()==1){
	deslashes($_POST);
	deslashes($_GET);
}
$_GET['eventid']=intval($_GET['eventid']);

function deslashes(&$s){
	if(is_array($s)){
		foreach($s as $k=>$v){
			deslashes($s[$k]);
		}
	}elseif(is_string($s)){
		$s=stripslashes($s);
	}
}

function redirect($url,$unlock=0){
	if($unlock>0){
		sql('UNLOCK TABLES');
	}
	header('Location: '.$url);
	die();
}

function ip(){
	$a=array();
	if(!empty($_SERVER['REMOTE_ADDR'])){
		$a[]=$_SERVER['REMOTE_ADDR'];
	}
	if(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$a[]=preg_replace('[^A-Z0-9.]','',$_SERVER['HTTP_X_FORWARDED_FOR']);
	}
	return implode('-',$a);
}

function pretry($a,$b){
	global $preload;
	if(isset($preload[$a])){
		return $preload[$a];
	}
	return $b;
}

function condom($s){
	return htmlspecialchars($s, ENT_QUOTES);
}

function ampty($s){
	if($s==='0'){
		return false;
	}
	return empty($s);
}

function linebreak($s){
	$arr=explode("\n",$s);
	$ret=array();
	foreach($arr as $a){
		$a=trim($a);
		if(!ampty($a) && (!in_array($a,$ret))){
			$ret[]=$a;
		}
	}
	return $ret;
}

function strize($s){
	$s=condom($s);
	$o='';
	$l=strlen($s);
	for($i=0;$i<$l;++$i){
		$c=substr($s,$i,1);
		switch($c){
			case '\\': $o.='\\\\'; break;
			case "\n": $o.='\\n'; break;
			case "\r": break;
			default:
				$o.=$c;
		}
	}
	return $o;
}

function sqlslashes($s){
	return mysql_real_escape_string($s);
}

function sql($sql,$renew=NULL){
	global $SQLres;
	if(!isset($SQLres[$sql]) || $renew!==NULL){
		$SQLres[$sql]=mysql_query($sql);
	}
	if($SQLres[$sql]===true){
		unset($SQLres[$sql]);
		return mysql_affected_rows();
	}elseif($SQLres[$sql]){
		$ret=mysql_fetch_assoc($SQLres[$sql]);
		if(!$ret && mysql_num_rows($SQLres[$sql])>0){
			mysql_data_seek($SQLres[$sql],0);
		}
		return $ret;
	}
	return false;
}

function sql_insert($table,$res){
	if(count($res)==0){
		return 0;
	}
	$res2=$fields=array();
	$n=0;
	foreach($res as $k=>$v){
		$fields[]='`'.$k.'`';
		if(!is_array($v)){
			$res2[$k]=array($v);
		}else{
			$res2[$k]=$v;
		}
		$t=count($res2[$k]);
		if($n>0 && $n!=$t){
			return false;
		}
		$n=$t;
	}
	$dat=array();
	for($i=0;$i<$n;++$i){
		$t=array();
		foreach($res2 as $v){
			$t[]='"'.sqlslashes($v[$i]).'"';
		}
		$dat[]='('.implode(',',$t).')';
	}
	return sql('INSERT INTO `'.$table.'` ('.implode(',',$fields).') VALUES '.implode(',',$dat));
}

function sql_lock(){
#XXX
	sql('LOCK TABLES');
}

function sql_update($table,$res,$cond){
	$dat=$cnd=array();
	foreach($res as $key => $val){
		$dat[]='`'.$key.'`="'.mysql_real_escape_string($val).'"';
	}
	foreach($cond as $key => $val){
		$cnd[]='`'.$key.'`="'.mysql_real_escape_string($val).'"';
	}
	return sql('UPDATE `'.$table.'` SET '.implode(',',$dat).' WHERE ('.implode(' AND ',$cnd).')');
}

function post_fix($s){
	if(!isset($_POST[$s])){
		$_POST[$s]=array();
	}
}

function mk_line($eid,$f){
	global $not;
	$fid=$f['fieldid'];
	if(isset($_SESSION['cache']['f'.$fid])){
		$defval=$_SESSION['cache']['f'.$fid];
	}else{
		$defval=$f['default'];
	}
	echo '<input type="text" name="f'.$fid.'" value="'.condom($defval).'"'.$not.' />';
}

function mk_textarea($eid,$f){
	global $not;
	$fid=$f['fieldid'];
	if(isset($_SESSION['cache']['f'.$fid])){
		$defval=$_SESSION['cache']['f'.$fid];
	}else{
		$defval=$f['default'];
	}
	echo '<textarea name="f'.$fid.'"'.$not.'>'.condom($defval).'</textarea>';
}

function mk_radio($eid,$f){
	global $not;
	$fid=$f['fieldid'];
	$i=0;
	if(isset($_SESSION['cache']['f'.$fid])){
		$defval=$_SESSION['cache']['f'.$fid];
	}else{
		$defval=$f['default'];
	}
	while($e=sql('SELECT `optionid`,`caption` FROM `options` WHERE `eventid`="'.$eid.'" AND `fieldid`="'.$fid.'" ORDER BY `optionid`')){
		echo '<span style="float:leftl"><input type="radio" style="border:none;" name="f'.$fid.'" id="f'.$fid.'-'.$e['optionid'].'" value="'.condom($e['caption']).'"'.(($defval==$e['caption'] || (empty($defval) && $i==0))?' checked="checked"':'').$not.' /><label for="f'.$fid.'-'.$e['optionid'].'">'.condom($e['caption']).'</label></span>'."\n";
		++$i;
	}
}

function mk_checkbox($eid,$f){
	global $not;
	$fid=$f['fieldid'];
	if(isset($_SESSION['cache']['f'.$fid])){
		if(!is_array($_SESSION['cache']['f'.$fid])){
			$_SESSION['cache']['f'.$fid]=array();
		}
		$defval=$_SESSION['cache']['f'.$fid];
	}else{
		$defval=linebreak($f['default']);
	}
	$options=array();
	while($e=sql('SELECT `optionid`,`caption` FROM `options` WHERE `eventid`="'.$eid.'" AND `fieldid`="'.$fid.'" ORDER BY `optionid`')){
		$options[]=$e;
	}
	if(count($options)==1){
		echo '<input type="checkbox" name="f'.$fid.'[]" id="f'.$fid.'-'.$e['optionid'].'" value="'.condom($e['caption']).'"'.(in_array($e['caption'],$defval)?' checked="checked"':'').$not.' />';
	}else{
		foreach($options as $e){
			echo '<span style="float:leftl"><input type="checkbox" name="f'.$fid.'[]" id="f'.$fid.'-'.$e['optionid'].'" value="'.condom($e['caption']).'"'.(in_array($e['caption'],$defval)?' checked="checked"':'').$not.' /><label for="f'.$fid.'-'.$e['optionid'].'">'.condom($e['caption']).'</label></span>'."\n";
		}
	}
}

function valid_none($s){
	return true;
}

function valid_email($s){
	return preg_match('/^[^@]+@([a-z0-9]+\\.)+[a-z0-9]+$/i',$s);
}

function valid_twid($user_number){
	if($user_number){
		$ID_ABC_Data="A10B11C12D13E14F15G16H17I34J18K19L20M21N22O35P23Q24R25S26T27U28V29W32X30Y31Z33";
		if(strlen($user_number)!=10){
			return false;
		}
		$InputID=strtoupper($user_number);
		$id_first_wd= substr($InputID,0,1);
		$id_latter_wd= substr($InputID,1);
		$idno=strrpos($ID_ABC_Data,$id_first_wd)+1;
		$id_abc_wd= substr($ID_ABC_Data,$idno,2);
		$InputID=$id_abc_wd.$id_latter_wd;
		$GetNo = 1;
		$SUM =substr($InputID,0,1); 
		for( $i=9;$i>0;$i--){
			$SUM += substr($InputID,$GetNo,1) * $i;
			$GetNo++;
		}
		if (substr($InputID,-1,1)!=substr((10 - substr($SUM,-1,1)),-1,1)){
			return false;
		}else{
			return true;
		}
	}
}
?>
