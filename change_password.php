<?php
$title = "课外课-修改密码";
require_once "header.inc.php";

if(!isset($_SESSION['userName'])) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}

?>

<section id="content">

	<nav id="mainNav">
	<a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">修改密码</h1>
	</nav>
	<?php
	
	if(isset($_POST['submitted'])) {
		
		echo "<section id=\"msg\" class=\"cb\">\n<p>提示信息：</p>";
		
		require_once "./mysql_connect.php";
		
		$errors = array();
		
		if(empty($_POST['oldPassword'])) {
			$errors[] = '<p>请填写旧密码。</p>';
		}else {
			$oldPassword = escapeInput($_POST['oldPassword']);
		}
		
		if(empty($_POST['newPassword'])) {
			$errors[] = '<p>请填写新密码。</p>';
		}else {
			$newPassword = escapeInput($_POST['newPassword']);
		}
		
		if(empty($errors)) {
			
			$query = "SELECT * FROM users WHERE name='".$_SESSION['userName']."' LIMIT 1";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			$rows = mysql_fetch_array($result);
			
			$date = $rows['registration_date'];
			$sha1 = $oldPassword . $date;
			$sha1Value = sha1($sha1);
			
			if($sha1Value == $rows['password']) {
				
				$sha1 = $newPassword . $date;
				$query = "UPDATE users SET password=SHA1('$sha1') WHERE name='".$_SESSION['userName']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				if(mysql_affected_rows() == 1) {
					echo '<p>密码已更改。</p>';
				} else {
					echo '<p>新密码与旧密码相同。</p>';
				}
			} else {
				echo '<p>旧密码不正确。</p>';
			}
			
			mysql_close();
			
		}else {
			foreach($errors as $msg) {
				echo "$msg\n";
			}
		}
		
		echo "</section>";//结束提示信息section
	}
	
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="cb">
		<p>旧密码：<input type="password" class="autoFocus" name="oldPassword" required /></p>
		<p>新密码：<input type="password" name="newPassword" required /></p>
		<input class="mt10" type="submit" value="更改密码" />
		<input type="hidden" name="submitted" value="true" />
	</form>
	
</section>

<?php
require_once "footer.inc.php";
?>