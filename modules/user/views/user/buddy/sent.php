<div id="content-container">
	<div class="row vcard">
	    <div class="list-group list-all panel panel-default">
		
		<div class="panel-heading">
			<div class="col-md-6">
				<h3 class="panel-title">
                    <?php echo __('Sent (%total)', ['%total' => $total]); ?>
				</h3>
			</div>
			<div class="col-md-6">
                <?php echo HTML::anchor("buddy/" . $id, __('Friends'), [
                    'class' => 'buddy btn btn-default pull-right',
                    'title' => __('View Friends list')
                ]) ?>
                <?php echo HTML::anchor("buddy/pending/" . $id, __('Pending'), [
                    'class' => 'buddy btn btn-default pull-right',
                    'title' => __('View Sent list')
                ]) ?>
			</div>
			<div class="clearfix"></div>
		</div>

            <?php foreach ($sentRequests as $sent): ?>
                <div class="list-group-item friend-item panel-body">
				<?php $accept = User::lookup($sent['request_to']); ?>
				<?php if($accept): ?>
					<div class="col-md-2">
                        <?php echo HTML::anchor("user/view/" . $accept->id, User::getAvatar($accept, ['size' => 80]), [
                            'class' => 'action-view',
                            'title' => __('view profile')
                        ]) ?>
					</div>
					<div class="col-md-5">
                        <?php echo HTML::anchor("user/view/" . $accept->id, $accept->nick, [
                            'class' => 'action-view',
                            'title' => __('view profile')
                        ]) ?>
                        <br>
                        <?php echo HTML::anchor("#", $accept->mail, ['title' => __('mail')]) ?>
                        <br>
                        <?php echo ($accept->dob != 0) ? $accept->dob : '__'; ?>
                        <br>
                        <?php echo HTML::anchor("$accept->homepage", $accept->homepage) ?>
					</div>
				<?php endif; ?>

			</div>
		<?php endforeach ;?>
	    </div>
	    
	</div>
</div>
<?php echo $pagination; ?>