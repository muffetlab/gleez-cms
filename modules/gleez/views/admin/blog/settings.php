<div class="help">
	<p><?php echo __('Blog specific settings, default status, tags, comments etc.'); ?></p>
</div>

<?php include Kohana::find_file('views', 'errors/partial'); ?>

<?php echo Form::open($action, array('class'=>'blog-settings-form form form-horizontal')); ?>

	<div class="form-group <?php echo isset($errors['items_per_page']) ? 'has-error': ''; ?>">
		<?php echo Form::label('title', __('Blog entries per page'), array('class' => 'control-label col-sm-3')) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('items_per_page', HTML::per_page(), $config['items_per_page'] ?? null, array('class' => 'form-control')); ?>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['default_status']) ? 'has-error': ''; ?>">
		<?php echo Form::label('default_status', __('Default Blog Status'), array('class' => 'control-label col-sm-3')) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('default_status', Post::status(), $config['default_status'] ?? null, array('class' => 'form-control')); ?>
		</div>
	</div>

	<div class="form-group">
		<?php
			// @important the hidden filed should be before checkbox
			echo Form::hidden('use_excerpt',   0 );
			echo Form::hidden('use_comment',       0);
			echo Form::hidden('use_authors',       0);
			echo Form::hidden('use_captcha',       0);
			echo Form::hidden('use_category',      0);
			echo Form::hidden('use_tags',          0);
			echo Form::hidden('use_submitted',     0);
			echo Form::hidden('use_cache',         0);
			echo Form::hidden('primary_image',     0);
			echo Form::hidden('comment_anonymous', 0);
		?>
		<div class="controls set-check">
			<div class="checkbox">
                <?php echo Form::label('use_excerpt', Form::checkbox('use_excerpt', TRUE, $config['use_excerpt'] ?? false) . __('Enable excerpt')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_comment', Form::checkbox('use_comment', TRUE, $config['use_comment'] ?? false) . __('Enable comments')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_authors', Form::checkbox('use_authors', TRUE, $config['use_authors'] ?? false) . __('Enable authors')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_captcha', Form::checkbox('use_captcha', TRUE, $config['use_captcha'] ?? false) . __('Enable captcha')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_category', Form::checkbox('use_category', TRUE, $config['use_category'] ?? false) . __('Enable Category')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_tags', Form::checkbox('use_tags', TRUE, $config['use_tags'] ?? false) . __('Enable tag cloud')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_submitted', Form::checkbox('use_submitted', TRUE, $config['use_submitted'] ?? false) . __('Show Submitted Info')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('use_cache', Form::checkbox('use_cache', TRUE, $config['use_cache'] ?? false) . __('Enable Blog Cache')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('primary_image', Form::checkbox('primary_image', TRUE, $config['primary_image'] ?? false) . __('Use Primary Image')); ?>
			</div>
			
			<div class="checkbox">
                <?php echo Form::label('comment_anonymous', Form::checkbox('comment_anonymous', TRUE, $config['comment_anonymous'] ?? false) . __('Allow anonymous commenting (with contact information)')); ?>
			</div>
		</div>
	</div>

	<hr>

	<div class="form-group <?php echo isset($errors['comment']) ? 'has-error': ''; ?>">
		<?php echo Form::label('comment', __('Allow people to post comments'), array('class' => 'control-label col-sm-3')); ?>
		<div class="controls col-sm-4">
			<div class="radio">
                <?php echo Form::label('comment', Form::radio('comment', 0, ($config['comment'] ?? null) === 0) . __('Disabled')); ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment', Form::radio('comment', 1, ($config['comment'] ?? null) === 1) . __('Read only')); ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment', Form::radio('comment', 2, ($config['comment'] ?? null) === 2) . __('Read/Write')); ?>
			</div>
			<p class="help-block"><?php echo __('These settings may be overridden for individual posts.'); ?></p>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comment_default_mode']) ? 'has-error': ''; ?>">
		<?php echo Form::label('comment_default_mode', __('Comment display mode'), array('class' => 'control-label col-sm-3')) ?>
		<div class="controls col-sm-4">
			<div class="radio">
                <?php echo Form::label('comment_default_mode', Form::radio('comment_default_mode', 1, ($config['comment_default_mode'] ?? null) === 1) . __('Flat list &mdash; collapsed')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment_default_mode', Form::radio('comment_default_mode', 2, ($config['comment_default_mode'] ?? null) === 2) . __('Flat list &mdash; expanded')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment_default_mode', Form::radio('comment_default_mode', 3, ($config['comment_default_mode'] ?? null) === 3) . __('Threaded list &mdash; collapsed')) ?>
			</div>
			
			<div class="radio">
                <?php echo Form::label('comment_default_mode', Form::radio('comment_default_mode', 4, ($config['comment_default_mode'] ?? null) === 4) . __('Threaded list &mdash; expanded')) ?>
			</div>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comment_order']) ? 'has-error': ''; ?>">
		<?php echo Form::label('comment_order', __('Comment Order'), array('class' => 'control-label col-sm-3')) ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('comment_order', array('asc' => __('Older'), 'desc' => __('Newer')), $config['comment_order'] ?? 'asc', array('class' => 'form-control')); ?>
			<p class="help-block"><?php echo __('Comments should be displayed with the older/new comments at the top of each blog'); ?></p>
		</div>
	</div>

	<div class="form-group <?php echo isset($errors['comments_per_page']) ? 'has-error': ''; ?>">
		<?php echo Form::label('comments_per_page', __('Comments per page'), array('class' => 'control-label col-sm-3')); ?>
		<div class="controls col-sm-4">
            <?php echo Form::select('comments_per_page', HTML::per_page(), $config['comments_per_page'] ?? 50, array('class' => 'form-control')); ?>
		</div>
	</div>

	<?php echo Form::submit('blog_settings', __('Save'), array('class' => 'btn btn-success pull-right')); ?>
	<div class="clearfix"></div><br>
<?php echo Form::close() ?>