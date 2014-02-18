<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="keywords" content="baboy, cms, php, BK framework, php framework">
		<meta name="description" content="cms adminitrator">
		<meta name="author" content="Baboy">
		<title>AppMan</title>
		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/main.css">
		<script src="<?=$relatvie_path?>/static/js/jquery.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/jquery.form.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/B.util.js?<?=rand()?>" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/init.js?<?=rand()?>" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/admin.js?<?=rand()?>" charset="UTF-8"></script>


		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/B.win.css">
		<script src="<?=$relatvie_path?>/static/js/win/B.resizable.js?<?=rand()?>" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/win/B.win.js?<?=rand()?>" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/win/B.win.mgr.js?<?=rand()?>" charset="UTF-8"></script>
		<style>
			a {
				color:blue;
				cursor:pointer;
			}
			table, td{
				 border-spacing: 0;
			}
			iframe{
				border:none;
				width:100%;
				height:100%;
			}
		</style>
		<style>
			.div-input{
				padding: 10px 20px 10px;
			}
			.div-input label{
				display:block;
				float:left;
				width:150px;
				font: 80%;
				color:#555;
			}
			.div-submit{
				padding:10px  0  10px 150px;
			}
			#overlay{
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background: #cccccc;
				display: none;
			}
			#overlay iframe{
				width: 100%;
				height: 100%;
				border: none;

			}
		</style>
		<script>
			window._insert_ = function(){
				console.log(arguments);
			}
			window.onload = function(){
				$("#content").attr("src","/xman/news");
			}
			$(document.body).ready(function(){
				$(window).resize(function() {
				    var W = $("#container").width();
				    var H = $("#container").height();
				    var mainWidth = W;
				    var mainHeight = H - $("header").height();
				    var menuWidth = $("#menu").width();
				    console.log(menuWidth);
				    var contentWidth = (mainWidth-menuWidth);
				    $("#main").css("height", mainHeight);
				    $("#content-wrapper").css("width",contentWidth);
				 });
				$(window).resize();
			});

		</script>
	</head>
	<body>
		<div id="container">
			<header>
				<div class="notice-board-wrapper"><div id="notice-board">正在加载...</div></div>
				<div class="site-title">BK Simple Framework</div>
			</header>
			<div id="main" style="border:1px solid #CCC;">
				<menu id="menu" class="menu">
					<ul class="menu-item">
						<li class="title">App Man</li>
						<li>
							<ul>
								<li>App</li>
							</ul>
						</li>
					</ul>
				</menu>
				<div class="content-wrapper" id="content-wrapper">
					<iframe id="content"></iframe>
				</div>
			</div>
		</div>

	</body>

</html>