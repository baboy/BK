<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;UTF-8">
		<meta name="keywords" content="baboy, cms, php, BK framework, php framework">
		<meta name="description" content="cms adminitrator">
		<meta name="author" content="Baboy">
		<title><?php echo $title;?></title>
		<script src="<?=$relatvie_path?>/static/js/jquery.js"></script>
		<script src="<?=$relatvie_path?>/static/js/app.package.js"></script>
		<style>
			html,body{
				padding:0;
				margin: 0;
			}
			.site-title{
				font-weight: 900;
				font-size: 28px;
			}
			header{
				border-bottom:1px solid #ccc;
				height:120px;
				line-height: 120px;
				text-align: center;
			}
			menu{
				float:left;
				width:200px;
				height:1000px;
				padding: 0;
				margin: 0;
				border-right:1px solid #ccc;
			}
			menu li{
				list-style-type:none;
			}
			menu ul.menu-item{
				margin: 0;
				padding: 0;
				border-bottom1:1px solid #ccc;
			}
			menu .menu-item li.title{
				height: 40px;
				line-height: 40px;
				padding-left: 10px;
				border-bottom:1px solid #ccc;
			}
			menu ul.menu-item ul{
				margin: 0;
				padding: 0;
				margin-left:20px;
			}
			menu ul.menu-item ul li{
				padding:10px 5px;
				border-bottom:1px solid #ccc;
			}
			.title{
				font-weight: 900;
			}
			.content-wrapper{
				margin-left:200px;
			}
		</style>
		<style>
			.nh-top{
				height:40px;
				line-height: 40px;
				border-bottom: 1px solid #ccc;
				background: #fefefe;
			}
			.nh-top .nh-back{
				float:left;
				height:100%;
			}
			.nh-top .nh-title{
				height:100%;
				text-align: left;
				padding-left: 10px;
				vertical-align: middle;;
			}
		</style>
		<style>
			a {
				color:blue;
				cursor:pointer;
			}
			table, td{
				 border-spacing: 0;
			}
			.app-content table{
				width:99.9%;
			}

			.app-content table thead{
				background: #eee;
			}
			.app-content table thead tr th{
				text-align: left;
				padding-left:5px;
				background: #eee;
				border-bottom:1px solid #ccc;
				border-right:1px solid #ccc;
			}
			.app-content table thead th{
				height:20px;
			}
			.app-content table thead th.app-name{
				width: 100px;
			}

			.app-content table thead th.app-package{
			}
			.app-content table thead th.app-developer{
				width:100px;
			}
			

			.app-content table thead th.app-action{
				width: 150px;
			}
			.app-content table thead th.app-action a{

			}
			.app-content table td{
				padding:5px;
			}
			.app-content table tbody tr td{
				border-bottom:1px solid #CCC;
			}

		</style>
		<script>
			window.onload = function(){
				var obj = document.createElement("div");
				var nav = new NavigationHandler("app-content");
				nav.push(obj);
				obj.setTitle("title");

				var app = new AppHandler(obj);
				app.query();
			}
		</script>
	</head>
	<body>
		<header>
			<div class="site-title">BK Simple Framework</div>
		</header>
		<menu>
			<ul class="menu-item">
				<li class="title">App Man<li>
				<li>
					<ul>
						<li>App</li>
					</ul>
				</li>
			</ul>
		</menu>
		<div class="content-wrapper" id="content-wrapper">
			<div class="app-desc"></div>
			<div class="app-content" id="app-content"></div>
		</div>

	</body>

</html>