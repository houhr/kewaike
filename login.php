<?php
$title = "课外课-登录";
require_once "header.inc.php";
?>

<section id="content">

	<nav id="mainNav">
		<a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">登录</h1>
	</nav>
	<?php
	
	$success = false;
	
	if(isset($_POST['submitted'])) {
		
		echo "<section id=\"msg\" class=\"cb\">\n<p>提示信息：</p>";
		
		require_once "./mysql_connect.php";
		
		$errors = array();
		
		if(empty($_POST['email'])) {	//检查邮箱
			$errors[] = '<p>请填写邮箱。</p>';
		}else if(preg_match('/^[[:alnum:]][a-z0-9_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,4}$/', stripslashes(trim($_POST['email'])))) {
			$email = escapeInput($_POST['email']);
		}else {
			$errors[] = '<p>邮箱格式错误。</p>';
		}
		
		if(empty($_POST['password'])) {
			$errors[] = '<p>请填写密码。</p>';
		}else {
			$password = escapeInput($_POST['password']);
		}
		
		if(isset($_POST['autologin']) && ($_POST['autologin'] == 'yes')) {
			$autologin = true;
		} else {
			$autologin = false;
		}
		
		if(empty($errors)) {
			
			$query = "SELECT * FROM users WHERE email='$email'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				
				$rows = mysql_fetch_array($result);
				$date = $rows['registration_date'];
				$sha1 = $password . $date;
				
				$query = "SELECT * FROM users WHERE email='$email' AND password=SHA1('$sha1')";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				if(mysql_num_rows($result) == 1) {
					
					$rows = mysql_fetch_array($result);
					if($rows['active'] == 'kill') {
					
						echo '<p>该账号因违反本站点使用条款而被永久冻结。</p>';
						
					}else if($rows['active'] == NULL) {
						
						$_SESSION['userName'] = $rows['name'];
						
						if($autologin) {
							
							$cookieValue = SHA1($sha1).$rows['name'];
							setcookie('akewaike', $cookieValue, time()+864000);	//设置自动登录的cookie
							
						}
						
						if($rows['is_admin']) {
							ob_end_clean();
							$_SESSION['admin'] = 'yeah';
							header("Location: ". BASEPATH ."/admin.php?kind=v");
							exit();
						}
						
						if(isset($_POST['ref'])) {
							
							ob_end_clean();
							//判断前一网页是否是本站点的网页
							if(strpos($_POST['ref'], $_SERVER['HTTP_HOST']) !== FALSE) {
								header("Location: " . $_POST['ref']);
							}else {
								header("Location: ./");
							}
							exit();
						}
						
						ob_end_clean();
						header("Location: " . BASEPATH);
						exit();
						
					}else {
						echo '<p>该账号未激活。</p>';
					}
					
				}else {
					echo '<p>密码错误。</p>';
				}
				
			}else {
				echo '<p>该邮箱未注册。</p>';
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
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="cb">
		<p><label for="email">邮箱：</label><br /><input type="email" class="autoFocus" name="email" value="<?php if(!$success && isset($_POST['email'])){echo $_POST['email'];}?>" required /></p>
		<p><label for="password">密码：</label><br /><input type="password" name="password" required /></p>
		<p><a href="forget_password.php" tabindex="-1">忘了密码？</a></p>
		<p><input type="checkbox" name="autologin" id="autologin" value="yes" /><label for="autologin">&nbsp;记住我</label></p>
		<p><input type="submit" value="登录" class="mt10" /></p>
		<input type="hidden" name="submitted" value="true" />
		<?php if(isset($_SERVER['HTTP_REFERER'])) {echo '<input type="hidden" name="ref" value=' . $_SERVER['HTTP_REFERER'] . ' />';}?>
	</form>
	
</section>

<?php
require_once "footer.inc.php";
?>