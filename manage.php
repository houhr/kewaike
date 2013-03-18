<?php
$title = "课外课-管理";
require_once "header.inc.php";

if(!isset($_SESSION['userName'])) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}

?>

<section id="content">

	<nav id="mainNav">
	<a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">管理</h1>
	</nav>
	
	<ul id="adminItems" class="clearfix">
		<li <?php if(isset($_GET['kind']) && $_GET['kind'] == 'v')echo 'class="currentItem"';?>><a href="?kind=v">我上传的视频</a></li>
		<li <?php if(isset($_GET['kind']) && $_GET['kind'] == 'c')echo 'class="currentItem"';?>><a href="?kind=c">我发布的留言</a></li>
	</ul>
	
	<?php
	require_once "./mysql_connect.php";
	
	
	if(isset($_GET['kind']) && $_GET['kind'] == 'v') {
		//删除视频
		if(isset($_GET['d']) && $_GET['d'] == true && isset($_GET['vid'])){
			$query = "SELECT * FROM videos WHERE contributor='".$_SESSION['userName']."' and id='".$_GET['vid']."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				$query = "DELETE FROM videos WHERE contributor='".$_SESSION['userName']."' and id='".$_GET['vid']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				header("Location: ". BASEPATH ."/manage.php?kind=v");
			}
		}
		
		$query = "SELECT * FROM videos WHERE contributor='".$_SESSION['userName']."'";
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
			
			$query = "SELECT * FROM videos WHERE contributor='".$_SESSION['userName']."' ORDER BY upload_date DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '
			<table>
			<caption>我上传的视频</caption>
			<tr><th>视频ID</th><th>观看</th><th>审核状态</th><th>删除</th></tr>
			';
			
			while($rows = mysql_fetch_array($result)) {
			
				if($rows['passed']) {
					$passed = '已通过';
				}else {
					$passed = '待审核';
				}
				
				echo '
				<tr>
					<td>'.$rows['id'].'</td>
					<td><video src="videos/'.$rows['id'].'.mp4" controls width="300"></video></td>
					<td>'.$passed.'</td>
					<td><a href="'. BASEPATH .'/manage.php?kind=v&vid='.$rows['id'].'&d=true">删除</a></td>
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
			echo '你还没有上传过视频，<a href="upload.php">上传一个</a>？';
		}
	
	}else if(isset($_GET['kind']) && $_GET['kind'] == 'c') {
		//删除留言
		if(isset($_GET['d']) && $_GET['d'] == true && isset($_GET['cid'])){
			$query = "SELECT * FROM comments WHERE whose_comment='".$_SESSION['userName']."' and id='".$_GET['cid']."'";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			if(mysql_num_rows($result) != 0) {
				$query = "DELETE FROM comments WHERE whose_comment='".$_SESSION['userName']."' and id='".$_GET['cid']."'";
				$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
				header("Location: ". BASEPATH ."/manage.php?kind=c");
			}
		}
		
		$query = "SELECT * FROM comments WHERE whose_comment='".$_SESSION['userName']."'";
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
			
			$query = "SELECT * FROM comments WHERE whose_comment='".$_SESSION['userName']."' ORDER BY date DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '
			<table>
			<caption>我发布的留言</caption>
			<tr><th>留言内容</th><th>时间</th><th>所属视频</th><th>审核状态</th><th>删除</th></tr>
			';
			
			while($rows = mysql_fetch_array($result)) {
				if($rows['passed']) {
					$passed = '已通过';
				}else {
					$passed = '待审核';
				}
			
				echo '
				<tr>
					<td>'.$rows['content'].'</td>
					<td>'.$rows['date'].'</td>
					<td><a href="view.php?vid='. $rows['which_video'] .'">'.$rows['which_video'].'</a></td>
					<td>'.$passed.'</td>
					<td><a href="'. BASEPATH .'/manage.php?kind=c&cid='.$rows['id'].'&d=true">删除</a></td>
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
			echo '你还没有发布过留言。';
		}
		
	}
	
	?>
	
</section>

<?php
require_once "footer.inc.php";
?>