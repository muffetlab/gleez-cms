<div class="help">
	<p><?php echo __('Add and edit your tags using the form below.'); ?></p>
</div>

<?php echo Form::open($action, ['id' => 'tag-form ', 'class' => 'tag-form form form-horizontal well']); ?>
	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="form-group <?php echo isset($errors['tag']) ? 'has-error': ''; ?>">
        <?php echo Form::label('name', __('Tag'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::input('name', $post->name, ['class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['type']) ? 'has-error': ''; ?>">
        <?php echo Form::label('type', __('Type'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::select('type', Gleez::types(), $post->type, ['class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['slug']) ? 'has-error': ''; ?>">
        <?php echo Form::label('path', __('Slug'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::input('path', $path, ['class' => 'form-control slug']); ?>
			<p class="help-block"><?php echo HTML::anchor($site_url.$path, $site_url.$path); ?></p>
		</div>
	</div>

<?php echo Form::submit('tag', __('Save'), ['class' => 'btn btn-success pull-right']); ?>
	<div class="clearfix"></div><br>

<?php echo Form::close() ?>
