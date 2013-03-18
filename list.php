<?php
require_once "header.inc.php";
?>

<section id="content" class="clearfix">
	
<?php
if(empty($_GET['uni']) && empty($_GET['sub']) && !isset($_GET['lu']) && !isset($_GET['ls']) && !isset($_GET['q'])) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}else {
	require_once "./mysql_connect.php";
	//处理学科
	if(!empty($_GET['uni'])) {
		
		$cleanUni = escapeInput($_GET['uni']);
		$query = "SELECT * FROM videos WHERE university='$cleanUni'";
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
			
			$query = "SELECT * FROM videos WHERE university='$cleanUni' ORDER BY id DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '<nav id="mainNav"><a href="'. BASEPATH .'">首页</a> &gt; <h1 class="inline">'.$cleanUni.'</h1></nav><ol class="cb">';
			while($rows = mysql_fetch_array($result)) {
				
				echo '
				<li>
					<a href="list.php?sub='.urlencode($rows['subject']).'">'.$rows['subject'].'</a> &gt;
					<a href="view.php?vid='.$rows['id'].'">'.$rows['name'].'</a>
				</li>
				';
				
			}
			echo '</ol>';
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?uni='.$_GET['uni'].'&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?uni='.$_GET['uni'].'&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?uni='.$_GET['uni'].'&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			
			ob_end_clean();
			header("Location: " . BASEPATH);
			exit();
			
		}
	
	//处理大学
	}else if(!empty($_GET['sub'])) {
		
		$cleanSub = escapeInput($_GET['sub']);
		$query = "SELECT * FROM videos WHERE subject='$cleanSub'";
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
			
			$query = "SELECT * FROM videos WHERE subject='$cleanSub' ORDER BY id DESC LIMIT $offset,$pageSize";
			$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
			
			echo '<nav id="mainNav"><a href="'. BASEPATH .'">首页</a> &gt; <h1 class="inline">'.$cleanSub.'</h1></nav><ol class="cb">';
			while($rows = mysql_fetch_array($result)) {
				
				echo '
				<li>
					<a href="list.php?uni='.urlencode($rows['university']).'">'.$rows['university'].'</a> &gt;
					<a href="view.php?vid='.$rows['id'].'">'.$rows['name'].'</a>
				</li>
				';
				
			}
			echo '</ol>';
			
			if ($total > $pageSize) {
				
				echo '<nav id="pageNav">';
					if ($page != 1) {
						echo '<a href="?sub='.$_GET['sub'].'&page=1">&lt;&lt;第一页</a>';
						echo '<a href="?sub='.$_GET['sub'].'&page=' . $pre . '">&lt;上一页</a>';
					}
					if ($page != $pageCount) {
						echo '<a href="?sub='.$_GET['sub'].'&page=' . $next . '">下一页&gt;</a>';
					}
				echo '</nav>';
				
			}
			
		}else {
			
			ob_end_clean();
			header("Location: " . BASEPATH);
			exit();
			
		}
	
	//大学一览
	}else if(isset($_GET['lu'])) {
	
		$query = "SELECT DISTINCT university FROM videos";
		$result = mysql_query($query);
		
		echo '<nav id="mainNav"><a href="'. BASEPATH .'">首页</a> &gt; <h1 class="inline">大学一览</h1></nav><ul class="horizontal">';
		while($rows = mysql_fetch_array($result)) {
		
			echo '<li><a href="list.php?uni='.urlencode($rows['university']).'">'.$rows['university'].'</a></li>';
		
		}
		echo '</ul>';
	
	//学科一览
	}else if(isset($_GET['ls'])) {
	
		$query = "SELECT DISTINCT subject FROM videos";
		$result = mysql_query($query);
		
		echo '<nav id="mainNav"><a href="'. BASEPATH .'">首页</a> &gt; <h1 class="inline">学科一览</h1></nav><ul class="horizontal">';
		while($rows = mysql_fetch_array($result)) {
		
			echo '<li><a href="list.php?sub='.urlencode($rows['subject']).'">'.$rows['subject'].'</a></li>';
		
		}
		echo '</ul>';
	
	//处理搜索
	}else if(isset($_GET['q'])) {
		
		$cleanQ = escapeInput($_GET['q']);
		if(!empty($cleanQ)) {
			echo '<nav id="mainNav"><a href="'. BASEPATH .'">首页</a> &gt; <h1 class="inline">搜索结果</h1></nav>';
			
			$query1 = "SELECT * FROM videos WHERE name LIKE '%$cleanQ%'";
			$result1 = mysql_query($query1);
			$num1 = mysql_num_rows($result1);
			$query2 = "SELECT * FROM videos WHERE subject LIKE '%$cleanQ%'";
			$result2 = mysql_query($query2);
			$num2 = mysql_num_rows($result2);
			$query3 = "SELECT * FROM videos WHERE university LIKE '%$cleanQ%'";
			$result3 = mysql_query($query3);
			$num3 = mysql_num_rows($result3);
			
			$total = $num1 + $num2 + $num3;		//总项目数
			
			if($total != 0) {
				echo '<ul>';
			}else {
				echo '<p>抱歉，没有找到与“'.$cleanQ.'”相关的课程。</p>';
			}
			
			if($num1 != 0) {
				
				while($rows1 = mysql_fetch_array($result1)) {
				
					echo '<li>
							<a href="list.php?uni='.urlencode($rows1['university']).'">'.$rows1['university'].'</a> &gt;
							<a href="list.php?sub='.urlencode($rows1['subject']).'">'.$rows1['subject'].'</a> &gt;
							<a href="view.php?vid='.$rows1['id'].'">'.$rows1['name'].'</a>
						</li>';
				
				}
				
			}
			
			if($num2 != 0) {
				
				while($rows2 = mysql_fetch_array($result2)) {
				
					echo '<li>
							<a href="list.php?uni='.urlencode($rows2['university']).'">'.$rows2['university'].'</a> &gt;
							<a href="list.php?sub='.urlencode($rows2['subject']).'">'.$rows2['subject'].'</a> &gt;
							<a href="view.php?vid='.$rows2['id'].'">'.$rows2['name'].'</a>
						</li>';
				
				}
				
			}
			
			if($num3 != 0) {
				
				while($rows3 = mysql_fetch_array($result3)) {
				
					echo '<li>
							<a href="list.php?uni='.urlencode($rows3['university']).'">'.$rows3['university'].'</a> &gt;
							<a href="list.php?sub='.urlencode($rows3['subject']).'">'.$rows3['subject'].'</a> &gt;
							<a href="view.php?vid='.$rows3['id'].'">'.$rows3['name'].'</a>
						</li>';
				
				}
				
			}
			
			if($total != 0) {
				echo '</ul>';
			}
			
		}else {
			ob_end_clean();
			header("Location: " . BASEPATH);
			exit();
		}
		
	}
}

?>
	
</section>

<?php
mysql_close();
require_once "footer.inc.php";
?>