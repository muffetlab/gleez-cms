<?php include Kohana::find_file('views', 'errors/partial'); ?>

<div class="col-sm-6">
	<div class="panel panel-default window-shadow">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php _e('Fill in the information below to register') ?>
			</h3>
		</div>
        <?php echo Form::open($action, ['class' => 'form-horizontal', 'role' => 'form']); ?>
		<div class="panel-body">
			<fieldset>
				<?php if ($config->username): ?>

					<div class="form-group <?php echo isset($errors['name']) ? 'has-error': ''; ?>">
                        <?php echo Form::label('name', __('Username'), ['class' => 'col-sm-3 control-label']); ?>
						<div class="col-xs-12 col-sm-8">
                            <?php echo Form::input('name', $post->name, [
                                'id' => 'name',
                                'class' => 'form-control',
                                'type' => "text",
                                'title' => __('Username for login')
                            ]); ?>
						</div>
					</div>
				<?php endif ?>

				<div class="form-group <?php echo isset($errors['mail']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('mail', __('E-mail'), ['class' => 'col-sm-3 control-label']); ?>
					<div class="col-xs-12 col-sm-8">
                        <?php echo Form::input('mail', $post->mail, [
                            'id' => 'mail',
                            'class' => 'form-control',
                            'rel' => 'tooltip',
                            'data-placement' => 'right',
                            'title' => __('Will be private')
                        ]); ?>
					</div>
				</div>

				<div class="form-group <?php echo isset($errors['pass']) ? 'has-error': ''; ?>">
                    <?php echo Form::label('pass', __('Password'), ['class' => 'col-sm-3 control-label']); ?>
					<div class="col-xs-12 col-sm-8">
                        <?php echo Form::password('pass', null, [
                            'id' => 'pass',
                            'class' => 'form-control',
                            'rel' => 'tooltip',
                            'data-placement' => 'right',
                            'title' => __('Try to come up with a complex password')
                        ]); ?>
					</div>
				</div>

				<?php if ($config->confirm_pass): ?>
					<div class="form-group <?php echo isset($errors['pass_confirm']) ? 'has-error': ''; ?>">
                        <?php echo Form::label('pass_confirm', __('Confirm Password'), [
                            'class' => 'col-sm-3 control-label'
                        ]); ?>
						<div class="col-xs-12 col-sm-8">
                            <?php echo Form::password('pass_confirm', null, [
                                'id' => 'pass_confirm',
                                'class' => 'form-control',
                                'rel' => 'tooltip',
                                'data-placement' => 'right',
                                'title' => __('Repeat entered password')
                            ]); ?>
						</div>
					</div>
				<?php endif; ?>

				<?php if ($config->use_nick): ?>
					<div class="form-group <?php echo isset($errors['nick']) ? 'has-error': ''; ?>">
                        <?php echo Form::label('nick', __('Display Name'), ['class' => 'col-sm-3 control-label']); ?>
						<div class="col-xs-12 col-sm-8">
                            <?php echo Form::input('nick', $post->nick, [
                                'id' => 'nick',
                                'class' => 'form-control',
                                'rel' => 'tooltip',
                                'data-placement' => 'right',
                                'title' => __('Will be public')
                            ]); ?>
						</div>
					</div>
				<?php endif ?>

				<div class="form-group <?php echo isset($errors['gender']) ? 'has-error': ''; ?>">
                    <span id="gender-label" class="col-sm-3 control-label"><?php echo __('Gender') ?></span>
                    <div class="col-xs-12 col-sm-8" role="radiogroup" aria-labelledby="gender-label">
						<div class="radio">
                            <?php echo Form::label('gender1', Form::radio('gender', 1, $male, ['id' => 'gender1']) . __('Male')); ?>
						</div>
						<div class="radio">
                            <?php echo Form::label('gender2', Form::radio('gender', 2, $female, ['id' => 'gender2']) . __('Female')); ?>
						</div>
					</div>
				</div>

				<div class="form-group <?php echo isset($errors['dob']) ? 'has-error': ''; ?>">
                    <span id="dob-label" class="col-sm-3 control-label"><?php echo __('Birthday') ?></span>
                    <div class="col-xs-12 col-sm-9 dob-fields">
                        <div>
                            <?php echo Form::label('birth-month', __('Month'), [
                                'id' => 'birth-month-label',
                                'class' => 'sr-only'
                            ]); ?>
                            <?php echo Form::select('month', Date::months(Date::MONTHS_SHORT), '', [
                                'id' => 'birth-month',
                                'class' => 'form-control select-caret',
                                'aria-labelledby' => 'dob-label birth-month-label'
                            ]); ?>
                        </div>
                        <div>
                            <?php echo Form::label('birth-days', __('Day'), [
                                'id' => 'birth-days-label',
                                'class' => 'sr-only'
                            ]); ?>
                            <?php echo Form::select('days', Date::days(Date::DAY), '', [
                                'id' => 'birth-days',
                                'class' => 'form-control select-caret',
                                'aria-labelledby' => 'dob-label birth-days-label'
                            ]); ?>
                        </div>
                        <div>
                            <?php echo Form::label('birth-years', __('Year'), [
                                'id' => 'birth-years-label',
                                'class' => 'sr-only'
                            ]); ?>
                            <?php echo Form::select(
                                'years',
                                Date::years(date('Y') - 95, date('Y') - 5),
                                date('Y') - 5,
                                [
                                    'id' => 'birth-years',
                                    'class' => 'form-control select-caret',
                                    'aria-labelledby' => 'dob-label birth-years-label'
                                ]
                            ); ?>
                        </div>
					</div>
				</div>

                <?php if ($config->use_captcha && !$captcha->promoted()): ?>
					<div class="form-group captcha <?php echo isset($errors['captcha']) ? 'has-error': ''; ?>">
                        <?php echo Form::label('_captcha', __('Security code'), [
                            'class' => 'col-sm-3 control-label'
                        ]); ?>
						<div class="col-sm-4">
                            <?php echo Form::input('_captcha', '', [
                                'id' => '_captcha',
                                'class' => 'form-control input-md'
                            ]); ?>
							<br><span class="captcha-image"><?php echo $captcha; ?></span>
						</div>
						<div class="clearfix"></div><br>
					</div>
				<?php endif; ?>
			</fieldset>
		</div>
		<div class="panel-footer form-actions-right">
            <?php echo Form::button('register', __('Register new account'), [
                'class' => 'btn btn-success',
                'tabindex' => 11
            ]) ?>
		</div>
		<?php echo Form::close(); ?>
	</div>
