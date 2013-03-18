<?php
$title = "课外课-上传课程";
require_once "header.inc.php";

if(!isset($_SESSION['userName'])) {
	ob_end_clean();
	header("Location: " . BASEPATH);
	exit();
}

?>

<section id="content">
	
	<nav id="mainNav"><a href="<?php echo BASEPATH;?>">首页</a> &gt; <h1 class="inline">上传课程</h1></nav>
	<p class="cb">注意：仅支持上传512MB以下mp4格式的视频文件。</p>
	
	<?php
	//如果提交了表单
	if(isset($_POST['submitted']) && isset($_FILES['upload'])) {
	
		echo "<section id=\"msg\">\n<p>提示信息：</p>";
		
		require_once "./mysql_connect.php";
		//上传成功标志设置为false
		$success = false;
		//初始化错误记录数组
		$errors = array();
		//检查各项输入的数据
		if(empty($_POST['university'])) {
			$errors[] = '<p>请填写大学名称。</p>';
		} else {
			$cleanUniversity = escapeInput($_POST['university']);
		}
		
		if(empty($_POST['subject'])) {
			$errors[] = '<p>请填写所属学科。</p>';
		} else {
			$cleanSubject = escapeInput($_POST['subject']);
		}
		
		if(empty($_POST['name'])) {
			$errors[] = '<p>请填写课程名称。</p>';
		} else {
			$cleanName = escapeInput($_POST['name']);
		}
		
		if(empty($errors)) {
			//如果文件小于512MB
			if($_FILES['upload']['size'] > 0 && $_FILES['upload']['size'] < 536870912) {
				//如果文件为MP4格式
				if($_FILES['upload']['type'] == 'video/mp4') {
					//重命名文件
					$tmpFileName = $_SERVER['REMOTE_ADDR'];
					if(move_uploaded_file($_FILES['upload']['tmp_name'], "videos/$tmpFileName.mp4")) {
						//将视频信息插入到数据库中
						
						$query = "INSERT INTO videos (id, university, subject, name, upload_date, contributor) VALUES (NULL, '$cleanUniversity', '$cleanSubject', '$cleanName', NOW(), '".$_SESSION['userName']."')";
						$result = mysql_query($query) or trigger_error("Query: $query \n<br />MySQL Error: " . mysql_error());
						if(mysql_affected_rows() == 1) {
							$finalFileName = mysql_insert_id();
							rename("videos/$tmpFileName.mp4", "videos/$finalFileName.mp4");
							echo "<p>视频上传成功！<br />待管理员审核中，你可以观看其他视频或者继续上传。</p>";
							//上传成功标志设置为true
							$success = true;
							//关闭数据库连接
							mysql_close();
						}
					}else {
						echo '<p>文件上传失败：';
						switch ($_FILES['upload']['error']) {
							case 1:
								echo '文件大小超过服务器所允许上传的值。';
								break;
							case 2:
								echo '文件大小超过HTML中MAX_FILE_SIZE所设置的值。';
								break;
							case 3:
								echo '文件没有完全上传。';
								break;
							case 4:
								echo '没有指定上传的文件。';
								break;
							default:
								echo '系统错误。';
						}
						echo '</p>';
					}
				}else {
					echo '<p>请选择mp4类型的文件。</p>';
				}
			}else {
				echo '<p>请选择小于512MB的文件。</p>';
			}
		}else {
			foreach($errors as $msg) {
				echo "<p>$msg</p>";
			}
		}
		
		echo "</section>";//结束提示信息section
	}
	?>
	
	<form id="uploadForm" enctype="multipart/form-data" action="upload.php" method="post">
		<p><label for="university">所属大学：</label><br /><input type="text" class="autoFocus" name="university" value="<?php if(isset($_POST['university']) && !$success) echo $_POST['university']; ?>" required /><span class="tips">&nbsp;例如：哈佛大学</span></p>
		<p><label for="subject">所属学科：</label><br /><input type="text" name="subject" value="<?php if(isset($_POST['subject']) && !$success) echo $_POST['subject']; ?>" required /><span class="tips">&nbsp;例如：心理学</span></p>
		<p><label for="name">课程名称：</label><br /><input type="text" name="name" size="80" value="<?php if(isset($_POST['name']) && !$success) echo $_POST['name']; ?>" required /></p>
		<p><span class="tips">请选择512MB以下mp4格式的视频文件</span><br /><input type="hidden" name="MAX_FILE_SIZE" value="536870912" />
		<input type="file" name="upload" /></p>
		<p><input type="hidden" name="submitted" value="true" />
		<input type="submit" name="submit" value="开始上传" class="mt10" />&nbsp;<span class="tips">上传过程可能较长，请耐心等待，中间请勿重复点击“开始上传”。</span></p>
	</form>
	
</section>

<?php
require_once "footer.inc.php";
?>