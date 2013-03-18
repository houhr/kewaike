<?php
require_once "header.inc.php";
?>

	
<section id="content">

	<section id="show">
		<section id="slider">
			<ul id="sliderImages">
				<li><img src="images/1.jpg"/></li>
				<li><img src="images/2.jpg"/></li>
				<li><img src="images/3.jpg"/></li>
			</ul>
			<p id="sliderLinks">
				<a id="1" href="#">1</a>
				<a id="2" href="#">2</a>
				<a id="3" href="#">3</a>
			</p>
		</section>
		
		<section id="newest">
			<h1>最新收录</h1>
			<?php
			require_once "./mysql_connect.php";
			$query = "SELECT * FROM videos WHERE passed=1 ORDER BY upload_date DESC LIMIT 1 ";
			$result = mysql_query($query);
			$rows = mysql_fetch_array($result);
			
			echo '<p><a href="list.php?uni='.urlencode($rows['university']).'">'.$rows['university'].'</a> > <a href="view.php?vid='.$rows['id'].'" title="'.$rows['name'].'">'.$rows['name'].'</a></p>
			<video src="videos/'.$rows['id'].'.mp4" width="300" controls preload="auto"></video>';
			
			?>
			
		</section>
	</section>
	
	<section class="clearfix">
		<nav class="hot">
			<h1>热门大学</h1><span class="more"><a href="list.php?lu">更多大学»</a></span>
			<ul>
				<?php
				
				$query = "SELECT DISTINCT university FROM videos ORDER BY RAND()  LIMIT 20";
				$result = mysql_query($query);
				
				while($rows = mysql_fetch_array($result)) {
					
					echo '<li><a href="list.php?uni='.urlencode($rows['university']).'">'.$rows['university'].'</a></li>';
					
				}
				
				?>
			</ul>
		</nav>
		<nav class="hot">
			<h1>热门学科</h1><span class="more"><a href="list.php?ls">更多学科»</a></span>
			<ul>
				<?php
				
				$query = "SELECT DISTINCT subject FROM videos ORDER BY RAND() LIMIT 20";
				$result = mysql_query($query);
				while($rows = mysql_fetch_array($result)) {
					
					echo '<li><a href="list.php?sub='.urlencode($rows['subject']).'">'.$rows['subject'].'</a></li>';
					
				}
				
				?>
			</ul>
		</nav>
		<nav id="last">
			<h1>热门课程</h1><span class="more"></span>
			<ul>
				<?php
				
				$query = "SELECT * FROM videos ORDER BY play_times DESC LIMIT 15";
				$result = mysql_query($query);
				while($rows = mysql_fetch_array($result)) {
					
					echo '<li>&gt; <a href="view.php?vid='.$rows['id'].'" title="' . $rows['name'] . '">'.$rows['name'].'</a></li>' . "\n";
				}
				
				?>
			</ul>
		</nav>
	</section>

</section>
	
<?php
require_once "footer.inc.php";
?>