</div>

<div class="col-sm-6">
	<div class="panel panel-default window-shadow">
		<div class="panel-heading">
			<h3 class="panel-title">
				<?php _e('Already have an account? Choose how you would like to sign in') ?>
			</h3>
		</div>
		<div class="panel-body">
			<div class="form-group oauth-buttons">
				<?php
					_e('You can sign in from any of the following services:');

					$providers = Auth_ORM::providers();

                echo HTML::anchor(Route::get('user')->uri([
                    'action' => 'login'
                ]), '<i class="fas fa-home"></i> ' . $site_name, [
                    'class' => 'btn btn-default',
                    'title' => __('Login with :provider', [':provider' => $site_name]),
                    'rel' => 'tooltip',
                    'data-placement' => 'right'
                ]);

                foreach ($providers as $name => $provider) {
                    echo HTML::anchor($provider['url'], '<i class="fab fa-' . $provider['icon'] . '"></i>' . ucfirst($name), [
                        'class' => 'btn btn-default',
                        'title' => __('Login with :provider', [':provider' => $name]),
                        'rel' => 'tooltip',
                        'data-placement' => 'right'
                    ]);
                }
				?>
			</div>
            <p class="help-sign-up">
				<?php _e("If you don't use any of these services, you can create an account.") ?>
				<?php _e('Fast, safe & secure way!') ?>
			</p>
		</div>
	</div>
</div>
