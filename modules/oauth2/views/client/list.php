<?php if (Request::is_datatables()): ?>
	<?php echo $datatables->render(); ?>
<?php else:?>
	<?php Assets::datatables(); ?>
	
	<div class="wellact">
        <table class="table table-striped table-bordered table-highlight" data-toggle="datatable"
               data-ajax="<?php echo $url ?>" data-order='[["3", "desc"]]'>
		<thead>
			<tr>
                <th data-columns='{"width":"20%"}'><?php echo __('Title'); ?></th>
                <th data-columns='{"width":"30%"}'><?php echo __('Client Id'); ?></th>
                <th data-columns='{"width":"20%"}'><?php echo __('Created By'); ?></th>
                <th data-columns='{"searchable":false, "width":"20%"}'><?php echo __('Created On'); ?></th>
                <th data-columns='{"orderable":false, "searchable":false, "width":"10%"}'></th>
			</tr>
		</thead>
		<tbody>
			<tr>
                <td colspan="5" class="dt-empty"><?php echo __('Loading data from server'); ?></td>
			</tr>
		</tbody>
	</table>
	</div>
<?php endif; ?>