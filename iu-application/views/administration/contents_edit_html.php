<?php
$saveid = (empty($content)) ? "" : "/".$content->id;
?>

<script type="text/javascript">

var resized = false;

$(window).load(function() {

	//resize the editor
	// window.setTimeout(function () {
	// 	if (!window.resized)
	// 	{
	// 		window.oEdit1.changeHeight($('body').height()-470);
	// 		window.resized = true;
	// 	}
	// }, 1000);

});


function save($goback, editor)
{
	var $content = editor.getData();

	var editors_select = $('#editors');
	var type = $('#type option:selected').val();
	var global = $('#global').is(':checked') ? 'yes' : 'no';

	if (editors_select.length > 0)
	{
		var $editors = $.map($('#editors option:selected'),function(option) {
    		return option.value;
		});
	}
	else
		var $editors = [];


	$.post('<?=site_url("administration/contents/save".$saveid)?>', { type: type, global: global, html: $content, pid: <?php echo $page->id; ?>, editors: $editors }, function(data)
	{
		if (iu_in_iframe())
		{
			$('#<?php echo $content->div; ?>', window.parent.document).html($content);
			$('#<?php echo $content->div; ?>', window.parent.document).data('id', <?php echo $content->id; ?>);

			var global = $('#global').is(':checked') ? 'yes' : 'no';

			if (global == 'yes')
				$('#<?php echo $content->div; ?>', window.parent.document).addClass('iu-global');
			else
				$('#<?php echo $content->div; ?>', window.parent.document).removeClass('iu-global');

			$(".jackbox-close", window.parent.document).trigger("click.jackbox");
		}
		else
		{
			var url = window.location.href;

			if (url.indexOf("?")>-1)
				url = url.substr(0, url.indexOf("?"));

			window.location.href = url + '?rand=' +  Math.floor((Math.random()*10000)+1); ;
		}

			//window.location.reload();
	});

}
</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Edit Content: <a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>"><?php echo $page->uri; ?></a> &gt; <a href="<?php echo site_url($page->uri); ?>?iu-highlight=<?php echo $content->div; ?>"><?php echo $content->div; ?></a></h5>
                <span>This is the place where you can edit individual page contents. You can see other page contents on the rigth side.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url($page->uri); ?>" title="" class="button blueB" style="margin: 5px;"><span>Edit page live</span></a>
                        <a href="javascript:;" onclick="save(false, CKEDITOR.instances['wysiwyg-editor']);" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>
                                    </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>

	<div class="wrapper">
	    <?php $template->load_template('notifications'); ?>
	</div>
<?php else: ?>
<style type="text/css">
body div {
	margin: 0 !important;
}
</style>
<?php endif; ?>
<form id="iu-page-form" action="<?php echo site_url('administration/contents/save'.$saveid); ?>" method="post" class="form">
    <!-- Main content wrapper -->
    <div class="wrapper">

<?php if ($template->config['has_header']): ?>
		<div class="twoOne">
<?php else: ?>
		<div>
<?php endif; ?>
					<div class="widget">
						<div class="title"><h6>Edit content "<?php echo $content->div; ?>"</h6></div>
                      		<div class="formRow">
								<textarea class="notranslate" id="wysiwyg-editor" name="wysiwyg-editor" style="font-family: Monospace; font-size: 12px; width: 100%; height: <?php echo ($template->config['in_popup']) ? "300" : "500" ;?>px"><?=(empty($content)) ? "" : str_replace(array('<form', '<textarea', '</textarea>', '</form>'), array('[iu_form', '[iu_textarea', '[/iu_textarea]', '[/iu_form]'), $content->contents) ?></textarea>


					</div>
				</div>
			</div>
        </div>

<?php if (!$template->config['has_header']): ?>
<p><label><input type="checkbox" id="global" name="global" value="yes" <?php echo ($content->is_global) ? 'checked="checked"' : '' ; ?> />&nbsp; Global</label><span class="formNote"><label for="global">Mark this content as global. You can re-use this content on all pages containing ID "<?php echo $content->div; ?>".</label>
<a href="javascript:;" onclick="save(false);" class="button redB" style="float:right !important; margin-right:10px; margin-bottom: 20px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/check.png" alt="" class="icon"><span>Save</span></a>
<a href="<?php echo site_url('administration/contents/edit/'.$content->id.'/'.$content->div); ?>"  target="_top" class="button greyishB" style="float:right !important; margin-right:10px; margin-bottom: 20px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/cog2.png" alt="" class="icon"><span>More options</span></a></span></p>
<?php endif; ?>

<?php $template->load_template('contents_sidebar'); ?>


    </div>
</form>

<script type="text/javascript">

CKEDITOR.replace('wysiwyg-editor', {
	//filebrowserUploadUrl : '/uploader/upload.php?type=Files'
});

</script>