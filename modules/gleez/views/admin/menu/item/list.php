<?php echo HTML::anchor(Route::get('admin/menu/item')->uri(['action' => 'add', 'id' => $id]), '<i class="fas fa-plus"></i>' . __('Add New Item'), ['title' => __('Add New Item'), 'class' => 'btn btn-success pull-right']); ?>
<div class='clearfix'></div><br/>

<?php echo Form::open(Route::get('admin/menu/item')->uri(['action' => 'confirm', 'id' => $id]), ['id' => 'menu-form', 'class' => 'form']); ?>

    <table id="admin-list-menu-items" class="table table-striped table-bordered table-highlight"
           data-toggle="tableDrag">
		<thead>
			<tr>
				<th><?php echo __('Name') ?></th>
				<th><?php echo __('Enabled') ?></th>
                <th class="table-drag-hide"><?php echo __('Weight') ?></th>
				<th><?php echo __('Actions') ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ($items as $item): ?>
			<tr id="item-row-<?php echo $item['id'] ?>" class="draggable">
				<td id="item-<?php echo $item['id'] ?>"  class="lid-<?php echo $item['lvl'] ?>">
					<?php
						$c = 2;
						while ($c < $item['lvl'])
						{
							echo '<div class="indentation">&nbsp;</div>';
							$c++;
						}
						echo HTML::chars($item['title'])
					?>
				</td>

				<td>
                    <?php echo Form::checkbox('mlid:' . $item['id'] . '[hidden]', TRUE, (bool) $item['active']); ?>
				</td>

                <td class="table-drag-hide">
                    <?php echo Form::weight('mlid:' . $item['id'] . '[weight]', 0, ['class' => 'row-weight']) ?>
                    <?php echo Form::hidden('mlid:' . $item['id'] . '[plid]', $item['pid'], ['class' => 'row-parent']) ?>
                    <?php echo Form::hidden('mlid:' . $item['id'] . '[mlid]', $item['id'], ['class' => 'row-id']) ?>
				</td>

				<td class="action">
                    <?php echo HTML::anchor(Route::get('admin/menu/item')->uri(['action' => 'edit', 'id' => $item['id']]), '<i class="fa far fa-edit"></i>', ['class' => 'btn btn-sm btn-default', 'title' => __('Edit Item')]) ?>
                    <?php echo HTML::anchor(Route::get('admin/menu/item')->uri(['action' => 'delete', 'id' => $item['id']]), '<i class="fa fas fa-trash-can"></i>', ['class' => 'btn btn-sm btn-default', 'title' => __('Delete Item')]) ?>
        </td>
			  </tr>
			<?php endforeach ?>
		</tbody>
	</table>
<?php echo Form::submit('menu-item-list', __('Save'), ['class' => 'btn btn-success pull-right']); ?>
    <div class="clearfix"></div><br>
<?php echo Form::close(); ?>