</div>

<?php if ($template->config['has_footer']): ?>
    <!-- Footer line -->
<div id="footer">
<?php
$first = Setting::value('custom_footer_text', '');
$second = Setting::value('custom_footer_text2', '');

if (empty($first))
	$first = 'Content Management System provided by <a href="http://instant-update.com">Instant Update '.get_app_version().'</a>';

if (empty($second))
	$second = 'You can brand Instant Update as your own software (Settings > Branding).';
?>
	<div class="left"><?php echo $first; ?></div>
	<div class="right"><?php echo $second; ?></div>
</div>
<?php endif; ?>

<?php //PluginManager::do_actions('body.bottom'); ?>


</body>
</html>