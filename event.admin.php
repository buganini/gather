<?php
$preload=array();
?><html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="/jscss/bg.css">
<script type="text/javascript" src="/jscss/enable_tab.js"></script>
<script type="text/javascript" src="/jscss/tinymce/tiny_mce.js"></script>
<style type="text/css">
th{text-align:center; background-color:#ccc;}
.noscroll{overflow-y:hidden;}
</style>
<script language="javascript" type="text/javascript">
	tinyMCE.init({
		// General options
		mode : "exact",
		elements : "desc",
		theme : "advanced",
		plugins : "safari,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,imagemanager,filemanager",
	
		// Theme options
		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});

	function atrows(o){
		o.rows=o.value.split('\n').length;
	}

	function getelm(id){
		return document.getElementById(id);
	}

	function upRow(obj){
		node1=obj.parentNode.parentNode;
		if(node1.rowIndex>1)
		node1.parentNode.insertBefore(node1, node1.previousSibling);
	}

	function downRow(obj){
		node1=obj.parentNode.parentNode;
		if(node1.parentNode.rows.length-node1.rowIndex>2)
		node1.parentNode.insertBefore(node1.nextSibling, node1);
	}

	function select(a,b){
		if(a==b){
			return ' selected="selected"';
		}
		return '';
	}

	var dummy;
	function addRow(fieldid,caption,type,option,defval,valid,pattern,public,admin,key){
		var fieldid=(fieldid==null)?'':fieldid;
		var caption=(caption==null)?'':caption;
		var type=(type==null)?'':type;
		var option=(option==null)?'':option;
		var defval=(defval==null)?'':defval;
		var valid=(valid==null)?'':valid;
		var pattern=(pattern==null)?'':pattern;
		var public=(public==null)?'checked="checked"':(public==1?'checked="checked"':'');
		var admin=(admin==null)?'':(admin==1?'checked="checked"':'');
		var key=(key==null)?'0':key;
		var tbl = getelm('signup');
		var newRow = tbl.insertRow(tbl.rows.length-1);
		var str;

		dummy=(dummy==null)?1:(dummy+1);

		var newCell0 = newRow.insertCell(0);
		newCell0.innerHTML = '<input type="hidden" name="fieldid[]" value="'+fieldid+'" /><input type="hidden" name="dummy[]" value="'+dummy+'" /><input type="input" name="caption[]" size="10" value="'+caption+'" />';

		var newCell1 = newRow.insertCell(1);
		str = '<select name="type[]">';
		str += '<option value="line"'+select('line',type)+'>單行文字</option>';
		str += '<option value="textarea"'+select('textarea',type)+'>多行文字</option>';
		str += '<option value="radio"'+select('radio',type)+'>單選</option>';
		str += '<option value="checkbox"'+select('checkbox',type)+'>查核方塊</option>';
		str += '</select>';
		newCell1.innerHTML=str;

		var newCell2 = newRow.insertCell(2);
		newCell2.innerHTML = '<textarea rows="1" onkeyup="atrows(this)" class="noscroll" name="option[]">'+option+'</textarea>';

		var newCell3 = newRow.insertCell(3);
		newCell3.innerHTML = '<textarea rows="1" onkeyup="alrows(this)" class="noscroll" name="default[]">'+defval+'</textarea>';
		
		var newCell4 = newRow.insertCell(4);
		str = '<select name="valid[]">';
		str += '<option value="none"'+select('none',valid)+'>無</option>';
		str += '<option value="twid"'+select('twid',valid)+'>身分證字號</option>';
		str += '<option value="email"'+select('email',valid)+'>E-Mail</option>';
		str += '</select>';
		newCell4.innerHTML=str;

		var newCell5 = newRow.insertCell(5);
		newCell5.innerHTML = '<textarea name="pattern[]" cols="10" rows="1" class="noscroll" onkeyup="alrows(this)">'+pattern+'</textarea>';

		var newCell6 = newRow.insertCell(6);
		newCell6.innerHTML = '<input type="checkbox" name="public[]" value="'+dummy+'" '+public+'/>';

		var newCell7 = newRow.insertCell(7);
		newCell7.innerHTML = '<input type="checkbox" name="admin[]" value="'+dummy+'" '+admin+'/>';

		var newCell8 = newRow.insertCell(8);
		newCell8.innerHTML = '<input type="input" name="key[]" size="3" value="'+key+'" />';
		
		var newCell9 = newRow.insertCell(9);
		newCell9.innerHTML = '<input type="button" value="↑" onclick="upRow(this)"><input type="button" value="↓" onclick="downRow(this)"><input type="button" value="X" onclick="delRow(this)" />';
	}

	function delRow(obj){
		getelm('signup').deleteRow(obj.parentNode.parentNode.rowIndex);
	}

	function preload(){<?php
		if(!empty($_GET['eventid'])){
			$tpl='';
		}elseif(!empty($_GET['tpl_eventid'])){
			$tpl='tpl_';
		}
		$preload[$tpl.'eventid']=$_GET[$tpl.'eventid'];
		if($e=sql('SELECT * FROM `'.$tpl.'events` WHERE `eventid`="'.$preload[$tpl.'eventid'].'"')){
			$preload['title']=condom($e['title']);
			$preload['expose']=($e['expose']==0?'':' checked="checked"');
			$preload['desc']=condom($e['desc']);
			if(empty($tpl)){
				$preload['beg_y']=substr($e['beg_date'],0,4);
				$preload['beg_m']=substr($e['beg_date'],4,2);
				$preload['beg_d']=substr($e['beg_date'],6,2);
				$preload['end_y']=substr($e['end_date'],0,4);
				$preload['end_m']=substr($e['end_date'],4,2);
				$preload['end_d']=substr($e['end_date'],6,2);
			}
		}
		if(!empty($preload[$tpl.'eventid'])){
			while($e=sql('SELECT * FROM `'.$tpl.'fields` WHERE `eventid`="'.$preload[$tpl.'eventid'].'" ORDER BY `order` ASC')){
				$opt=array();
				while($e2=sql('SELECT `caption` FROM `'.$tpl.'options` WHERE `eventid`="'.$preload[$tpl.'eventid'].'" AND `fieldid`="'.$e['fieldid'].'" ORDER BY `optionid` ASC')){
					$opt[]=$e2['caption'];
				}
				echo 'addRow(\''.strize($e['fieldid']).'\',\''.strize($e['caption']).'\',\''.strize($e['type']).'\',\''.strize(implode("\n",$opt)).'\',\''.strize($e['default']).'\',\''.strize($e['valid']).'\',\''.strize($e['pattern']).'\',\''.strize($e['public']).'\',\''.strize($e['admin']).'\',\''.strize($e['key']).'\');'."\n";
			}
		}
	?>}
</script>
</head>
<body onload="preload()">
<form method="POST" action="engine.php" id="form">
<input type="hidden" name="action" id="action" value="save" />
<input type="hidden" name="eventid" value="<?echo $preload['eventid'];?>" />
<input type="hidden" name="tpl_eventid" value="<?echo $preload['tpl_eventid'];?>" />
<table style="position:absolute; right:0; top:0;">
<tr><td><select id="tpl"><?
$flg=true;
while($e=sql('SELECT `eventid`,`title` FROM `tpl_events`')){
	echo '<option value="'.$e['eventid'].'">'.$e['title'].'</option>';
	$flg=false;
}
if($flg){
	echo '<option value="">-- 沒有樣板 --</option>';
}
?></select></td><td><input type="button" onClick="document.location.href='event.php?tpl_eventid='+getelm('tpl').value" value="載入樣版" /></td></tr>
<tr><td><input type="button" value="儲存樣版" onClick="getelm('action').value='tpl_save'; getelm('form').submit();" /></td><td><?if(!empty($preload['tpl_eventid'])){?><input type="button" value="刪除樣版" onClick="if(confirm('確定刪除樣板 <?echo $preload['title'];?>？')){getelm('action').value='tpl_delete'; getelm('form').submit();}" /><?}?></td></tr>
</table>
<table>
<tr><td>活動名稱</td><td><input type="text" name="title" value="<?echo $preload['title'];?>" /></td></tr>
<tr><td>公開活動</td><td><input type="checkbox" name="expose"<?echo pretry('expose',' checked="checked"');?> /></td></tr>
<tr><td>報名開始</td><td><input type="text" name="beg_y" value="<?echo pretry('beg_y',date('Y'));?>" />年<input type="text" name="beg_m" value="<?echo pretry('beg_m',date('n'));?>" />月<input type="text" name="beg_d" value="<?echo pretry('beg_d',date('j'));?>" />日</td></tr>
<tr><td>報名截止</td><td><input type="text" name="end_y" value="<?echo pretry('end_y',date('Y'));?>" />年<input type="text" name="end_m" value="<?echo pretry('end_m',date('n'));?>" />月<input type="text" name="end_d" value="<?echo pretry('end_d',date('j'));?>" />日</td></tr>
</table>
<textarea onkeydown="return catchTab(this,event);" name="desc" id="text" rows="15" cols="80" style="width:100%; text-align:left;"><?echo $preload['desc'];?></textarea>
<table id="signup" width="100%">
<tr><th>項目</th><th>類型</th><th>選項</th><th>預設值</th><th>驗證</th><th>驗證設定</th><th>公開</th><th>保留</th><th>鍵別</th><th>#</th></tr>
<tr><td colspan="7" align="center"><input type="button" onclick="addRow()" value="新增欄位" /></td></tr>
</table>
<div style="text-align:center;"><input type="submit" onclick="getelm('form').submit(); this.disabled=true;" value="儲存報名表" /></div>
<?if(!empty($preload['eventid'])){?>
<div style="text-align:right;"><input type="button" value="刪除報名表" onClick="if(confirm('確定刪除？')){getelm('action').value='delete'; getelm('form').submit();}" /></div>
<?}?>
</form>
</body>
</html>
