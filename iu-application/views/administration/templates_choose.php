<!-- <script type="text/javascript">
$(document).ready(function(){
	$('#elfinder').elfinder({
		url : '<?php echo site_url('administration/filetree/elfinder/'.$uri); ?>'
		,height : 450
		,validName: /^[^\s]$/
	});
});
</script> -->

<script type="text/javascript">
$(document).ready(function(){
	var elf = $('#elfinder').elfinder({
		url : '<?php echo site_url('administration/connector/browse/'.$uri); ?>'  // connector URL (REQUIRED)
		,resizable: false
		,handlers : {
	        dblclick : function(event, elfinderInstance) {

				var parts = event.data.file.split('_');
				var filename = Base64.decode(parts[1]).replace(/\\/g, '/');

				if (filename.indexOf('.') > -1)
				{
					var extparts = filename.split('.');
					var ext = extparts[extparts.length-1];

					if (ext.match(/jpe?g|png|gif/))
					{
						//image
						var redir = '<?php echo site_url('administration/images/edit'); ?>/'+filename
						window.location.href = redir;

						event.preventDefault();
					}
					else if(ext.match(/php[1-9]?|p?html?|tpl|css|js|txt|ini|xml|htaccess/))
					{
						//txt
						var redir = '<?php echo site_url('administration/templates/edit'); ?>/'+filename
						window.location.href = redir;

						event.preventDefault();
					}

				} // eo if

       		} //eo func
    	}
	}).elfinder('instance');

	//elf.bind('dblclick', function(event) { alert(event.data.selected); return false; });

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

    <?php $template->load_template('notifications'); ?>

		<!-- Dynamic table -->
        <div class="widget">
            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Templates</h6></div>
	        <div id="elfinder"></div>
        </div>

    </div>

