<table>
	<thead>
		<th style='width:100px'>Channel</th>
		<th style='width:100px'>Version</th>
		<th style='width:100px'>Build</th>
		<th>Description</th>
		<th style='width:100px'>Download</th>
	</thead>
	<tbody>
		<tr class='row-input' role='add' style='background:#EEE;display:none1'>
			<td colspan='5'>
				<div class='div-input'>
					<form action="/app/add/build" method="post">
						<div><label>Product Name</label><span>--</span></div>
						<div><label>Package</label><span>--</span></div>
						<div><label>channel:</label><input type='text' name='channel'/></div>
						<div><label>version:</label><input type='text' name='version'/></div>
						<div><label>build:</label><input type='text' name='build'/></div>
						<div><label>description:</label><input type='text' name='description'/></div>
						<div><label>App:</label><input type='file' name='app_file'/><input type="hidden" name="download_url"/></div>
						<input type="hidden" name="appid" value="<?=$data->id?>"/>
						<div class='div-submit'><button role='add-build' class='submit'>Submit</button></div>
					</form>
				</div>
			</td>
		</tr>
		<?php 
		$list = $data->list;
		for ($i = 0, $n = count($list); $i < $n; $i++) { 
			$item = $list[$i];
			$bgColor = ($i%2==0 ? "#FEFEFE" : "#EFEFEF");
		?>
		<tr style='background:<?=$bgColor?>'>
			<td><span role="view-detail" data='<?=json_encode($item)?>'><?=_s($item->channel)?></span></td>
			<td><?=_s($item->version)?></td>
			<td><?=_s($item->build)?></td>
			<td><?=_s($item->description)?></td>
			<td class='row-action'>
				<a href="<?=_s($itemdownload_url)?>" role='download' data='<?=json_encode($item)?>'>Download</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>