<table class="list">
	<thead>
		<th class="cb"><input type="checkbox" role="select-all"/></th>
		<th style=''>Title</th>
		<th style='width:60px'>Publish</th>
		<th style='width:100px'>Type</th>
		<th style='width:0px'>Weight</th>
		<th style='width:60px'>Status</th>
		<th style='width:100px;border-right:none;'>Date</th>
	</thead>
	<tbody>
		<?php 
		$list = $data;
		for ($i = 0, $n = count($list); $i < $n; $i++) { 
			$item = $list[$i];
			$bgColor = ($i%2==0 ? "#FEFEFE" : "#EFEFEF");
		?>
		<tr class="list-row" style='background:<?=$bgColor?>'>
			<td style="text-align:center"><input type="checkbox"/></td>
			<td class="t" role="toggle-detail" id="<?=$item->sid?>"><img src='<?=$item->thumbnail?>'><span><?=_s($item->title)?></span></td>
			<td><?=_s($item->platform)?></td>
			<td><?=_s($item->Type)?></td>
			<td><?=_s($item->weight)?></td>
			<td><?=_s($item->status)?></td>
			<td><?=_s($item->pubdate)?></td>
		</tr>
		<?php } ?>
	</tbody>
</table>