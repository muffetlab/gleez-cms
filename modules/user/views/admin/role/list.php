<?php if ($is_datatables): ?>
	<?php echo $datatables->render(); ?>
<?php else:?>
	<?php Assets::datatables(); ?>
	<div class="help">
		<p><?php echo __('Roles allow you to fine tune the security and administration of Gleez CMS. A role defines a group of users that have certain privileges as defined in user permissions. Examples of roles include: anonymous user, authenticated user, moderator, administrator and so on. In this area you will define the role names of the various roles.'); ?></p>
	</div>

    <?php echo HTML::anchor($add_url, '<i class="fas fa-plus"></i> ' . __('Add New Role'), [
        'class' => 'btn btn-success pull-right'
    ]); ?>
	<div class="clearfix"></div><br>

	<table id = "admin-list-paths" class="table table-striped table-bordered table-highlight" data-toggle="datatable" data-ajax="<?php echo $url?>" data-order='[["0", "desc"]]'>
		<thead>
		<tr>
            <th data-columns='{"width":"20%"}' class="sorting_desc"><?php echo __('Name'); ?></th>
            <th data-columns='{"width":"60%"}' class="sorting_desc"><?php echo __('Description'); ?></th>
            <th data-columns='{"searchable":false, "className": "status", "width":"10%"}'><?php echo __('Special'); ?></th>
            <th data-columns='{"orderable":false, "searchable":false, "width":"10%"}'></th>
		</tr>
		</thead>
		<tbody>
		<tr>
            <td colspan="4" class="dt-empty"><?php echo __('Loading data from server'); ?></td>
		</tr>
		</tbody>
	</table>

<?php endif; ?>
