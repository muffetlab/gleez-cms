<div id="content-container">
	<div class="row vcard">
	    <div class="list-group list-all panel panel-default">

		<div class="panel-heading">
			<div class="col-md-6">
				<h3 class="panel-title buddy_sent">
                    <?php echo __('Friends (%total)', ['%total' => $total]); ?>
				</h3>
			</div>
			<div class="col-md-6">
                <?php echo HTML::anchor("buddy/sent/" . $id, __('Sent'), [
                    'class' => 'buddy btn btn-default pull-right',
                    'title' => __('View sent items')
                ]); ?>
                <?php echo HTML::anchor("buddy/pending/" . $id, __('Pending'), [
                    'class' => 'buddy btn btn-default pull-right',
                    'title' => __('View pending items')
                ]); ?>
			</div>
			<div class="clearfix"></div>
		</div>

		<?php foreach($friends as $id): ?>
            <div class="list-group-item friend-item panel-body">
				<?php $accept = User::lookup($id); ?>
				<div class="col-md-5">
                    <?php echo HTML::anchor("user/view/" . $accept->id, User::getAvatar($accept, ['size' => 80]), [
                        'class' => 'action-view',
                        'title' => __('View profile')
                    ]); ?>
				</div>
				<div class="col-md-6">
                    <?php echo HTML::anchor("user/view/" . $accept->id, $accept->nick, [
                        'class' => 'action-view',
                        'title' => __('View profile')
                    ]); ?>
                    <br>
                    <?php echo HTML::anchor("#", $accept->mail, ['title' => __('mail')]); ?>
                    <br>
                    <?php echo ($accept->dob != 0) ? $accept->dob : '__'; ?>
                    <br>
                    <?php echo HTML::anchor("$accept->homepage", $accept->homepage); ?>
				</div>
				
				<?php if($is_owner): ?>
                    <?php echo HTML::anchor("buddy/delete/" . $accept->id, '<i class="fas fa-trash-can"></i>', [
                        'class' => 'action-delete col-md-1',
                        'title' => __('Delete')
                    ]); ?>
				<?php endif ;?>
			</div>
		<?php endforeach ;?>
	    </div>
	    
	</div>
</div>
<?php echo $pagination; ?>