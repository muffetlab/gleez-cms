<div class="col-sm-6 col-sm-offset-3">
	<?php include Kohana::find_file('views', 'errors/partial'); ?>

	<div class="panel panel-default window-shadow">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php _e('Change Password')?>
			</h3>
		</div>
        <?php echo Form::open($action, ['class' => 'form form-horizontal']) ?>
			<div class="panel-body">
				<div class="form-group <?php echo isset($errors['pass']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('pass', __('New password'), ['class' => 'col-sm-4 control-label']); ?>
					<div class="col-sm-8">
						<div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <?php echo Form::password('pass', NULL, ['class' => 'form-control']); ?>
						</div>
					</div>
				</div>

				<div class="form-group <?php echo isset($errors['pass_confirm']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('pass_confirm', __('New password (again)'), [
                        'class' => 'col-sm-4 control-label'
                    ]); ?>
					<div class="col-sm-8">
						<div class="input-group">
                            <span class="input-group-addon"><i class="fas fa-key"></i></span>
                            <?php echo Form::password('pass_confirm', NULL, ['class' => 'form-control']); ?>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer form-actions-right">
                <?php echo Form::button('password_confirm', __('Apply new password'), ['class' => 'btn btn-primary']) ?>
			</div>
		<?php echo Form::close(); ?>
	</div>
</div>
