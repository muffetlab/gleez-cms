<?php if (Request::is_datatables()): ?>
	<?php echo $datatables->render(); ?>
<?php else:?>
	<?php Assets::datatables(); ?>
	
	<div class="wellact">
	<table id="datatable-oaclient" class="table table-striped table-bordered table-highlight" data-toggle="datatable" data-ajax="<?php echo $url?>" data-order='[["3", "desc"]]'>
		<thead>
			<tr>
				<th width="20%"><?php echo __("Title"); ?></th>
				<th width="30%"><?php echo __("Client Id"); ?></th>
				<th width="20%"><?php echo __("Created By"); ?></th>
                <th width="20%" data-columns='{"searchable":false}'><?php echo __("Created On"); ?></th>
				<th width="10%"  data-columns='{"orderable":false, "searchable":false}'></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td colspan="5" class="dt-empty"><?php echo __("Loading data from server"); ?></td>
			</tr>
		</tbody>
	</table>
	</div>
<?php endif; ?>