<?php
session_name('kewaike');
session_start();
if(isset($_SESSION['userName']) && isset($_GET['comm']) && isset($_GET['vid'])) {
	
	require_once "./mysql_connect.php";
	
	if(!empty($_GET['comm'])) {
		
		$cleanComm = escapeInput($_GET['comm']);
		if(!empty($cleanComm)) {
			
			$commQuery = "INSERT INTO comments (id, which_video, whose_comment, content, date) VALUES (NULL, '".$_GET['vid']."', '".$_SESSION['userName']."','$cleanComm', NOW())";
			$commResult = mysql_query($commQuery);
		}
	}
	
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
	
	$query = "SELECT * FROM comments WHERE which_video='".$_GET['vid']."' ORDER BY date DESC";
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
		
		$query = "SELECT * FROM comments WHERE which_video='".$_GET['vid']."' ORDER BY date DESC LIMIT $offset,$pageSize";
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
	
}

?>