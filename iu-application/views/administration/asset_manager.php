<script type="text/javascript">
$(document).ready(function(){
	var elf = $('#elfinder').elfinder({
		url : '<?php echo site_url('administration/connector/assets'); ?>'  // connector URL (REQUIRED)
		,resizable: false
	}).elfinder('instance');

	//elf.bind('dblclick', function(event) { alert(event.data.selected); return false; });

});
</script>

<?php if ($template->config['has_header']): ?>
	<!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Asset Manager</h5>
                <span>This is the place where you can manage your asset files (images, documents and multimedia). To upload new files click on icon <img src="<?php echo $template->base_url(); ?>images/icons/dark/add.png" alt="" class="icon" height="12px" width="12px" /></span>
            </div>
            <div class="subnavtitle">
            	<?php if ($this->user->can('add_pages')): ?>
                        <a href="<?php echo site_url('administration/pages/add'); ?>" title="" class="button basic" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/dark/imagesList.png" alt="" class="icon" /><span>Create new page</span></a>
                <?php endif; ?>
						<a href="<?php echo site_url(); ?>" class="button blueB" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/preview.png" alt="" class="icon" /><span>Edit pages live</span></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

    <!-- Main content wrapper -->
    <div class="wrapper">

    <?php $template->load_template('notifications'); ?>

		<!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Assets</h6></div>
	        <div id="elfinder"></div>
        </div>

    </div>

