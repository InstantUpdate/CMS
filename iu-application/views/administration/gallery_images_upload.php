<script type="text/javascript">

$(document).ready(function() {
    $('#file_upload').uploadify({
    	'debug'    : false,
        'swf'      : '<?php echo $template->base_url(); ?>js/uploadify/uploadify.swf'
        ,'uploader' : '<?php echo root_url( site_url('administration/ajax/gallery_upload/'.$content->id.'/'.$user->id) ); ?>'
        ,'onQueueComplete': function() {
			var exists = $('#<?php echo $content->div; ?>', window.parent.document).attr('data-id');

			if ($.trim(exists) == "")
				window.parent.location.reload();
			else
				window.parent.iu_repeatable_load_page(1, '<?php echo $content->div; ?>');

			$(".jackbox-close", window.parent.document).trigger("click.jackbox");
		}
    });
});

</script>

   <div class="wrapper">
	<form id="iu-upload-form" action="<?php echo site_url('administration/images/upload'); ?>" method="post" class="form" class="validate" enctype="multipart/form-data">
		<fieldset>
			<div>
				<div class="widget">
					<div class="title"><h6>Add images</h6></div>

                   	<div class="formRow">
                        <p><input type="file" name="file_upload" id="file_upload" /></p>
                   	</div>

				</div>

       		</div>
       	</fieldset>
      	</form>
   </div>