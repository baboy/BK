<table>
	<thead>
		<th class='app-name'>Name</th>
		<th class='app-package'>Package</th>
		<th class='app-developer'>Developer</th>
		<th class='app-action'>Action</th>
	</thead>
	<tbody>
		<tr class='row-input' role='add' style='background:#EEE;display:none1'>
			<td colspan='4'>
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
			<td class='row-action'>
				<a role='view-builds' data='<?=json_encode($item)?>'>Builds</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>