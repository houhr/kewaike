<?php
$title = "课外课-重设密码";
require_once "header.inc.php";
?>

<section id="content">

	<nav id="mainNav">
		<a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">重设密码</h1>
	</nav>
	
	<?php
	
	if(isset($_POST['submitted'])) {
	
		echo "<section id=\"msg\">\n<p>提示信息：</p>";
		
		require_once "./mysql_connect.php";
		
		if(empty($_POST['email'])) {	//检查邮箱
			echo '<p>请填写邮箱。</p>';
		}else if(preg_match('/^[[:alnum:]][a-z0-9_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,4}$/', stripslashes(trim($_POST['email'])))) {
			$email = escapeInput($_POST['email']);
			
			$query = "SELECT registration_date FROM users WHERE email='$email'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) == 1) {
				
				$rows = mysql_fetch_array($result);
				$p = substr(md5(uniqid(rand(), 1)), 3, 6);
				$date = $rows['registration_date'];
				$sha1 = $p . $date;
				
				$query = "UPDATE users SET password=SHA1('$sha1') WHERE email='$email'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				if(mysql_affected_rows() == 1) {
					$body = "你的新密码为'$p'，请用此密码登录课外课，并重新设置密码。";
					
					
					require_once('./phpemail/class.phpmailer.php');
					include("./phpemail/class.smtp.php"); 
					
					$subject='重设密码-课外课';
					
					$mail             = new PHPMailer(); //new一个PHPMailer对象出来
					$body             = preg_replace("/\[\\\]/",'',$body); //对邮件内容进行必要的过滤
					$mail->CharSet ="UTF-8";//设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
					$mail->IsSMTP(); // 设定使用SMTP服务
					$mail->SMTPDebug  = 1;                     // 启用SMTP调试功能
														   // 1 = errors and messages
														   // 2 = messages only
					$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
					//$mail->SMTPSecure = "ssl";                 // 安全协议
					$mail->Host       = "mail.imu.edu.cn";      // SMTP 服务器
					$mail->Port       = 25;                   // SMTP服务器的端口号
					$mail->Username   = "";  // SMTP服务器用户名
					$mail->Password   = "";            // SMTP服务器密码
					$mail->SetFrom('kewaike@imu.edu.cn', 'kewaike');
					$mail->AddReplyTo("kewaike@imu.edu.cn","kewaike");
					$mail->Subject    = $subject;
					$mail->AltBody    = "To view the message, please use an HTML compatible email viewer! - From abc"; // optional, comment out and test
					$mail->MsgHTML($body);
					$address = $email;
					$mail->AddAddress($address, '');
					//$mail->AddAttachment("images/phpmailer.gif");      // attachment 
					//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
					if(!$mail->Send()) {
						echo "Mailer Error: " . $mail->ErrorInfo;
					} else {
						$start = strpos($email, "@") + 1;
						$subEmail = substr($email, $start);
						echo "<p>重设密码成功！<br />新密码已发往你的邮箱。<a href=\"http://mail.$subEmail\">前往邮箱 » </a></p>";
					}
					
				} else {
					echo '<p>你的密码暂时不能修改，请稍后再试。</p>';
				}
			} else {
				echo '<p>该邮箱没有注册过。</p>';
			}
			
		}else {
			echo '<p>邮箱格式错误。</p>';
		}
		
		echo "</section>";//结束提示信息section
		
	}
	
	
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
		<p><label for="email">邮箱：</label><br /><input type="email" class="autoFocus" name="email" required /></p>
		<p><input type="submit" value="重设密码" class="mt10" /></p>
		<input type="hidden" name="submitted" value="true" />
	</form>
	
</section>

<?php
require_once "footer.inc.php";
?>