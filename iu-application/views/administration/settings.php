<script type="text/javascript">
$(window).load(function() {
	$('.chzn-search').each(function() {
		var parent = $(this).parents('.formRight:first');
		var options = $(parent).find('select:first option');
		if (options.length <= 10)
			$(this).hide();
	});
});
</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Settings</h5>
                <span>Here you can set various website settings. </span>
            </div>
            <div class="subnavtitle">
            <a href="<?php echo site_url('administration/maintenance'); ?>" title="" class="button blueB" style="margin: 5px;"><span>Maintenance</span></a>
            <a href="javascript:;" title="" class="button redB" style="margin: 5px;" onclick="$('form#settings').submit();"><span>Save Settings</span></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

<div class="wrapper">

<?php $template->load_template('notifications'); ?>

<form action="<?php echo site_url('administration/settings/save'); ?>" method="post" class="form" id="settings">
<fieldset>
	<div class="widget">
		<ul class="tabs">
		<?php foreach ($groups as $group) : ?>
			<li><a href="#<?php echo strtolower($group['name']); ?>"><?php echo __(ucfirst(strtolower($group['name']))); ?></a></li>
		<?php endforeach; ?>
		</ul>


		<div class="tab_container">
			<?php foreach ($groups as $group) : ?>
			<div id="<?php echo strtolower($group['name']); ?>" class="tab_content">
				<?php foreach($group['settings'] as $setting) : ?>
				<div class="formRow">
					<label><?php echo __($setting->label); ?>:</label>
					<div class="formRight"><?php echo $setting->get_html(); ?>
					<span class="formNote"><?php echo str_replace('"', "'", __($setting->description)); ?></span></div>
					<div class="clear"></div>
				</div>
				<?php endforeach; ?>
			</div>
			<?php endforeach; ?>
		</div>
		<div class="clear"></div>
	</div>
</fieldset>
</form>
</div>
