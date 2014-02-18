<table>
	<thead>
		<th style="width:100px">Name</th>
		<th style="width:200px">Package</th>
		<th style="width:100px">Developer</th>
		<th>Description</th>
		<th style="width:100px">Action</th>
	</thead>
	<tbody>
		<tr class='row-input' role='add' style='background:#EEE;display:none1'>
			<td colspan='5'>
				<div class='div-input'>
					<form action="/app/register" method="post">
						<div><label>Product Name</label><input type='text' name='name'/></div>
						<div><label>Package</label><input type='text' name='package'/></div>
						<div><label>Developer:</label><input type='text' name='developer'/></div>
						<div class='div-submit'><button role='add-product' class='submit'>Submit</button></div>
					</form>
				</div>
			</td>
		</tr>
		<?php 
		for ($i = 0, $n = count($data); $i < $n; $i++) { 
			$item = $data[$i];
			$bgColor = ($i%2==0 ? "#FEFEFE" : "#EFEFEF");
		?>
		<tr style='background:<?=$bgColor?>'>
			<td><span role="view-detail" data='<?=json_encode($item)?>'><?=_s($item->name)?></span></td>
			<td><?=_s($item->package)?></td>
			<td><?=_s($item->developer)?></td>
			<td class="desc"><?=_s($item->description)?></td>
			<td class='row-action'>
				<a role='view-builds' data='<?=json_encode($item)?>' id="app-<?=$item->id?>">Builds</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>