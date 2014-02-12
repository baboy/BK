<?php
	if(empty($data))
		return;
	$list = $data;
	foreach ($list as $k => $item) {
?>
	<tr>
		<td><input type="checkbox" role="select-all"/></td>
		<td><span role="file-name" fid="<?=$item->id?>"><?=$item->title?></span></td>
		<td><span><?=$item->ext?></span></td>
		<td><span></span></td>
		<td><span><?=$item->create_time?></span></td>
	</tr>
<?php
	}
?>