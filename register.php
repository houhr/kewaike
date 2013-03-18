<?php
$title = "课外课-欢迎注册";
require_once "header.inc.php";
?>

<section id="content">

	<nav id="mainNav">
		<a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">欢迎注册</h1>
	</nav>
	
	<?php
	
	$success = false;
	
	if(isset($_POST['submitted'])) {	//如果提交
		
		echo "<section id=\"msg\" class=\"cb\">\n<p>提示信息：</p>";
		
		require_once('./mysql_connect.php');
		
		$errors = array();	//初始化错误记录数组
		
		if(empty($_POST['name'])) {	//检查用户名
			$errors[] = '<p>请填写昵称。</p>';
		} else {
			$name = escapeInput($_POST['name']);
		}
		
		if(empty($_POST['email'])) {	//检查邮箱
			$errors[] = '<p>请填写邮箱。</p>';
		} else if(preg_match('/^[[:alnum:]][a-z0-9_\.\-]*@[a-z0-9\.\-]+\.[a-z]{2,4}$/', stripslashes(trim($_POST['email'])))) {
			$email = escapeInput($_POST['email']);
		} else {
			$errors[] = '<p>邮箱格式错误。</p>';
		}
		
		if(empty($_POST['password']) || empty($_POST['repassword'])) {	//检查邮箱
			$errors[] = '<p>请填写密码。</p>';
		} else if($_POST['password'] != $_POST['repassword']) {
			$errors[] = '<p>两次密码不一致。</p>';
		} else {
			$password = escapeInput($_POST['password']);
		}
		
		if(!isset($_POST['agreement'])){
			$errors[] = '<p>请同意本站的使用协议。</p>';
		}
		
		if(empty($errors)) {	//全部通过
			
			$query = "SELECT name FROM users WHERE name='$name'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) == 0) {
			
				$query = "SELECT name FROM users WHERE email='$email'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				if(mysql_num_rows($result) == 0) {
				
					$active = md5(uniqid(rand(), true));
					$date = date("Y-m-d H:i:s");
					$sha1 = $password.$date;
					
					$query = "INSERT INTO users (name, email, password, active, registration_date) VALUES ('$name', '$email', SHA1('$sha1'), '$active', '$date')";
					$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
					if(mysql_affected_rows() == 1) {
						$body = "你好！感谢你的注册！请点击（或复制）下面的链接来激活你的账户：\n\n";
						$link = BASEPATH . "activate.php?n=" . urlencode($name) . "&a=$active";
						$body .= $link;
						
						require_once('./phpemail/class.phpmailer.php');
						include("./phpemail/class.smtp.php"); 
						
						$subject='课外课-激活账户';
						
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
						$mail->AddAddress($address, $_POST['name']);
						//$mail->AddAttachment("images/phpmailer.gif");      // attachment 
						//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
						if(!$mail->Send()) {
							echo "Mailer Error: " . $mail->ErrorInfo;
						} else {
							$start = strpos($email, "@") + 1;
							$subEmail = substr($email, $start);
							echo "<p>注册成功！<br />一封激活邮件已发往你的邮箱，请点击邮件中的链接来激活你的账户。<a href=\"http://mail.$subEmail\">前往邮箱 ? </a></p>";
							$success = true;
						}
						 
					} else {
						echo '非常抱歉，发生了一个系统错误！';
						echo mysql_error() . '<br /> . Query: '. $query;
					}
				} else {
					echo '<p>该邮箱已注册！</p>';
				}
			}else {
				echo '<p>该昵称已注册！</p>';
			}
			
			mysql_close();
			
		} else {	//报告错误
			foreach($errors as $msg) {
				echo "<p>$msg</p>";
			}
		}
		echo "</section>";//结束提示信息section
	}
	
	if(!$success) {
	
	?>
	
	<form action="<?php echo $_SERVER['PHP_SELF'] ?>" method="post" class="cb">
		<p><label for="name">昵称：</label><br /><input type="text" name="name" value="<?php if(isset($_POST['name'])) echo $_POST['name']; ?>" required /></p>
		<p><label for="email">邮箱：</label><br /><input type="email" name="email" maxlength="40" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" required /></p>
		<p><label for="password">密码：</label><br /><input type="password" name="password" maxlength="20" value="" required /></p>
		<p><label for="repassword">重复密码：</label><br /><input type="password" name="repassword" maxlength="20" value="" required /></p>
		<textarea readonly="readonly" cols="65" rows="6"  tabindex="-1">　　　　　　　　　　　　《使用协议》
　　根据《中华人民共和国宪法》和相关法律法规规定，在保护公民合法言论自由的同时，禁止利用互联网、通讯工具、媒体以及其他方式从事以下行为：
　　一、组织、煽动抗拒、破坏宪法和法律、法规实施的。
　　二、捏造或者歪曲事实，散布谣言，妨害社会管理秩序的。
　　三、组织、煽动非法集会、游行、示威、扰乱公共场所秩序的。
　　四、从事其他侵犯国家、社会、集体利益和公民合法权益的。管理部门将依法严加监管上述行为并予以处理；对构成犯罪的，司法机关将追究刑事责任。
		</textarea>
		<p><label><input type="checkbox" name="agreement" id="agreement" />&nbsp;我已阅读并同意以上《使用协议》。</label></p>
		<p><input type="submit" value="完成注册" class="mt10" /></p>
		<input type="hidden" name="submitted" value="true" />
	</form>
	
	<?php
	}
	?>
	
</section>

<?php
require_once "footer.inc.php";
?>