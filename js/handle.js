jQuery(function($) {
	
	function slideShow(id) {
		$('#sliderImages li').slice(0, id-1).animate({'left':'-625px'}, 500);
		$('#sliderImages li').eq(id-1).animate({'left':'0'}, 500);
		$('#sliderImages li').slice(id).animate({'left':'625px'}, 500);
	}
	
	function autoPlay() {
	
		var id = 1;
		var total = $('#sliderImages li').length;
		setInterval(function(){
		
			slideShow(id);
			id++;
			if(id > total) {
				id = 1;
			}
			
		},4000);
	}
	
	$(document).ready(function() {
		
		$('#sliderImages li').eq(0).css('left', '0px');
		$('#sliderImages li').slice(1).css('left', '625px');
		
		$('#sliderLinks a').click(function(){
			slideShow(this.id);
		});
		
		autoPlay();
		
	});

	//添加开关灯功能
	$('#view_video').before($('<a id="switch">关灯</a>'));
	$('#switch')
		.toggle(function() {
			$('body').css('backgroundColor','#000');
			$('#content').css('color', '#fff');
			$(this)
				.css('color', '#fff')
				.text('开灯');
		},
		function() {
			$('body').css('backgroundColor','#fff');
			$('#content').css('color', '#000');
			$(this)
				.css('color', '#447CD4')
				.text('关灯');
		});
	
	//需填表格自动获取焦点
	if($('.autoFocus').val() == "") {
		$('.autoFocus').focus();
	}
	
	//滚动最热课程
	setInterval(function(){
		$('#last ul li:last')
			.css('height','0px').hide()
			.prependTo($('#last ul'))
			.animate({'height':'25px'}, 1000).show();
	},3000);
	
	//显示登录窗口
	$('.loginButton').click(function(event) {
		event.preventDefault();
		$('#mask').css({'opacity':0.5, 'top':150, 'left':($('body').width()-350) / 2}).fadeIn(300);
		$('#window').css({'top':165, 'left':($('body').width()-320) /2}).fadeIn(300);
		$('.autoFocus').focus();
	});
	
	//关闭窗口
	$('.closeWindow').click(function(event) {
		event.preventDefault();
		$('#mask').add('#window').fadeOut(300);
	});
	
	//处理登录窗口
	$('#window>form').submit(function(event) {
		event.preventDefault();
		if($(this).find('input').eq(0).val() == "") {
			$(this).find('span').eq(0).text('<请填写邮箱');
			shake();
			return;
		}else {
			var reg = /^([a-zA-Z0-9_\-\.])+@([a-zA-Z0-9_\-])+(\.[a-zA-Z0-9_\-])+/;
			if(!reg.test($(this).find('input').eq(0).val())) {
				$(this).find('span').eq(0).text('<格式错误');
				shake();
				return;
			}
		}
		
		if($(this).find('input').eq(1).val() == "") {
			$(this).find('span').eq(1).text('<请填写密码');
			shake();
			return;
		}
		
		$('#window>form').find('span').eq(2).html('<img src="./images/ajax-loader.gif" />');
		$('#window>form').find('input').eq(3).attr('disabled','disabled');
		var data = "email=" + $(this).find('input').eq(0).val() + "&password=" + $(this).find('input').eq(1).val() + "&autologin=" + $(this).find('input')[2].checked;
		$.ajax({
			"type":"POST",
			"url":"login_by_ajax.php",
			"data":data,
			"dataType":"json",
			"success":function(data){
				if(data.error) {
					$('#window>form').find('span').eq(2).text(data.error);
					$('#window>form').find('input').eq(3).removeAttr('disabled');
				}else {
					$('#window>form').find('span').eq(2).text('');
					$('#mask').add('#window').fadeOut(300);
					
					$('#top').find('span').eq(0).html('<a href="upload.php">上传课程</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="manage.php?kind=v">管理</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="change_password.php">修改密码</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="./?logout">退出</a>');
					if($('#edit').length != 0) {
						var vid = getVid();
						if(vid) {
							
							$('#edit').html('<section id="edit"><form action="view.php?vid=' + vid + '" method="post"><textarea name="comm" id="comm" require></textarea><input type="submit" value="留言" class="floatRight" /><input type="hidden" name="submitted" value="true" /><input type="hidden" name="vid" value="'+ vid +'" /></form></section>');
						}
						
					}
				}
			}
		});
		
	});
	
	//获取vid
	function getVid() {
		var vid = false;
		var param = location.search;
		param = param.substring(1);
		var params = param.split("&");
		
		for(var i=0; i<params.length; i++) {
			if(params[i].indexOf("vid=") != -1) {
				vid = params[i].substring(4);
			}
		}
		
		return vid;
	}
	
	//处理留言提交
	$('#edit>form').live('submit', function(event) {
		event.preventDefault();
		if($.trim($('#comm').val()) == "") {
			return;
		}else {
			var vid = getVid();
			if(vid) {
				$(this).find('input').eq(0).attr('disabled','disabled');
				var data = "comm=" + $.trim($('#comm').val()) + "&vid=" + vid;
				$.ajax({
					"type":"GET",
					"url":"add_comment_by_ajax.php",
					"data":data,
					"dataType":"html",
					"success":function(data){
						$('#comments').html(data);
					}
				});
			}
		}
	});
	
	//抖动窗口
	function shake() {
		$('#mask').add('#window')
			.animate({'left':'-=100px'}, 25)
			.animate({'left':'+=200px'}, 100)
			.animate({'left':'-=200px'}, 100)
			.animate({'left':'+=200px'}, 100)
			.animate({'left':'-=100px'}, 25);
	}
	
	$('#uploadForm').live('submit', function(event) {
		$(this).find('.mt10').attr('disabled', 'disabled');
	});
	
});