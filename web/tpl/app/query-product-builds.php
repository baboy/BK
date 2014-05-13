<div class="nh-top">
	<a class="nh-back" role="push-back" href="#">&lt;返回</a>
	<div class="nh-title-item-right" role="toggle" toggle="addBuild">+Add</div>
	<div class="nh-title">
		App Builds For <strong>[<?=$data->name?>]</strong> Product:<strong>[<?=$data->product_id?>]</strong>
	</div>
</div>
<div>
	<table>
		<thead>
			<th style='width:100px'>Channel</th>
			<th style='width:100px'>Version</th>
			<th style='width:100px'>Build</th>
			<th>Description</th>
			<th style='width:100px'>Download</th>
		</thead>
		<tbody>
			<tr class='row-input' role='add' id="addBuild" style="display:none">
				<td colspan='5'>
					<div class='div-input'>
						<form action="/app/add/build" method="post"  onsubmit="return false">
							<div><label>Product Name</label><span><?=$data->name?></span></div>
							<div><label>Product</label><span><?=$data->product_id?></span></div>
							<div><label>channel:</label><input type='text' name='channel'/></div>
							<div><label>version:</label><input type='text' name='version'/></div>
							<div><label>build:</label><input type='text' name='build'/></div>
							<div><label>description:</label><input type='text' name='description'/></div>
							<div>
								<label>App:</label><a id="display-download_url" input="download_url"></a>
								<input type='file' name='app_file' id="app_file" onchange="uploadAppPackage()" role="upload-file"/>
								<input type="hidden" name="download_url" id="download_url" display="display-download_url"/>
							</div>
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
				<td class="desc"><?=_s($item->description)?></td>
				<td class='row-action'>
					<a href="<?=_s($item->download_url)?>" role='download' data='<?=json_encode($item)?>'>Download</a>
				</td>
			</tr>
			<?php } ?>
		</tbody>
	</table>
</div>