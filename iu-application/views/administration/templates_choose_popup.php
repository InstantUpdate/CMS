<script type="text/javascript">
$(document).ready( function() {
    $('#filetree').fileTree({
		root: '',
		script: IU_SITE_URL+'/administration/connector/templates_onlyhtmlphp',
		expandSpeed: 750,
		collapseSpeed: 750,
		expandEasing: 'easeOutBounce',
		collapseEasing: 'easeOutBounce',
		multiFolder: true
    }, function(file) {

    	var insert = true;
    	$('#template option', window.parent.document).removeAttr('selected');

    	$('#template option', window.parent.document).each(function() {
    		var $opt = $(this);
    		if ($opt.text() == file)
    		{
    			$opt.attr('selected', 'selected');
    			insert = false;
    		}
    	});

    	if (insert)
    	{
	    	var newel = $('<option>').attr('selected', 'selected').val(file).text(file);
			$('#template', window.parent.document).append(newel);
    	}

        window.parent.$('#template').trigger("liszt:updated");
        $(".jackbox-close", window.parent.document).trigger("click.jackbox");

    });


    setTimeout(function() {
    	$('#loader').fadeOut();
    }, 2000);
});
</script>

<?php if ($template->config['has_header']): ?>
	<!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Templates</h5>
                <span>A template is usually a file that determines or serves as a starting point for a new page.</span>
            </div>
            <div class="subnavtitle">
				<a href="<?php echo site_url('administration/templates'); ?>" title="" class="button basic" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/dark/imagesList.png" alt="" class="icon" /><span>Create new template</span></a>
				<a href="javascript:;" onclick="duplicate();" title="" class="button greyishB" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/download.png" alt="" class="icon" /><span>Create new page</span></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

    <!-- Main content wrapper -->
    <div class="wrapper">

    <?php //$template->load_template('notifications'); ?>

		<!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Templates</h6></div>
	        <div id="loader"><p align="center">File list loading...</p><p>&nbsp;</p></div>
			<div id="filetree"></div>
        </div>

    </div>

