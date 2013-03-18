<?php
//启用输出缓冲
ob_start();
//定义站点根目录
define("BASEPATH", "http://".$_SERVER['HTTP_HOST'] . "/kewaike/");
//调用date()之前必须设置时区
date_default_timezone_set("PRC");
//页面title

session_name('kewaike');
session_start();

$logined = false;

if(isset($_SESSION['userName'])) {
	$logined = true;
}else if(isset($_COOKIE['akewaike'])) {
	
	$cookiePassword = substr($_COOKIE['akewaike'], 0, 40);
	$cookieName = substr($_COOKIE['akewaike'], 40);
	
	require_once "./mysql_connect.php";
	
	$query = "SELECT * FROM users WHERE name='$cookieName' AND password='$cookiePassword'";
	$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
	if(mysql_num_rows($result) == 1) {
		$rows = mysql_fetch_array($result);
		$_SESSION['userName'] = $rows['name'];
		$logined =true;
	}
	
}

if(isset($_GET['logout'])) {
	setcookie('akewaike', '', time()-300, '/', '', 0);	//销毁自动登录的cookie
	$_SESSION = array();	//销毁变量
	session_destroy();		//销毁session自身
	setcookie(session_name(), '', time()-300, '/', '', 0);	//销毁Cookie
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}

if(!isset($title)) {
	$title = '课外课-分享国内外大学精品课程';
}

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo $title;?></title>
	<!--[if IE]>
	<script src="./js/excanvas.js">/**  http://code.google.com/p/explorercanvas/  **/</script>
	<![endif]-->
	<!--[if lt IE 9]>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<!-- <script src="./js/modernizr-1.6.min.js"></script> -->
	<link rel="stylesheet" href="./css/nivo-slider.css" media="screen" />
	<link rel="stylesheet" href="./css/main.css" media="screen" />
</head>
<body>
	<section id="mask">
	</section>
	<section id="window">
		<form action="login_by_ajax.php" method="post">
			<p><label for="email">邮箱：</label><input type="email" class="autoFocus" id="email" value="" required /><span class="warning"></span></p>
			<p><label for="password">密码：</label><input type="password" id="password" required /><span class="warning"></span></p>
			<p><a href="forget_password.php" tabindex="-1">忘了密码？</a></p>
			<p><input type="checkbox" name="autologin" id="autologin" value="yes" /><label for="autologin">&nbsp;记住我</label></p>
			<p><input type="submit" value="登录" />&nbsp;<a class="closeWindow" href="#">取消</a>&nbsp;<span class="warning"></span></p>
		</form>
		<a class="closeWindow" href="#" title="关闭窗口">X</a>
	</section>
	
	<section id="top">
		<header>
			
			<h1 class="floatLeft"><a href="<?php echo BASEPATH;?>">内蒙古大学课外课学习园地</a></h1>
			
			<form id="search" class="floatLeft" action="list.php" method="get">
				<input type="search" id="q" name="q" required />
				<input type="submit" value="搜索课程" />
			</form>
			
			<span class="floatRight">
			<?php
			if(!$logined) {
				echo '<a href="login.php" class="loginButton">登录</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="register.php">注册</a>';
			}else {
				echo '<a href="upload.php">上传课程</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="manage.php?kind=v">管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="change_password.php">修改密码</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.BASEPATH.'?logout">退出</a>';
				//echo '<a href="#">上传</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="change_password.php">修改密码</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="'.BASEPATH.'?logout">退出</a>';
			}
			?>
			</span>
			
		</header>
	</section>