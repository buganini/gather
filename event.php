<?php
include('config.php');
if(admin()){
	include('event.admin.php');
}else{
	include('event.user.php');
}
?>