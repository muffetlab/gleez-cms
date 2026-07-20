<?php echo Form::open($action, ['class' => 'form form-horizontal']); ?>
	<div class="container1">
			<div class="form-group <?php echo isset($errors['database']) ? 'error': ''; ?>">
                <?php echo Form::label('database', __('Database Name'), ['class' => 'col-sm-3 control-label']) ?>
				<div class="col-sm-6">
                    <?php echo Form::input('database', $form['database'], ['class' => 'form-control']); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['user']) ? 'error': ''; ?>">
                <?php echo Form::label('user', __('User'), ['class' => 'col-sm-3 control-label']) ?>
				<div class="col-sm-6">
                    <?php echo Form::input('user', $form['user'], ['class' => 'form-control']); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['pass']) ? 'error': ''; ?>">
                <?php echo Form::label('pass', __('Password'), ['class' => 'col-sm-3 control-label']) ?>
				<div class="col-sm-6">
                    <?php echo Form::password('pass', $form['pass'], ['class' => 'form-control']); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['hostname']) ? 'error': ''; ?>">
                <?php echo Form::label('hostname', __('Host'), ['class' => 'col-sm-3 control-label']) ?>
				<div class="col-sm-6">
                    <?php echo Form::input('hostname', $form['hostname'], ['class' => 'form-control']); ?>
				</div>
			</div>

			<div class="form-group <?php echo isset($errors['table_prefix']) ? 'error': ''; ?>">
                <?php echo Form::label('table_prefix', __('Table Prefix'), ['class' => 'col-sm-3 control-label']) ?>
				<div class="col-sm-6">
                    <?php echo Form::input('table_prefix', $form['table_prefix'], ['class' => 'form-control']); ?>
				</div>
			</div>
	</div>

<?php echo Form::submit('db', __('Next'), ['class' => 'btn btn-primary pull-right']); ?>
	<div class="clearfix"></div><br>
<?php echo Form::close() ?>
