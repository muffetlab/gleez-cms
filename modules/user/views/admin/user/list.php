<?php if ($is_datatables): ?>
	<?php echo $datatables->render(); ?>
<?php else:?>
	<?php Assets::datatables(); ?>

	<div class="help">
		<?php echo __('Gleez CMS allows users to register, login, log out, maintain user profiles, etc. Users of the site may not use their own names to post content until they have signed up for a user account.'); ?>
	</div>

    <?php echo HTML::anchor(Route::get('admin/user')->uri([
        'action' => 'add'
    ]), '<i class="fas fa-plus"></i> ' . __('Add New User'), [
        'class' => 'btn btn-success pull-right'
    ]); ?>
	<div class='clearfix'></div><br>

	<table id = "admin-list-users" class="table table-striped table-bordered table-highlight" data-toggle="datatable" data-ajax="<?php echo $url?>" data-order='[["2", "desc"]]'>
		<thead>
			<tr>
                <th data-columns='{"width":"20%"}' class="sorting_desc"><?php echo __('Username'); ?></th>
                <th data-columns='{"width":"22%"}' class="sorting_desc"><?php echo __('Email'); ?></th>
                <th data-columns='{"searchable":false, "width":"15%"}'><?php echo __('First Visit'); ?></th>
                <th data-columns='{"searchable":false, "width":"15%"}'><?php echo __('Last Visit'); ?></th>
                <th data-columns='{"orderable":false, "searchable":false, "width":"12%"}'><?php echo __('Roles') ?></th>
                <th data-columns='{"searchable":false, "className": "status", "width":"8%"}'><?php echo __('Status'); ?></th>
                <th data-columns='{"orderable":false, "searchable":false, "width":"8%"}'></th>
			</tr>
		</thead>
		<tbody>
			<tr>
                <td colspan="7" class="dt-empty"><?php echo __('Loading data from server'); ?></td>
			</tr>
		</tbody>
	</table>

<?php endif; ?>
