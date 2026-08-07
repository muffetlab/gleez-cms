<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <h4><?php echo __('Upgrade completed with errors'); ?></h4>
        <p><?php echo __('The upgrade completed with the following errors. You may need to manually fix these.'); ?></p>
    </div>
    <ul class="list-group">
        <?php foreach ($errors as $error): ?>
            <li class="list-group-item list-group-item-danger"><?php echo HTML::chars($error); ?></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>

<?php if ($needsUpgrade): ?>
    <div class="alert alert-warning">
        <h4><?php echo __('Upgrade Required'); ?></h4>
        <p>
            <?php echo __('A database upgrade is required. Your current schema version is :current, the installed Gleez CMS requires version :target. Please back up your database before proceeding.', [
                ':current' => HTML::chars($currentVersion),
                ':target' => HTML::chars($targetVersion),
            ]); ?>
        </p>
    </div>

    <?php echo Form::open(Route::get('admin')->uri(['controller' => 'tool', 'action' => 'upgrade']), [
        'class' => 'form'
    ]); ?>
    <div class="form-group">
        <button type="submit" name="upgrade" value="upgrade" class="btn btn-primary btn-lg"
                onclick="return confirm('<?php echo __('Are you sure? Do not refresh the page during the process.'); ?>')">
            <i class="fas fa-arrow-up"></i> <?php echo __('Run Upgrade'); ?>
        </button>
    </div>
    <?php echo Form::close(); ?>

<?php else: ?>
    <div class="alert alert-success">
        <p><?php echo __('Your database is up to date (version :version). No upgrade is needed.', [
                ':version' => HTML::chars($targetVersion)
            ]); ?></p>
    </div>
<?php endif; ?>
