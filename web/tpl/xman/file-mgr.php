<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
		<meta name="keywords" content="baboy, cms, php, BK framework, php framework">
		<meta name="description" content="cms adminitrator">
		<meta name="author" content="Baboy">
		<title>AppMan</title>
		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/main.css">
		<link rel="stylesheet" type="text/css" href="<?=$relatvie_path?>/static/css/file.mgr.css">
		<script src="<?=$relatvie_path?>/static/js/jquery.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/jquery.form.js" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/init.js?<?=rand()?>" charset="UTF-8"></script>
		<script src="<?=$relatvie_path?>/static/js/file.mgr.js?<?=rand()?>" charset="UTF-8"></script>
 		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/B.util.js?<?=rand()?>"></SCRIPT>
  		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/B.tree-1.01.js"></SCRIPT>
  		<SCRIPT LANGUAGE="JavaScript" src="<?=$relatvie_path?>/static/js/upload.js"></SCRIPT>
		<script src="<?=$relatvie_path?>/static/js/win/B.win.mgr.js?<?=rand()?>" charset="UTF-8"></script>
  		
		<script>


		</script>
	</head>
	<body>
		<div id="container">
			<header>
				<div class="notice-board-wrapper"><div id="notice-board">正在加载...</div></div>
				<div class="module-title">File Management</div>
			</header>
			
			<div id="main" style="border:1px solid #CCC;">
				<menu id="dir" class="menu">
				</menu>
				<div class="content-wrapper" id="content-wrapper">
					<menu class="h-menu" style="border-bottom:1px solid #ccc;">
						<ul class="menu-group">
							<li class="title left" role="menu-mkfile" style="position:relative">
								<strong>+File</strong>
								<div style="position:absolute;top:0;left:0;visible:hidden;opacity:0;">
									<input type="file" style="width:50px;height:30px" onchange="selectFile()">
								</div>
							</li>
							<li class="title" role="menu-mkdir">+Dir</li>
							<li class="title" role="menu-mkdir">+Rename</li>
							<li class="title" role="menu-del">+Delete</li>
						</ul>
						<ul class="menu-group" style="display:none">
							<li class="title" role="back">&lt; back</li>
						</ul>
					</menu>
					<div id="upload">
					</div>
					<div>
						<table class="list">
							<thead>
								<tr>
									<th class="cb"><input type="checkbox" role="select-all"/></th>
									<th style="">名称</th>
									<th style="width:60px">类型</th>
									<th style="width:80px">创建者</th>
									<th style="width:180px">日期</th>
								</tr>
							</thead>
							<tbody id="list-content">
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</body>

</html>