<?php if (isset($items)): ?>
	<div class="recent-announce-blogs">
        <div class="recent-announce-wrapper" itemscope itemtype="https://schema.org/CreativeWork">
			<?php foreach($items as $item) : ?>
				<div class="announce-blog">
					<div class="image"><?php echo $item['image']; ?></div>
                    <h3 itemprop='url'><?php echo HTML::anchor($item['url'], $item['title'], ['itemprop' => 'url']) ?></h3>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif; ?>