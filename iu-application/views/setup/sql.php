<h2>Database operations</h2>

<?php if (!$upgrade) : ?>

	<?php if (empty($errors)): ?>
		<p>Successfully created database tables.</p>
	<?php else : ?>
		<p>There were some errors when creating tables:</p>
		<?php foreach ($errors as $error) : ?>
		<p>Query #<?php echo $error['number']; ?>: (<?php echo $error['error_number']; ?>) <?php echo $error['message']; ?></p>
		<p><sub><?php echo $error['query']; ?></sub></p>
		<?php endforeach; ?>
	<?php endif; ?>


<?php else : ?>

	<?php foreach ($steps as $step) : ?>

		<?php if (empty($step['errors'])) : ?>
			<p>Upgrade to <?php echo $step['ver']; ?> successful.</p>
		<?php else : ?>
			<p>Upgrade to <?php echo $step['ver']; ?> failed:</p>
				<?php foreach ($step['errors'] as $error) : ?>
				<p>Query #<?php echo $error['number']; ?>: (<?php echo $error['error_number']; ?>) <?php echo $error['message']; ?></p>
				<p><sub><?php echo $error['query']; ?></sub></p>
				<?php endforeach; ?>
		<?php endif; ?>

	<?php endforeach; ?>

<?php endif; ?>
<p>&nbsp;</p>
<form action="<?php echo ($upgrade) ? site_url("setup/finish"): site_url("setup/createadmin") ; ?>" method="get">
<input class="submit" value="Proceed &raquo;" type="submit">
</form>
<p>&nbsp;</p>


<p>&nbsp;</p>
<p>&nbsp;</p>