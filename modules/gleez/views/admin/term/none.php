<h3><?php echo __('No Terms!'); ?></h3>

<?php echo HTML::anchor(Route::get('admin/term')->uri($params), '<i class="fas fa-plus"></i> ' . __('Add New Term'), array('title' => __('Add New Term'), 'class' => 'btn btn-danger pull-right')); ?>
<div class="clearfix"></div><br>

<p>
	<?php echo __('There are no Terms.') ?>
</p>
