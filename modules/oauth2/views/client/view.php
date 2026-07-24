<table class="table table-striped table-bordered" >
	<tbody>
		<tr>
			<th>
				<?php echo __('Title'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->title); ?>
			</td>
		</tr>
                
		<tr>
			<th>
				<?php echo __('Logo'); ?>
			</th>
			<td>
                <?php echo HTML::resize("media/logos/" . $client->logo); ?>
			</td>
		</tr>
		
                <tr>
			<th>
				<?php echo __('Client Id'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->client_id); ?>
			</td>
		</tr>
                
                <tr>
			<th>
				<?php echo __('Client Secret'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->client_secret); ?>
			</td>
		</tr>
                
		<tr>
			<th>
				<?php echo __('Redirect URI'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->redirect_uri); ?>
			</td>
		</tr>
		
		<tr>
			<th>
				<?php echo __('Status'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->status); ?>
			</td>
		</tr>
		
		<tr>
			<th>
				<?php echo __('Description'); ?>
			</th>
			<td>
                <?php echo Text::markup($client->description); ?>
			</td>
		</tr>
		
		<tr>
			<th>
				<?php echo __('Created By'); ?>
			</th>
			<td>
                <?php echo HTML::chars($client->user->nick) ?>
			</td>
		</tr>
		
				
		<tr>
			<th>
				<?php echo __('Created On'); ?>
			</th>
			<td>
                <?php echo System::date('M d, Y', $client->created) ?>
			</td>
		</tr>

	</tbody>
</table>