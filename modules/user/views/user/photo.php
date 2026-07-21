<div class="row">

	<div class="col-md-3 col-sm-4">
		<?php include Kohana::find_file('views', 'user/edit_link'); ?>
	</div>

	<div class="col-md-9 col-sm-8">
		<?php include Kohana::find_file('views', 'errors/partial'); ?>
        <?php echo Form::open(Route::get('user')->uri(['id' => $user->id, 'action' => 'photo']), [
            'class' => 'form form-horizontal',
            'enctype' => 'multipart/form-data'
        ]); ?>
			<div class="stacked-content">
				<div class="tab-pane" id="photo-tab">
					<div class="panel panel-default window-shadow">
						<div class="panel-body">
							<div class="form-group <?php echo isset($errors['picture']) ? 'has-error': ''; ?>">
                                <?php echo Form::label('photo', __('Photo'), ['class' => 'col-sm-3 control-label']) ?>
								<div class="col-sm-9">
                                    <?php echo Form::file('picture', ['class' => 'form-control']); ?>
								</div>
							</div>

							<div class="form-group">
								<div class="col-sm-12">
									<blockquote>
										<small class="muted">
                                            <?php echo __('Your picture will be changed proportionally to the size of :w&times;:h', [
                                                ':w' => 210,
                                                ':h' => 210
                                            ]); ?>
										</small>
										<small class="muted">
                                            <?php echo __('Allowed image formats: :formats', [
                                                ':formats' => '<strong>' . implode('</strong>, <strong>', $allowed_types) . '</strong>'
                                            ]); ?>
										</small>
									</blockquote>
								</div>
							</div>
						</div>
						<div class="panel-footer">
							<div class="row">
								<div class="col-sm-6">
                                    <?php echo HTML::anchor(Route::get('user')->uri([
                                        'action' => 'profile'
                                    ]), '<i class="fas fa-arrow-left"></i> ' . __('Profile'), ['class' => 'btn']); ?>
								</div>
								<div class="col-sm-6 form-actions-right">
                                    <?php echo Form::button('user_edit', __('Upload'), [
                                        'class' => 'btn btn-success'
                                    ]); ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php echo Form::close(); ?>
	</div>
</div>
