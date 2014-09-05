<style>
div.uploader { width: 223px !important; position: relative !important; overflow: hidden !important; box-shadow: 0 0 0 2px #f4f4f4 !important; -webkit-box-shadow: 0 0 0 2px #f4f4f4; -moz-box-shadow: 0 0 0 2px #f4f4f4 !important; border: 1px solid #DDD !important; background: white; padding: 2px 2px 2px 8px !important; cursor:pointer !important; }
div.uploader span.action { width: 22px !important; background: #fff url(<?php echo $template->base_url().'images/addFiles.png'; ?>) no-repeat 0 0 !important; height: 22px !important; font-size: 11px; font-weight: bold !important; cursor: pointer !important; float: right !important; text-indent: -9999px; display: inline !important; overflow: hidden !important; cursor: pointer !important; }
div.uploader:hover span.action { background-position: 0 -27px !important; }
div.uploader:active span.action { background-position: 0 -54px !important; }
div.uploader span.filename { color: #777; max-width: 200px; font-size: 11px; line-height: 22px; float: left; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; cursor: default; }
div.uploader input { width: 200px !important; opacity: 0; filter: alpha(opacity:0); position: absolute; top: 0; right: 0; bottom: 0; float: right; height: 26px; border: none; cursor: pointer; }
.uploader { display: -moz-inline-box !important; display: inline-block !important; vertical-align: middle !important; zoom: 1; *display: inline !important; }
</style>
<script type="text/javascript">

function iu_start_upload()
{
	$('#iu-loader').fadeIn();
	$('form#iu-upload-form').submit();
}

</script>

   <div class="wrapper">
	<form id="iu-upload-form" action="<?php echo site_url('administration/images/upload'); ?>" method="post" class="form" class="validate" enctype="multipart/form-data">
		<fieldset>
			<div>
				<div class="widget">
					<div class="title"><h6>Replace image</h6></div>

                   	<div class="formRow">
                        <label>Upload new image:</label>
                        <div class="formRight">
                        <input type="file" name="image" id="image" onchange="iu_start_upload();" />
                        <div class="clear"></div>
                  		<center><img id='iu-loader' style='display: none; margin-top:8px !important;' src='<?php echo site_url('iu-resources/images/ajax-load.png'); ?>' /></center>

                   	</div>

				</div>

       		</div>
       	</fieldset>
      	</form>
   </div>