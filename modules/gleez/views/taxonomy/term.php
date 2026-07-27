<?php foreach($posts as $i => $post) : ?>

	<div id="post-<?php echo $i; ?>" class="post-list<?php if ($post->sticky) { echo ' sticky'; } ?> <?php echo ' post-'. $post->status; ?>">
                
		<h2 class="post-title"><?php echo HTML::anchor($post->url, $post->title) ?></h2>
		<?php //echo isset( $teaser ) ? $post->teaser : $post->content ?>
        <?php echo View::factory($post->type . '/post')->set('post', $post)->set('page_title', true)->set('teaser', true); ?>
	</div>

<?php endforeach; ?>

<?php echo $pagination ?>