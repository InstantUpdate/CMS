<script type="text/javascript">

function remove_plugin(slug, name)
{
	iu_confirm('Are you sure you want to remove plugin "'+name+'"? Note that this can not be undone!',
	function () {
		window.location.href = IU_SITE_URL + '/administration/plugins/remove/'+slug;
	});
}

</script>

<?php if ($template->config['has_header']): ?>
<!-- Title area -->
<div class="titleArea">
	<div class="wrapper">
		<div class="pageTitle">
			<h5>Plugins</h5>
			<span>This is a nice place to extend your Instant Update functionalities.</span>
		</div>
		<div class="subnavtitle">
<!--			<a href="<?php echo $button['href']; ?>" <?php echo $button['attributes']; ?> class="button <?php echo $button['color']; ?>B" style="margin: 5px;"><img src="<?php echo $button['icon']; ?>" alt="" class="icon" /><span><?php echo $button['title']; ?></span></a>
	-->	</div>
		<div class="clear"></div>
	</div>
</div>

<div class="line"></div>
<?php endif; ?>

<div class="wrapper">
	<?php $template->load_template('notifications'); ?>
	<p>&nbsp;</p>
</div>

<div class="wrapper">

	<div class="">
    	<div class="widget">
            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Plugins</h6></div>

	        <table class="sTable" cellpadding="0" cellspacing="0" width="100%">
                <thead>
                    <tr>
                        <td>Name</td>
                        <td>Active</td>
                        <td>Author</td>
                        <td>Description</td>
                        <td>Actions</td>
                    </tr>
                </thead>
                <tbody>
                <?php
				foreach ($fs_plugins as $slug):
                $plugin = Plugin::factory()->get_by_slug($slug);
				?>
                    <tr>
                        <td><?php echo anchor($plugin->url, $plugin->name .' '. $plugin->version, 'target="_blank"'); ?></td>
                        <td><?php echo ($plugin->active) ? '<strong>yes</strong>' : 'no'; ?></td>
                        <td><?php echo anchor($plugin->author_url, $plugin->author, 'target="_blank"'); ?></td>
                        <td><?php echo $plugin->description; ?></td>
                        <td>
                        <?php echo anchor('administration/plugins/toggle/'.$slug, ($plugin->active) ? 'deactivate' : 'activate'); ?>
                        , <a href="javascript:;" onclick="remove_plugin('<?php echo $slug; ?>', '<?php echo $plugin->name; ?>');">remove</a>
						</td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>
        </div>
    </div>


</div>