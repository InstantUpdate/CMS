<?php if ($template->config['has_header']): ?>
<!-- Title area -->
<div class="titleArea">
	<div class="wrapper">
		<div class="pageTitle">
			<h5><?php echo $heading; ?></h5>
			<span><?php echo $tagline; ?></span>
		</div>
		<div class="subnavtitle">
			<?php foreach ($buttons as $button): ?>
				<a href="<?php echo $button['href']; ?>" <?php echo $button['attributes']; ?> class="button <?php echo $button['color']; ?>B" style="margin: 5px;"><img src="<?php echo $button['icon']; ?>" alt="" class="icon" /><span><?php echo $button['title']; ?></span></a>
			<?php endforeach; ?>
		</div>
		<div class="clear"></div>
	</div>
</div>

<div class="line"></div>
<?php endif; ?>

<div class="wrapper">
	<?php $template->load_template('notifications'); ?>
	<p>&nbsp;</p>
</div>

<?php echo $html; ?>
