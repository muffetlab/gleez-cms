<?php
echo Form::open($action, ['id' => 'page-form', 'class' => 'post-form form', 'enctype' => 'multipart/form-data']);

	include Kohana::find_file('views', 'errors/partial');
?>

	<div class="row">

		<div id="post-body" class="col-md-9">

			<div class="form-group <?php echo isset($errors['title']) ? 'has-error': ''; ?>">
                <?php echo Form::label('title', __('Title'), ['class' => 'control-label']) ?>
				<div class="controls">
                    <?php echo Form::input('title', $post->rawtitle, [
                        'id' => 'title',
                        'class' => 'form-control',
                        'placeholder' => __('Enter title here')
                    ]); ?>
				</div>
			</div>

            <?php if (ACL::check('administer content') || ACL::check('administer page')): ?>
				<div class="form-group <?php echo isset($errors['slug']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('path', __('Permalink'), ['class' => 'control-label']) ?>
					<div class="controls">
                        <div class="input-group">
                            <span class="input-group-addon"><?php echo HTML::chars($site_url) ?></span>
                            <?php echo Form::input('path', $path, ['id' => 'path', 'class' => 'form-control slug']); ?>
                        </div>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($config->use_tags) : ?>
                <div class="form-group <?php echo isset($errors['form_tags']) ? 'has-error' : ''; ?>">
                    <?php echo Form::label('form_tags', __('Tags'), ['class' => 'control-label']) ?>
					<div class="controls">
                        <?php echo Form::input('form_tags', $tags, [
                            'id' => 'form_tags',
                            'class' => 'form-control'
                        ], 'autocomplete/tag/page'); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($config->primary_image): ?>
				<div class="form-group <?php echo isset($errors['image']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('image', __('Primary Image'), ['class' => 'control-label']) ?>
					<div class="controls page-img">
                        <?php if (!empty($post->image)): ?>
                            <div class="thumbnail">
                                <?= HTML::resize($post->image, ['alt' => $post->title, 'width' => 144, 'height' => 144, 'type' => 'resize']) ?>
                            </div>
                        <?php endif; ?>
                        <?php echo Form::file('image', ['id' => 'image', 'class' => 'form-control']); ?>
					</div>
				</div>
			<?php endif; ?>

			<?php if ($config->use_excerpt): ?>
				<div class="form-group <?php echo isset($errors['teaser']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('excerpt', __('Excerpt'), ['class' => 'control-label']) ?>
					<div class="controls">
                        <?php echo Form::textarea('teaser', $post->rawteaser, ['class' => 'form-control', 'rows' => 3]) ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group <?php echo isset($errors['body']) ? 'has-error': ''; ?>">
                <?php echo Form::label('body', __('Content'), ['class' => 'control-label']) ?>
				<div class="controls">
                    <?php echo Form::textarea('body', $post->rawbody, [
                        'id' => 'body',
                        'class' => 'textarea form-control',
                        'autofocus',
                        'placeholder' => __('Enter text...')
                    ]) ?>
				</div>
			</div>

            <?php if (ACL::check('administer content') || ACL::check('administer page')): ?>
				<div class="form-group <?php echo isset($errors['format']) ? 'has-error': ''; ?>">
					<div class="controls">
						<div class="input-group">
							<span class="input-group-addon"><?php echo __('Text format') ?></span>
                            <?php echo Form::select('format', Filter::formats(), $post->format, [
                                'class' => 'form-control select-caret',
                                'aria-label' => __('Text format')
                            ]); ?>
						</div>
					</div>
				</div>
			<?php endif; ?>

            <?php if ($config->use_captcha && !$captcha->promoted()): ?>
				<div class="form-group <?php echo isset($errors['captcha']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('_captcha', __('Security'), ['class' => 'wrap']) ?>
                    <?php echo Form::input('_captcha', '', ['class' => 'form-control']); ?><br>
					<?php echo $captcha; ?>
				</div>
			<?php endif; ?>

		</div>

		<div id="side-info-column" class="col-md-3">
            <?php if (ACL::check('administer content') || ACL::check('administer page')): ?>
                <div class="panel panel-info">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __('Publication') ?></h3>
					</div>
                    <div class="panel-body">
						<div id="minor-publishing">

							<div class="form-group <?php echo isset($errors['status']) ? 'has-error': ''; ?>">
                                <?php echo Form::label('status', __('Status'), ['class' => 'control-label']) ?>
                                <?php echo Form::select('status', Post::status(), $post->status, [
                                    'id' => 'status',
                                    'class' => 'form-control select-caret'
                                ]); ?>
							</div>

							<div class="form-group <?php echo isset($errors['sticky']) ? 'has-error': ''; ?>">
								<?php
                                $sticky = isset($post->sticky) && $post->sticky == 1;
                                $promote = isset($post->promote) && $post->promote == 1;
									echo Form::hidden('sticky', 0);
									echo Form::hidden('promote', 0);
								?>
								<div class="controls checkbox">
                                    <?php echo Form::label('sticky', Form::checkbox('sticky', true, $sticky, ['id' => 'sticky']) . __('Sticky this Post')) ?>
								</div>
								<div class="controls checkbox">
                                    <?php echo Form::label('promote', Form::checkbox('promote', true, $promote, ['id' => 'promote']) . __('Promote this Post')) ?>
								</div>
							</div>

							<div class="form-group <?php echo isset($errors['author_date']) ? 'has-error': ''; ?>">
                                <?php echo Form::label('author_date', __('Date'), ['class' => 'control-label']) ?>
								<div class="controls">
                                    <?php echo Form::date('author_date', $created, [
                                        'id' => 'author_date',
                                        'class' => 'form-control'
                                    ]); ?>
								</div>
							</div>

							<?php if ($config->use_authors): ?>
								<div class="form-group <?php echo isset($errors['author_name']) ? 'has-error': ''; ?>">
                                    <?php echo Form::label('author_name', __('Author'), ['class' => 'control-label']) ?>
									<div class="controls">
                                        <?php echo Form::input('author_name', $author, [
                                            'id' => 'author_name',
                                            'class' => 'form-control',
                                            'data-items' => 10
                                        ], 'autocomplete/user'); ?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
					<div class="panel-footer">
						<div id="major-publishing-actions" class="row">
                            <?php if ($post->loaded() && ACL::post('delete', $post)): ?>
                                <?php echo HTML::anchor(
                                    $post->delete_url . URL::query($destination),
                                    '<i class="fas fa-trash-can"></i> ' . __('Move to Trash'),
                                    ['id' => 'delete-action', 'class' => 'btn btn-default']
                                ) ?>
							<?php endif; ?>

							<div id="publishing-action">
                                <?php echo Form::submit('page', __('Save'), ['class' => 'btn btn-success']) ?>
							</div>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if($config->use_category) : ?>
                <div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo __('Category'); ?></h3>
					</div>
					<div class="panel-body">
						<div class="form-group <?php echo isset($errors['categories']) ? 'has-error': ''; ?>">
                            <?php echo Form::select('categories[1]', $terms, $post->terms_form, ['class' => 'form-control']); ?>
						</div>
					</div>
				</div>

			<?php endif; ?>

			<?php if( $config->use_comment) : ?>
                <div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title"><?php echo  __('Comments'); ?></h3>
					</div>

					<div class="panel-body">
						<div class="form-group <?php echo isset($errors['comment']) ? 'has-error': ''; ?>">
							<?php
                            if (!isset($post->comment)) {
									$post->comment = $config->comment;
								}

                            $comment1 = isset($post->comment) && $post->comment == 0;
                            $comment2 = isset($post->comment) && $post->comment == 1;
                            $comment3 = isset($post->comment) && $post->comment == 2;
							?>

                            <fieldset class="comment-fieldset">
                                <legend class="control-label"><?php echo __('Discussion') ?></legend>
                                <div class="controls radio">
                                    <label><?php echo Form::radio('comment', 0, $comment1, ['id' => 'comment-0']) ?><?php echo __('Disabled') ?></label>
                                </div>

                                <div class="controls radio">
                                    <label><?php echo Form::radio('comment', 1, $comment2, ['id' => 'comment-1']) ?><?php echo __('Read only') ?></label>
                                </div>

                                <div class="controls radio">
                                    <label><?php echo Form::radio('comment', 2, $comment3, ['id' => 'comment-2']) ?><?php echo __('Read/Write') ?></label>
                                </div>
                            </fieldset>

						</div>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<div class="form-actions">
        <?php echo Form::submit('page', __('Save'), ['class' => 'btn btn-success']); ?>
	</div>

<?php echo Form::close() ?>
