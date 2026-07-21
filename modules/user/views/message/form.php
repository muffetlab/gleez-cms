<?php echo Form::open($action, ['class' => 'form']); ?>

	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="row">
		<div class="col-md-12">
			<div class="form-group <?php echo isset($errors['recipient']) ? 'has-error': ''; ?>">
				<div class="controls">
                    <?php echo Form::input('recipient', $recipient, [
                        'class' => 'form-control',
                        'placeholder' => __('Enter recipient here')
                    ]); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['subject']) ? 'has-error': ''; ?>">
				<div class="controls">
                    <?php echo Form::input('subject', $message->rawsubject, [
                        'class' => 'form-control',
                        'placeholder' => __('Enter subject here')
                    ]); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['body']) ? 'has-error': ''; ?>">
				<div class="controls">
                    <?php echo Form::textarea('body', $message->rawbody, [
                        'class' => 'textarea form-control',
                        'autofocus',
                        'placeholder' => __('Enter text...')
                    ]) ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['format']) ? 'has-error': ''; ?>">
				<div class="controls">
					<div class="input-group">
						<span class="input-group-addon"><?php _e('Text format') ?></span>
                        <?php echo Form::select('format', Filter::formats(), $message->format, [
                            'class' => 'form-control'
                        ]); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-12">
			<div class="form-group <?php echo isset($errors['draft']) ? 'has-error': ''; ?>">
				<?php
                $draft = isset($message->status) && $message->status == PM::STATUS_DRAFT;
					echo Form::hidden('draft', 0);
				?>
				<div class="controls checkbox">
					<?php echo Form::label('draft', Form::checkbox('draft', TRUE, $draft).__("Don't send, save as draft")) ?>
				</div>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="col-sm-6">
            <?php echo HTML::anchor(Route::get('user/message')->uri([
                'action' => 'inbox'
            ]), '<i class="fas fa-arrow-left"></i> ' . __('Back to Inbox'), ['class' => 'btn']); ?>
		</div>
		<div class="col-sm-6 form-actions-right">
            <?php echo Form::button('message', __('Send Message'), ['class' => 'btn btn-success']); ?>
		</div>
	</div>

<?php echo Form::close() ?>
