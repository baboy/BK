<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;UTF-8">
		<meta name="keywords" content="baboy, cms, php, BK framework, php framework">
		<meta name="description" content="cms adminitrator">
		<meta name="author" content="Baboy">
		<title>AppMan</title>
		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/main.css">
		<script src="<?=$relatvie_path?>/static/js/jquery.js"></script>
		<script src="<?=$relatvie_path?>/static/js/jquery.form.js"></script>
		<script src="<?=$relatvie_path?>/static/js/B.util.js?<?rand()?>"></script>
		<script src="<?=$relatvie_path?>/static/js/init.js?<?rand()?>"></script>
		<script src="<?=$relatvie_path?>/static/js/app.js?<?rand()?>"></script>
		<script src="<?=$relatvie_path?>/static/js/upload.js?<?rand()?>"></script>
		<style>
			html,body{
				padding:0;
				margin: 0;
				background:#EEE;
				height:100%;
				font-size:14px;
			}
			#container{
				width:1024px;
				margin: 0 auto 0;
				height:100%;
			}
			#main{
				border:1px solid #CCC;
				background: white;
				height:100%;
			}
			#content-wrapper{
				margin:0;
				width:100%;
			}
			.site-title{
				font-weight: 900;
				font-size: 28px;
				line-height: 100px;
			}
			header{
				height1:120px;
				line-height1: 120px;
				text-align: center;
			}
			menu{
				float:left;
				width:200px;
				height:100%;
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
				background: #DDD;
			}
			.notice-board-wrapper{
			    text-align:center;
			    position:absolute;
			    width:80%;
			    top:-1px;
			}
			#notice-board{
			    background:#FFF1A8;
			    font-weight:900;
			    font-size:12px;
			    color:black;
			    width:120px;
			    padding:3px 5px;
			    text-align:center;
			    display:none;
			    margin-left:auto;
			    margin-right:auto;

			    border-color:#ccc;
			    border-width:1px;
			    border-style:solid;
			    border-radius:6px;
			    -webkit-border-radius:6px;
			    -moz-border-radius:6px;
			    border-top-left-radius: 0;
			    border-top-right-radius: 0;
			    -moz-border-radius-topleft: 0;
			    -moz-border-radius-topright: 0;
			    -webkit-border-top-left-radius: 0;
			    -webkit-border-top-right-radius: 0;
			}
		</style>
		<style>
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
				width:100%;
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
		</style>
	</head>
	<body>
		<div id="container">
			<header>
				<div class="notice-board-wrapper"><div id="notice-board">正在加载...</div></div>
				<div class="site-title">BK Simple Framework</div>
			</header>
			<div id="main">
				<div class="content-wrapper" id="content-wrapper">
					<div class="app-desc"></div>
					<div class="app-content" id="app-content"></div>
				</div>
			</div>
		</div>

	</body>

</html>