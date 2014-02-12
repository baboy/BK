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
		<script src="<?=$relatvie_path?>/static/js/init.js?<?=rand()?>"></script>
		<script src="<?=$relatvie_path?>/static/js/admin.js?<?=rand()?>"></script>
		<script src="<?=$relatvie_path?>/static/js/module.movie.js?<?=rand()?>"></script>
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
		<style>
			#container{
				width: 100%;
			}
			#content-wrapper{
				margin: 0;
				width:100%;
			}
		</style>
		<script>
			window.onload = function(){
				var appDiv = document.createElement("div");
				var nav = new NavigationHandler("app-content");
				nav.push(appDiv);
				appDiv.setTitle("Movie");
				var app = new MovieHandler(appDiv);
				app.showMovieList();
			}

		</script>
	</head>
	<body>
		<div id="container">
			<div id="main">
				<div id="content-wrapper">
					<div id="content">
						<div id="app-content"></div>
					</div>
				</div>
			</div>
		</div>

	</body>

</html>