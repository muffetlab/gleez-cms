<?php echo Form::open($action, ['id' => 'role-form', 'class' => 'role-form form form-horizontal well']); ?>

	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="form-group <?php echo isset($errors['name']) ? 'has-error': ''; ?>">
        <?php echo Form::label('name', __('Name'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-5">
            <?php echo Form::input('name', $post->name, ['class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['description']) ? 'has-error': ''; ?>">
        <?php echo Form::label('description', __('Description'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-5">
            <?php echo Form::input('description', $post->description, ['class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['special']) ? 'has-error': ''; ?>">
        <?php echo Form::label('special', __('Special Role'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-5">
            <?php echo Form::select('special', [0 => __('No'), 1 => __('Yes')], $post->special, ['class' => 'form-control']); ?>
		</div>
	</div>

<?php echo Form::submit('role', __('Save'), ['class' => 'btn btn-success pull-right']) ?>
	<div class="clearfix"></div><br>

<?php echo Form::close(); ?>