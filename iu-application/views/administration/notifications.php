<?php if (count($template->messages) > 0) : ?>
	<?php foreach ($template->messages as $message) : ?>
<div class="nNote n<?php echo ucfirst($message['type']); ?> hideit">
	<p><strong><?php echo strtoupper($message['title']); ?>:</strong> <?php echo __($message['text']); ?></p>
</div>
	<?php endforeach; ?>
<?php endif; ?>