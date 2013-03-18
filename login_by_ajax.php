<?php
require_once "./mysql_connect.php";

if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['autologin'])) {
	
	$json = '{';
	
	$email = escapeInput($_POST['email']);
	$password = escapeInput($_POST['password']);
	$autologin = $_POST['autologin'];
	
	$query = "SELECT * FROM users WHERE email='$email'";
	$result = mysql_query($query);
	if(mysql_num_rows($result) != 0) {
		
		$rows = mysql_fetch_array($result);
		$date = $rows['registration_date'];
		$sha1 = $password . $date;
		
		$query = "SELECT * FROM users WHERE email='$email' AND password=SHA1('$sha1')";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) == 1) {
			
			$rows = mysql_fetch_array($result);
			if($rows['active'] == 'kill') {
				$json .= '"error":"该账号已永久冻结"';
			}else if($rows['active'] == NULL) {
				
				//验证成功
				session_name('kewaike');
				session_start();
				$sid = session_id();
				$_SESSION['userName'] = $rows['name'];
				
				if($autologin) {
					
					$cookieValue = SHA1($sha1).$rows['name'];
					$json .= '"akewaike":"' . $cookieValue . '"';
					
				}
				
			}else {
				$json .= '"error":"该账号未激活"';
			}
			
		}else {
			$json .= '"error":"密码错误"';
		}
		
	}else {
		$json .= '"error":"该邮箱未注册"';
	}
	
	$json .= '}';
	echo $json;
	
	mysql_close();
}

?>