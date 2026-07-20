<div class="help">
	<?php _e('Add new category groups to your site, edit and reorganize existing categories.') ?>
</div>

<?php $params = isset($post->id) ? ['id' => $post->id, 'action' => 'edit'] : ['action' => 'add'];
echo Form::open(Route::get('admin/taxonomy')->uri($params), ['id' => 'vocab-form', 'class' => 'form form-horizontal well']) ?>

	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="form-group <?php echo isset($errors['name']) ? 'has-error': ''; ?>">
        <?php echo Form::label('name', __('Group Name'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::input('name', $post->rawname, ['class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['type']) ? 'has-error': ''; ?>">
        <?php echo Form::label('type', __('Type'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::select('type', Gleez::types(), $post->type, ['class' => 'form-control']); ?>
			<span class="help-block"><?php _e('For what type of content you intend to use categories from this group?') ?></span>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['description']) ? 'has-error': ''; ?>">
        <?php echo Form::label('description', __('Description'), ['class' => 'control-label col-md-3']) ?>
		<div class="controls col-md-5">
            <?php echo Form::textarea('description', $post->description ?? '', ['class' => 'form-control', 'rows' => 5]) ?>
			<span class="help-block"><?php _e('Description not visible by default, however some themes may show it. The main purpose of this description - to inform administrator about the group.') ?></span>
		</div>
	</div>

	<div class="form-group">
		<div class="col-md-12 clearfix">
            <?php echo Form::button('vocab', __('Save'), ['class' => 'btn btn-success pull-right']) ?>
		</div>
	</div>
<?php echo Form::close() ?>
