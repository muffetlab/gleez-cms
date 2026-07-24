<?php include Kohana::find_file('views', 'errors/partial');?>
<?php
if (isset($client->id) && Valid::digit($client->id))
    {
        $parms = ['id' => $client->id, 'action' => 'edit'];
		$btntxt    = __("Save Changes");
    }
    else
    {
        $parms = ['action' => 'register'];
		$btntxt    = __("Register");
    }
?>
<?php echo Form::open(Route::get('oauth2/client')->uri($parms), [
    'class' => 'form form-horizontal',
    'enctype' => 'multipart/form-data'
]) ?>
	<div class="row-fluid">
		<div class="form-group <?php echo isset($errors['title']) ? 'error' : ''; ?>">
            <?php echo Form::label('title', __('Title'), ['class' => 'control-label1']) ?>
			<div class="controls ">
                <?php echo Form::input('title', $client->title, ['class' => 'col-sm-5']); ?>
			</div>
		</div>
		
		<div class="form-group <?php echo isset($errors['redirect_uri']) ? 'error' : ''; ?>">
            <?php echo Form::label('redirect_uri', __('Redirect URL'), ['class' => 'control-label1']) ?>
			<div class="controls ">
                <?php echo Form::input('redirect_uri', $client->redirect_uri, ['class' => 'col-sm-5']); ?>
			</div>
		</div>
		
		<div class="form-group <?php echo isset($errors['description']) ? 'error' : ''; ?>">
            <?php echo Form::label('description', __('Description'), ['class' => 'control-label1']) ?>
			<div class="controls ">
                <?php echo Form::textarea('description', $client->description, ['class' => 'col-sm-5', 'rows' => 3]); ?>
			</div>
		</div>
		
		<?php if(User::is_admin()): ?>
			<div class="form-group <?php //echo isset($errors['grant_types']) ? 'error' : ''; ?>">
                <?php echo Form::label('grant_types', __('Grant Types'), ['class' => 'control-label1']) ?>
				<div class="controls ">
                    <?php $selected = explode(" ", $client->grant_types); ?>
					<?php foreach ($grant_types as $k => $v) : ?>
					<label for="grant_types[<?php echo $k?>]" class=" checkbox">
						<input type="checkbox" <?php echo in_array($k, $selected) ? "checked='checked'" : "";?> value="<?php echo $k?>" name="grant_types[<?php echo $k?>]" id="form-grant_types_<?php echo $k?>">
						<?php echo $v ?>
					</label>
					<?php endforeach; ?>
				</div>
			</div>
		<?php endif;?>
		<div class="form-group <?php echo isset($errors['logo']) ? 'error': ''; ?>">
            <?php echo Form::label('logo', __('Logo'), ['class' => 'control-label1']) ?>
			<div class="controls">
                <?php echo Form::file('logo', ['class' => 'span12', 'title' => 'Upload']); ?>
			</div>
		</div>
        <?php if ($client->logo): ?>
			<div class="thumbnail">
                <?php echo HTML::resize("media/logos/" . $client->logo); ?>
			</div>
		<?php endif; ?>
		
		<div class="form-group">
		    <div class="form-actions-left">
                <?php echo Form::submit('save', $btntxt, ['class' => 'btn btn-success']); ?>
                <?php echo Form::submit('cancel', __('Cancel'), ['class' => 'btn btn-default']); ?>
		    </div>
		</div>
	</div>
<?php echo Form::close(); ?>