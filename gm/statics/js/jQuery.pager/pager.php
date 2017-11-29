<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtoupper($_SERVER['HTTP_X_REQUESTED_WITH'])=='XMLHTTPREQUEST'){
	echo '<PRE>';
	echo "\nGET数据如下\n" ;
	print_r($_GET);
	echo "\nPOST数据如下\n" ;
	print_r($_POST);
	echo "\n当前时间\n" ;
	echo date('Y-m-d H:i:s');
	echo '</PRE>';
	exit;
}else{
	echo file_get_contents('pager.html');	
}