<?php echo HTML::anchor(Route::get('admin/term')->uri($params), '<i class="fas fa-plus"></i> ' . __('Add New Term'), ['title' => __('Add New Term'), 'class' => 'btn btn-success pull-right']); ?>
<div class="clearfix"></div><br>

<?php echo Form::open(Route::get('admin/term')->uri(['action' => 'confirm', 'id' => $id]), ['id' => 'menu-form', 'class' => 'form']); ?>
	<div class="clearfix"></div>

<table id="term-admin-list" class="table table-striped table-bordered table-highlight" data-toggle="tableDrag">
		<thead>
		<tr>
			<th width="30%"><?php echo __('Name'); ?></th>
            <th class="table-drag-hide"><?php echo __('Weight'); ?></th>
			<th width="50%"><?php echo __('Description'); ?></th>
			<th width="10%"><?php echo __('Actions'); ?></th>
		</tr>
		</thead>
		<tbody>
		<?php foreach ($terms as $item): ?>
			<tr id="term-row-<?php echo $item['id'] ?>" class="draggable">
				<td id="term-<?php echo $item['id'] ?>">
					<?php
						$c = 2;
						while ($c < $item['lvl'])
						{
							echo '<div class="indentation">&nbsp;</div>';
							$c++;
						}

						echo HTML::chars($item['name'])
					?>
				</td>
                <td class="table-drag-hide">
                    <?php echo Form::weight('tid:' . $item['id'] . '[weight]', 0, ['class' => 'row-weight']) ?>
                    <?php echo Form::hidden('tid:' . $item['id'] . '[pid]', $item['pid'], ['class' => 'row-parent']) ?>
                    <?php echo Form::hidden('tid:' . $item['id'] . '[tid]', $item['id'], ['class' => 'row-id']) ?>
                    <?php echo Form::hidden('tid:' . $item['id'] . '[depth]', $item['lvl'], ['class' => 'term-depth']) ?>
				</td>
				<td>
                    <p class="text text-muted"> <?php echo HTML::chars($item['description']); ?> </p>
				</td>
				<td class="action">
                    <?php echo HTML::anchor(Route::get('admin/term')->uri(['action' => 'edit', 'id' => $item['id']]), '<i class="fa far fa-edit"></i>', ['class' => 'btn btn-sm btn-default', 'title' => __('Edit Term')]); ?>
                    <?php echo HTML::anchor(Route::get('admin/term')->uri(['action' => 'delete', 'id' => $item['id']]), '<i class="fa fas fa-times"></i>', ['class' => 'btn btn-sm btn-default btn-danger', 'title' => __('Delete Term')]); ?>
				</td>
			</tr>
		<?php endforeach ?>
		</tbody>
	</table>

<?php echo Form::submit('term-list', __('Save'), ['class' => 'btn btn-success pull-right']); ?>
	<div class="clearfix"></div><br>
<?php echo Form::close(); ?>
