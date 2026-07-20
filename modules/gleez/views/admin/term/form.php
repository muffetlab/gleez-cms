<?php echo Form::open($action, ['id' => 'term-form', 'class' => 'term-form form form-horizontal well clearfix']); ?>

<?php include Kohana::find_file('views', 'errors/partial'); ?>

<div class="form-group <?php echo isset($errors['name']) ? 'has-error': ''; ?>">
    <?php echo Form::label('name', __('Name'), ['class' => 'control-label col-md-3']); ?>
	<div class="controls col-md-5">
        <?php echo Form::input('name', $post->rawname, ['class' => 'form-control']); ?>
	</div>
</div>

<div class="form-group <?php echo isset($errors['parent']) ? 'has-error': ''; ?>">
    <?php echo Form::label('parent', __('Parent'), ['class' => 'control-label col-md-3']); ?>
	<div class="controls col-md-5">
        <?php echo Form::select('parent', $terms, $post->pid, ['class' => 'form-control']); ?>
	</div>
</div>

<div class="form-group <?php echo isset($errors['slug']) ? 'has-error': ''; ?>">
    <?php echo Form::label('path', __('Slug'), ['class' => 'nowrap control-label col-md-3']) ?>
	<div class="controls col-md-5">
        <?php echo Form::input('path', $path, ['class' => 'form-control slug']); ?>
        <span class="help-block"><?php echo __('Slug for %slug', ['%slug' => $site_url]); ?></span>
	</div>
</div>

<div class="form-group <?php echo isset($errors['image']) ? 'has-error': ''; ?>">
    <?php echo Form::label('image', __('Image'), ['class' => 'col-sm-3 control-label']) ?>
	<div class="col-sm-5">
		<div class="thumbnail">
            <?php echo HTML::resize($post->image, ['alt' => $post->name, 'width' => 144, 'height' => 144, 'type' => 'resize']); ?>
		</div>
        <?php echo Form::file('image', ['class' => 'form-control']); ?>
        <span class="help-block"><?php echo __('Allowed image formats: :formats', [':formats' => '<strong>' . implode('</strong>, <strong>', $allowed_types) . '</strong>']); ?></span>
	</div>
</div>

<div class="form-group <?php echo isset($errors['description']) ? 'has-error': ''; ?>">
    <?php echo Form::label('description', __('Description'), ['class' => 'control-label col-md-3']); ?>
	<div class="controls col-md-5">
        <?php echo Form::textarea('description', $post->description ?? '', ['class' => 'form-control', 'rows' => 5]) ?>
	</div>
</div>

<?php echo Form::submit('term', __('Save'), ['class' => 'btn btn-success pull-right']); ?>

<?php echo Form::close() ?>
