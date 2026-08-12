<?php $action = Request::current()->action(); ?>
<ul class="nav nav-pills nav-stacked">
    <li<?php echo $action === 'edit' ? ' class="active"' : ''; ?>>
        <?php echo HTML::anchor(Route::get('user')->uri([
            'action' => 'edit'
        ]), '<i class="fas fa-fw fa-user"></i> ' . __('Profile Settings')); ?>
    </li>
    <li<?php echo $action === 'password' ? ' class="active"' : ''; ?>>
        <?php echo HTML::anchor(Route::get('user')->uri([
            'action' => 'password'
        ]), '<i class="fas fa-fw fa-lock"></i> ' . __('Change Password')); ?>
    </li>
    <?php if (!Kohana::$config->load('site')->get('use_gravatars', false)): ?>
        <li<?php echo $action === 'photo' ? ' class="active"' : ''; ?>>
            <?php echo HTML::anchor(Route::get('user')->uri([
                'action' => 'photo'
            ]), '<i class="fas fa-fw fa-upload"></i> ' . __('Change Avatar'), [
                'id' => 'add-pic1',
                'title' => __('Change your avatar')
            ]); ?>
        </li>
    <?php endif; ?>
</ul>
