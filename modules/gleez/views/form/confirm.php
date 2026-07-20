<?php echo Form::open($action, ['id' => 'delete-form ', 'class' => 'form']); ?>

    <p><?php echo __('Are you sure you want to :del %title?', [':del' => '<strong>' . __('delete') . '</strong>', '%title' => $title]) ?></p>
	<p><?php echo __('This action cannot be undone.'); ?></p>

	<div class="clearfix"></div>
<?php echo Form::submit('no', __('Cancel'), ['class' => 'btn btn-default']) ?> &nbsp;
<?php echo Form::submit('yes', __('Delete'), ['class' => 'btn btn-danger']) ?>

<?php echo Form::close() ?>