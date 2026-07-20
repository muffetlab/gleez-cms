<?php
$parms = isset($post->id) ? ['id' => $post->id, 'action' => 'edit'] : ['action' => 'add', 'id' => $menu->id];
	$items = isset($post->id) ? $post->select_list('id', 'title', '--') : $menu->select_list('id', 'title', '--');

echo Form::open(Route::get('admin/menu/item')->uri($parms), ['id' => 'menu-form', 'class' => 'form form-horizontal well']); ?>

	<?php include Kohana::find_file('views', 'errors/partial'); ?>

<div class="form-group <?php echo isset($errors['title']) ? 'has-error': ''; ?>">
    <?php echo Form::label('title', __('Title'), ['class' => 'control-label col-md-3']) ?>
	<div class="controls col-md-6">
        <?php echo Form::input('title', $post->title, ['class' => 'form-control']); ?>
	</div>
</div>

<div class="form-group <?php echo isset($errors['url']) ? 'has-error': ''; ?>">
    <?php echo Form::label('url', __('Link'), ['class' => 'control-label col-md-3']); ?>
	<div class="controls col-md-6">
        <?php echo Form::input('url', $post->url, ['class' => 'form-control'], 'admin/autocomplete/links'); ?>
	</div>
</div>

<?php if( ! isset($post->id) ):?>
	<div class="form-group <?php echo isset($errors['parent']) ? 'has-error': ''; ?>">
        <?php echo Form::label('parent', __('Parent'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-6">
            <?php echo Form::select('parent', $items, $post->pid, ['class' => 'form-control']); ?>
		</div>
	</div>
<?php endif; ?>

	<div class="form-group <?php echo isset($errors['image']) ? 'has-error': ''; ?>">
        <?php echo Form::label('image', __('Icon'), ['class' => 'control-label col-md-3']); ?>
		<div class="controls col-md-6 sys-icon">
            <?php echo Form::select('image', System::icons(), $post->image, ['class' => 'select-icons col-md-12', 'useSelect2' => true]); ?>
		</div>
	</div>

<div class="form-group <?php echo isset($errors['descp']) ? 'has-error': ''; ?>">
    <?php echo Form::label('descp', __('Description'), ['class' => 'control-label col-md-3']); ?>
	<div class="controls col-md-6">
        <?php echo Form::textarea('descp', $post->descp ?? '', ['class' => 'form-control', 'rows' => 3]) ?>
	</div>
</div>

<?php echo Form::submit('menu-item', __('Save'), ['class' => 'btn btn-success pull-right']); ?>
<div class="clearfix"></div>
<?php echo Form::close() ?>
