<div class="help">
	<?php echo __('Add new menus to your site, edit existing menus, and rename and reorganize menu links.'); ?>
</div>

<?php echo Form::open($action, ['id' => 'menu-form ', 'class' => 'menu-form form form-horizontal well']); ?>

	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="form-group <?php echo isset($errors['title']) ? 'has-error': ''; ?>">
        <?php echo Form::label('title', __('Title'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-6">
            <?php echo Form::input('title', $post->title, ['class' => 'form-control col-md-6']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['descp']) ? 'has-error': ''; ?>">
        <?php echo Form::label('description', __('Description'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-6">
            <?php echo Form::textarea('descp', $post->descp ?? '', ['class' => 'form-control col-md-6', 'rows' => 3]); ?>
		</div>
	</div>

<?php echo Form::submit('menu', __('Save'), ['class' => 'btn btn-success pull-right']); ?>
	<div class="clearfix"></div>

<?php echo Form::close() ?>
