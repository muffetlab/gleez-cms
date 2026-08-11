<?php if (!empty($needsUpgrade)): ?>
    <div class="alert alert-danger">
        <h4><i class="fas fa-exclamation-triangle"></i> <?php echo __('Database Upgrade Required'); ?></h4>
        <p>
            <?php echo __('Your database schema is outdated. Please run the :link.', [
                ':link' => HTML::anchor(Route::get('admin')->uri(['controller' => 'tool', 'action' => 'upgrade']), __('database upgrade')),
            ]); ?>
        </p>
    </div>
<?php endif; ?>

<div class="help">
    <p><?php echo __('Welcome to the administration section. Here you may control how your site functions.'); ?></p>
</div>

<div id="dashboard-widgets-wrap" class="row">
    <?php echo $widgets; ?>
</div>