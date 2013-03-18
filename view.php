<?php
require_once "header.inc.php";

if(!isset($_GET['vid']) && !isset($_POST['vid'])) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}else {
	//检测vid的有效性
	require_once "./mysql_connect.php";
	if(isset($_POST['vid']))$_GET['vid'] = $_POST['vid'];
	$cleanID = escapeInput($_GET['vid']);
	
	$query = "SELECT * FROM videos WHERE id='$cleanID'";
	$result = mysql_query($query);
	//如果vid无效则转到首页
	if(mysql_num_rows($result) == 0) {
		mysql_close();
		ob_end_clean();
		header("Location: " . BASEPATH);
		exit();
	}
	
	
	$rows = mysql_fetch_array($result);
	//如果视频未通过审核跳转到首页
	if($rows['passed'] == 0) {
		mysql_close();
		ob_end_clean();
		header("Location: " . BASEPATH);
		exit();
	}
	
	$playTimes = $rows['play_times'];
	$playTimes++;
	$query = "UPDATE videos SET play_times='$playTimes' WHERE id='$cleanID'";
	$result = mysql_query($query);
	
	$query = "SELECT * FROM videos WHERE id='$cleanID'";
	$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
	$rows = mysql_fetch_array($result);
	
	//如果有留言提交，处理留言
	if(isset($_POST['submitted']) && isset($_SESSION['userName'])) {
		
		if(!empty($_POST['comm'])) {
			
			$cleanComm = escapeInput($_POST['comm']);
			if(!empty($cleanComm)) {
				$commQuery = "INSERT INTO comments (id, which_video, whose_comment, content, date) VALUES (NULL, '".$_GET['vid']."', '".$_SESSION['userName']."','$cleanComm', NOW())";
				$commResult = mysql_query($commQuery) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			}
		}
		
	}
}


?>

<section id="content" class="clearfix">
	
	<nav id="mainNav">
		<a href="<?php echo BASEPATH;?>">首页</a> &gt; 
		<a href="list.php?uni=<?php echo urlencode($rows['university']);?>"><?php echo $rows['university'];?></a> &gt; 
		<a href="list.php?sub=<?php echo urlencode($rows['subject']);?>"><?php echo $rows['subject'];?></a> &gt; 
		<h1 class="inline"><?php echo $rows['name'];?></h1>
	</nav>
	
	<video src="videos/<?php echo $rows['id'];?>.mp4" controls preload="auto" autoplay id="view_video"></video>
	
	<aside>
		<p>贡献者：<?php echo $rows['contributor']?></p>
		<p>上传于<time datetime="<?php echo $rows['upload_date'];?>"><?php echo $rows['upload_date'];?></time></p>
		<p>播放次数：<?php echo $rows['play_times']?>次</p>
		<p><a href="videos/<?php echo $rows['id'];?>.mp4">下载该视频</a></p>
	</aside>
	
	<section id="comments">
		
		<?php
		
		if(isset($_SESSION['userName'])) {
			echo '<section id="edit">
				<form action="'.$_SERVER['PHP_SELF'].'?vid='.$_GET['vid'].'" method="post">
					<textarea name="comm" id="comm" require></textarea>
					<input type="submit" value="留言" class="floatRight" />
					<input type="hidden" name="submitted" value="true" />
					<input type="hidden" name="vid" value="'.$_GET['vid'].'" />
				</form></section>
			';
		}else {
			echo '<section id="edit"><p><a class="loginButton" href="login.php">登录</a>后即可留言。</p></section>';
		}
		
		$query = "SELECT * FROM comments WHERE which_video='".$_GET['vid']."'  and passed=1 ORDER BY date DESC";
		$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
		if(mysql_num_rows($result) != 0) {
		
			$pageSize = 2;		//每页项目数
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
			
			$query = "SELECT * FROM comments WHERE which_video='".$_GET['vid']."' and passed=1 ORDER BY date DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			while($rows = mysql_fetch_array($result)) {
				echo '
				<article class="comment">
					<header class="commentMeta">
						<p class="commentAuthor floatLeft">'.$rows['whose_comment'].'</p>
						<time class="commentDate tips floatRight" datetime="'.$rows['date'].'" pubdate>'.$rows['date'].'</time>
					</header>
					<p class="commentContent">
						'.$rows['content'].'
					</p>
				</article>';
			}
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?vid='.$_GET['vid'].'&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?vid='.$_GET['vid'].'&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?vid='.$_GET['vid'].'&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			echo '<p>暂无关于该视频的留言。</p>';
		}
		
		?>
	</section>
	
</section>

<?php
mysql_close();
require_once "footer.inc.php";
?>