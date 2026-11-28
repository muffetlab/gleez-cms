<div class="help">
	<p><?php echo __('Page specific settings, default status, tags, comments etc.'); ?></p>
</div>

<?php include Kohana::find_file('views', 'errors/partial'); ?>

<?php echo Form::open($action, ['class' => 'page-settings-form form form-horizontal']) ?>

	<div class="form-group <?php echo isset($errors['items_per_page']) ? 'has-error': ''; ?>">
        <?php echo Form::label('items_per_page', __('Page entries per page'), ['class' => 'control-label col-sm-3']) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('items_per_page', HTML::per_page(), $config['items_per_page'] ?? null, ['id' => 'items_per_page', 'class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['default_status']) ? 'has-error': ''; ?>">
        <?php echo Form::label('default_status', __('Default Page Status'), ['class' => 'control-label col-sm-3']) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('default_status', Post::status(), $config['default_status'] ?? null, ['id' => 'default_status', 'class' => 'form-control']); ?>
		</div>
	</div>

	<div class="form-group">
		<?php
			// @important the hidden filed should be before checkbox
			echo Form::hidden('use_excerpt',       0);
			echo Form::hidden('use_comment',       0);
			echo Form::hidden('use_authors',       0);
			echo Form::hidden('use_captcha',       0);
			echo Form::hidden('use_category',      0);
			echo Form::hidden('use_tags',          0);
			echo Form::hidden('use_submitted',     0);
			echo Form::hidden('use_cache',         0);
			echo Form::hidden('comment_anonymous', 0);
		?>
		<div class="controls set-check">
			<div class="checkbox">
                <?php echo Form::label('use_excerpt', Form::checkbox('use_excerpt', true, $config['use_excerpt'] ?? false, ['id' => 'use_excerpt']) . __('Enable excerpt')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_comment', Form::checkbox('use_comment', true, $config['use_comment'] ?? false, ['id' => 'use_comment']) . __('Enable comments')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_authors', Form::checkbox('use_authors', true, $config['use_authors'] ?? false, ['id' => 'use_authors']) . __('Enable authors')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_captcha', Form::checkbox('use_captcha', true, $config['use_captcha'] ?? false, ['id' => 'use_captcha']) . __('Enable captcha')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_category', Form::checkbox('use_category', true, $config['use_category'] ?? false, ['id' => 'use_category']) . __('Enable Category')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_tags', Form::checkbox('use_tags', true, $config['use_tags'] ?? false, ['id' => 'use_tags']) . __('Enable tag cloud')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_submitted', Form::checkbox('use_submitted', true, $config['use_submitted'] ?? false, ['id' => 'use_submitted']) . __('Show Submitted Info')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_cache', Form::checkbox('use_cache', true, $config['use_cache'] ?? false, ['id' => 'use_cache']) . __('Enable Page Cache')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('comment_anonymous', Form::checkbox('comment_anonymous', true, $config['comment_anonymous'] ?? false, ['id' => 'comment_anonymous']) . __('Allow anonymous commenting (with contact information)')); ?>
			</div>
		</div>
	</div>

	<hr>

	<div class="form-group <?php echo isset($errors['comment']) ? 'has-error': ''; ?>">
        <span id="comment-label" class="control-label col-sm-3"><?php echo __('Allow people to post comments') ?></span>
        <div class="controls col-sm-4" role="radiogroup" aria-labelledby="comment-label">
			<div class="radio">
                <?php echo Form::label('comment-0', Form::radio('comment', 0, ($config['comment'] ?? null) === 0, ['id' => 'comment-0']) . __('Disabled')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment-1', Form::radio('comment', 1, ($config['comment'] ?? null) === 1, ['id' => 'comment-1']) . __('Read only')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment-2', Form::radio('comment', 2, ($config['comment'] ?? null) === 2, ['id' => 'comment-2']) . __('Read/Write')) ?>
			</div>
			<p class="help-block"><?php echo __('These settings may be overridden for individual posts.') ?></p>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comment_default_mode']) ? 'has-error': ''; ?>">
        <span id="comment-default-mode-label"
              class="control-label col-sm-3"><?php echo __('Comment display mode') ?></span>
        <div class="controls col-sm-4" role="radiogroup" aria-labelledby="comment-default-mode-label">
			<div class="radio">
                <?php echo Form::label('comment-default-mode-1', Form::radio('comment_default_mode', 1, ($config['comment_default_mode'] ?? null) === 1, ['id' => 'comment-default-mode-1']) . __('Flat list &mdash; collapsed')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment-default-mode-2', Form::radio('comment_default_mode', 2, ($config['comment_default_mode'] ?? null) === 2, ['id' => 'comment-default-mode-2']) . __('Flat list &mdash; expanded')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment-default-mode-3', Form::radio('comment_default_mode', 3, ($config['comment_default_mode'] ?? null) === 3, ['id' => 'comment-default-mode-3']) . __('Threaded list &mdash; collapsed')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment-default-mode-4', Form::radio('comment_default_mode', 4, ($config['comment_default_mode'] ?? null) === 4, ['id' => 'comment-default-mode-4']) . __('Threaded list &mdash; expanded')) ?>
			</div>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comment_order']) ? 'has-error': ''; ?>">
        <?php echo Form::label('comment_order', __('Comment Order'), ['class' => 'control-label col-sm-3']) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('comment_order', ['asc' => __('Older'), 'desc' => __('Newer')], $config['comment_order'] ?? 'asc', ['id' => 'comment_order', 'class' => 'form-control']); ?>
			<p class="help-block"><?php echo __('Comments should be displayed with the older/new comments at the top of each page'); ?></p>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comments_per_page']) ? 'has-error': ''; ?>">
        <?php echo Form::label('comments_per_page', __('Comments per page'), ['class' => 'control-label col-sm-3']) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('comments_per_page', HTML::per_page(), $config['comments_per_page'] ?? 50, ['id' => 'comments_per_page', 'class' => 'form-control']); ?>
		</div>
	</div>

<?php echo Form::submit('page_settings', __('Save'), ['class' => 'btn btn-success pull-right']) ?>
	<div class="clearfix"></div><br>
<?php echo Form::close() ?>
