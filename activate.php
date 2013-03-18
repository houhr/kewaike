<?php
$title = "课外课-激活账号";
require_once "header.inc.php";
?>

<section id="content">

	<h1>激活账号</h1>
	<?php
	require_once "./mysql_connect.php";
	
	if(isset($_GET['n'])) {
		$n = escapeInput($_GET['n']);
	} else {
		$n = "";
	}

	if(isset($_GET['a'])) {
		$a = escapeInput($_GET['a']);
		$a = substr($a, 0, 32);
	} else {
		$a = "";
	}

	if(!empty($n) && (strlen($a) == 32)) {
		
		$query ="SELECT * FROM users WHERE (name='$n' AND active IS NOT NULL)";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) == 1) {
			
			$query = "UPDATE users SET active=NULL WHERE (name='$n' AND active='" . $a . "') LIMIT 1";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_affected_rows()!=0) {
				echo "<section id=\"msg\">\n<p>提示信息：</p><p>恭喜!</p><p>账户激活成功！马上登录吧！</p></section>";
			} else {
				echo "<section id=\"msg\">\n<p>提示信息：</p><p>无法激活你的账户 :(</p><p>请复制邮件中的链接，并粘贴到地址栏中后按回车键再试一次。</p></section>";
			}
		}
		mysql_close();
	} else {
		ob_end_clean();
		header("Location: " . BASEPATH);
		exit();
	}
	?>
</section>

<?php
require_once "footer.inc.php";
?>