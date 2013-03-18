<?php

DEFINE ('DB_USER', '');
DEFINE ('DB_PASSWORD', '');
DEFINE ('DB_HOST', '');
DEFINE ('DB_NAME', '');

if($dbc = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD)) {

	mysql_query("set names 'utf8' ");
	mysql_query("set character_set_client=utf8");
	mysql_query("set character_set_results=utf8");
	
	if(!mysql_select_db(DB_NAME)) {
		trigger_error("无法选择数据库!\n<br />MySQL Error:" . mysql_error());
		include('includes/footer.html');
		exit();
	}
	
} else {
	trigger_error("无法连接到MySQL！\n<br />MySQL Error:" . mysql_error());
	include('includes/footer.html');
	exit();
}

function escapeInput($input) {
	//去除首尾空格
	$input = trim($input);
	//将换行字符转成<br />
	$input = nl2br($input);
	//将用户输入中的&、"、'、<、>变为字符实体
	//$input = htmlentities($input, ENT_QUOTES, "UTF-8");
	//删除除<br>外的所有HTML标记
	$input = strip_tags($input, "<br>");
	//转义 SQL 语句中使用的字符串中的特殊字符
	$input = mysql_real_escape_string($input);
	return $input;
}

?>