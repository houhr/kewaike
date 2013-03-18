<?php
$title = "课外课-后台管理";
require_once "header.inc.php";

if(!isset($_SESSION['userName']) || ($_SESSION['admin'] != 'yeah')) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}

?>

<section id="content">

	<ul id="adminItems" class="clearfix">
		<li <?php if(isset($_GET['kind']) && $_GET['kind'] == 'v')echo 'class="currentItem"';?>><a href="?kind=v">审核视频</a></li>
		<li <?php if(isset($_GET['kind']) && $_GET['kind'] == 'c')echo 'class="currentItem"';?>><a href="?kind=c">审核留言</a></li>
		<li <?php if(isset($_GET['kind']) && $_GET['kind'] == 'u')echo 'class="currentItem"';?>><a href="?kind=u">管理用户</a></li>
	</ul>
	
	<?php
	require_once "./mysql_connect.php";
	
	if(isset($_GET['kind']) && $_GET['kind'] == 'v') {
		//处理通过的视频
		if(isset($_GET['p']) && $_GET['p'] == true && isset($_GET['vid'])){
			$query = "SELECT * FROM videos WHERE passed=0 and id='".$_GET['vid']."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				$query = "UPDATE videos SET passed=1 WHERE id='".$_GET['vid']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				header("Location: ". BASEPATH ."/admin.php?kind=v");
			}
		}
		
		$query = "SELECT * FROM videos WHERE passed=0";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) != 0) {
		
			$pageSize = 5;		//每页项目数
			$total = mysql_num_rows($result);		//总项目数
			$pageCount = ceil($total / $pageSize);	//总页数
			$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
			if ($page > $pageCount) {
				$page = $pageCount;
			}
			if ($page <= 0) {
				$page = 1;
			}
			$offset = ($page - 1) * $pageSize;
			($page - 1) < 1 ? $pre = 1 : $pre = ($page - 1);		//上一页
			($page + 1) > $pageCount ? $next = $pageCount : $next = ($page + 1);		//下一页
			
			$query = "SELECT * FROM videos WHERE passed=0 ORDER BY upload_date DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '
			<table>
			<caption>待审核视频</caption>
			<tr><th>上传者</th><th>视频ID</th><th>观看</th><th>通过</th></tr>
			';
			
			while($rows = mysql_fetch_array($result)) {
				echo '
				<tr>
					<td>'.$rows['contributor'].'</td>
					<td>'.$rows['id'].'</td>
					<td><video src="videos/'.$rows['id'].'.mp4" controls width="300"></video></td>
					<td><a href="'. BASEPATH .'/admin.php?kind=v&vid='.$rows['id'].'&p=true">通过</a></td>
				</tr>';
			}
			
			echo '</table>';
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?kind=v&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?kind=v&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?kind=v&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			echo '暂无待审核视频。';
		}
	
	}else if(isset($_GET['kind']) && $_GET['kind'] == 'c') {
		//处理通过的留言
		if(isset($_GET['p']) && $_GET['p'] == true && isset($_GET['cid'])){
			$query = "SELECT * FROM comments WHERE passed=0 and id='".$_GET['cid']."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				$query = "UPDATE comments SET passed=1 WHERE id='".$_GET['cid']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				header("Location: ". BASEPATH ."/admin.php?kind=c");
			}
		}
		
		$query = "SELECT * FROM comments WHERE passed=0";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) != 0) {
		
			$pageSize = 15;		//每页项目数
			$total = mysql_num_rows($result);		//总项目数
			$pageCount = ceil($total / $pageSize);	//总页数
			$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
			if ($page > $pageCount) {
				$page = $pageCount;
			}
			if ($page <= 0) {
				$page = 1;
			}
			$offset = ($page - 1) * $pageSize;
			($page - 1) < 1 ? $pre = 1 : $pre = ($page - 1);		//上一页
			($page + 1) > $pageCount ? $next = $pageCount : $next = ($page + 1);		//下一页
			
			$query = "SELECT * FROM comments WHERE passed=0 ORDER BY date DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '
			<table>
			<caption>待审核留言</caption>
			<tr><th>留言者</th><th>留言内容</th><th>时间</th><th>所属视频</th><th>通过</th></tr>
			';
			
			while($rows = mysql_fetch_array($result)) {
				echo '
				<tr>
					<td>'.$rows['whose_comment'].'</td>
					<td>'.$rows['content'].'</td>
					<td>'.$rows['date'].'</td>
					<td>'.$rows['which_video'].'</td>
					<td><a href="'. BASEPATH .'/admin.php?kind=c&cid='.$rows['id'].'&p=true">通过</a></td>
				</tr>';
			}
			
			echo '</table>';
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?kind=c&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?kind=c&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?kind=c&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			echo '暂无待审核留言。';
		}
		
	}else if(isset($_GET['kind']) && $_GET['kind'] == 'u') {
		
		//处理通过的留言
		if(isset($_GET['p']) && $_GET['p'] == true && isset($_GET['uid'])){
			$query = "SELECT * FROM users WHERE id='".$_GET['uid']."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				$query = "UPDATE users SET active='kill' WHERE id='".$_GET['uid']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				header("Location: ". BASEPATH ."/admin.php?kind=u");
			}
		}
		
		echo '
		<form action="admin.php" method="GET">
			<fieldset>
				<legend>搜索用户</legend>
				<input type="hidden" name="kind" value="u"/>
				<input type="text" id="search" name="search"/>
				<input type="submit" value="搜索用户"/>
			</fieldset>
		</form>';
		
		if(isset($_GET['search'])) {
			$clearSearch = escapeInput($_GET['search']);
			$query = "SELECT * FROM users WHERE name='". $clearSearch ."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
			
				echo '
				<table>
				<caption>查询结果</caption>
				<tr><th>用户名</th><th>邮箱</th><th>注册时间</th><th>是否为管理员</th><th>冻结账户</th></tr>
				';
				
				while($rows = mysql_fetch_array($result)) {
					if($rows['is_admin']) {
						$isAdmin = '是';
					}else {
						$isAdmin = '否';
					}
					
					echo '
					<tr>
						<td>'.$rows['name'].'</td>
						<td>'.$rows['email'].'</td>
						<td>'.$rows['registration_date'].'</td>
						<td>'.$isAdmin .'</td>';
					if($rows['active'] != 'kill') {
						echo '<td><a href="'. BASEPATH .'/admin.php?kind=u&uid='.$rows['id'].'&p=true">永久冻结</a></td>';
					}else {
						echo '<td>已永久冻结</td>';
					}
					
					echo '</tr>';
				}
				
				echo '</table>';
				
			}else {
				echo '查无此人。';
			}
		}
		
		echo '<hr/>';
		
		$query = "SELECT * FROM users";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) != 0) {
		
			$pageSize = 20;		//每页项目数
			$total = mysql_num_rows($result);		//总项目数
			$pageCount = ceil($total / $pageSize);	//总页数
			$page = isset($_GET["page"]) ? intval($_GET["page"]) : 1;
			if ($page > $pageCount) {
				$page = $pageCount;
			}
			if ($page <= 0) {
				$page = 1;
			}
			$offset = ($page - 1) * $pageSize;
			($page - 1) < 1 ? $pre = 1 : $pre = ($page - 1);		//上一页
			($page + 1) > $pageCount ? $next = $pageCount : $next = ($page + 1);		//下一页
			
			$query = "SELECT * FROM users LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '
			<table>
			<caption>用户列表一览 （共'. $total .'名）</caption>
			<tr><th>用户名</th><th>邮箱</th><th>注册时间</th><th>是否为管理员</th><th>冻结账户</th></tr>
			';
			
			while($rows = mysql_fetch_array($result)) {
				if($rows['is_admin']) {
					$isAdmin = '是';
				}else {
					$isAdmin = '否';
				}
				
				echo '
				<tr>
					<td>'.$rows['name'].'</td>
					<td>'.$rows['email'].'</td>
					<td>'.$rows['registration_date'].'</td>
					<td>'.$isAdmin .'</td>';
				if($rows['active'] != 'kill') {
					echo '<td><a href="'. BASEPATH .'/admin.php?kind=u&uid='.$rows['id'].'&p=true">永久冻结</a></td>';
				}else {
					echo '<td>已永久冻结</td>';
				}
				
				echo '</tr>';
			}
			
			echo '</table>';
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?kind=u&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?kind=u&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?kind=u&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			echo '暂无用户。';
		}
		
	}else {
		ob_end_clean();
		header("Location: " . BASEPATH);
		exit();
	}
	?>
	
</section>



<?php
require_once "footer.inc.php";
?>