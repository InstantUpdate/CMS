<?php
$saveid = (empty($item)) ? "" : "/".$item->id;
?>

<script type="text/javascript">

function remove_image(id)
{
	$.get(IU_SITE_URL+'/administration/repeatables/ajax_removeimage/'+id, function () {
		$('#item-image').fadeOut();
	}, 'json');
}

$(document).ready(function() {
	$( ".datepicker" ).datepicker({
		autoSize: true,
		appendText: '(<?php echo Setting::value('datepicker_format', 'dd/mm/yy'); ?>)',
		dateFormat: '<?php echo Setting::value('datepicker_format', 'dd/mm/yy'); ?>'
	});
});

<?php if ($template->config['has_header']): ?>
var myheight = screen.height;
<?php else: ?>
var myheight = Math.floor(screen.height * 0.6);
<?php endif; ?>

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
            <?php $content = isset($item) ? $item->content : $content; ?>
            <?php if (isset($item)): ?>
                <h5>Edit blog/news item on content: <a href="<?php echo site_url('administration/contents/edit/'.$content->id.'/'.$content->div); ?>"><?php echo $content->div; ?></a></h5>
            <?php else: ?>
				<h5>Add new blog/news item on content: <a href="<?php echo site_url('administration/contents/edit/'.$content->id.'/'.$content->div); ?>"><?php echo $content->div; ?></a></h5>
            <?php endif; ?>
				<span>Here you can edit and manage each repetable item. You can also edit this content item live.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/contents/edit/'.$content->id.'/'.$content->div); ?>" title="" class="button basic" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/dark/view.png" alt="" class="icon" /><span>List all</span></a>
                        <a href="<?php echo site_url($content->page->uri); ?>" title="" class="button blueB" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/preview.png" alt="" class="icon" /><span>Edit item live</span></a>
                        <a href="javascript:;" onclick="$('#iu-repeatable-form').submit();" title="" class="button redB" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/check.png" alt="" class="icon" /><span>Save</span></a>

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
<form id="iu-repeatable-form" action="<?php echo site_url('administration/repeatables/save'.$saveid); ?>" method="post" enctype="multipart/form-data" class="form">
<input type="hidden" name="cid" value="<?php echo $content->id; ?>" />
    <!-- Main content wrapper -->
    <div class="wrapper">

		<div class="twoOne">

			<fieldset>
				<div class="widget">
					<div class="title"><h6>Content "<?php echo $content->div; ?>" items</h6></div>

					<div class="formRow">
                       	<label>Item title:</label>
                       	<div class="formRight"><input class="notranslate" type="text" name="title" value="<?php echo empty($item) ? '' : $item->title ; ?>" />
                       	<span class="formNote">This is the title of your news/blog item.</span></div>
                       	<div class="clear"></div>
                   	</div>

                   	<div class="formRow">

                       	<label>Item contents:</label>

						<textarea class="notranslate" id="text" name="text" style="font-family: Monospace; font-size: 12px; width: 100%; height: <?php echo ($template->config['in_popup']) ? "300" : "500" ;?>px"><?=(empty($item)) ? "" : str_replace(array('<form', '<textarea', '</textarea>', '</form>'), array('[iu_form', '[iu_textarea', '[/iu_textarea]', '[/iu_form]'), empty($item) ? ' ' : $item->text) ?></textarea>

						<script type="text/javascript">
						var oEdit1 = new InnovaEditor("oEdit1");
							oEdit1.width = "100%";
							oEdit1.height = myheight;

						    oEdit1.arrCustomButtons = [["HTML5Video", "modalDialog('<?php echo base_url(); ?>/iu-application/views/administration/wysiwyg/scripts/common/webvideo.htm',690,330,'HTML5 Video');", "HTML5 Video", "btnVideo.gif"]];

						    //oEdit1.arrCustomButtons.push(["Save", "save(false)", "Save Content", "btnSave.gif"]);

							oEdit1.groups = [
								["group1", "", ["Bold", "Italic", "Underline", "FontDialog", "ForeColor", "TextDialog", /*"Styles",*/ "RemoveFormat"]]
								,["group2", "", ["Bullets", "Numbering", "JustifyLeft", "JustifyCenter", "JustifyRight"]]
								,["group3", "", ["LinkDialog", "ImageDialog", "YoutubeDialog", "TableDialog", "Emoticons"]]
								,["group4", "", ["Undo", "Redo", "SourceDialog"]]
								//,["group5", "", ["Save"]]
							];//*/

							oEdit1.css = '<?php echo root_url($template->base_url() . 'wysiwyg/scripts/style/awesome.css'); ?>';
							oEdit1.returnKeyMode = 3;

							oEdit1.fileBrowser = "<?php echo root_url($template->base_url() . 'wysiwyg/assetmanager/asset.php'); ?>";

							var html = document.getElementById('text').value;
							html = html.replace('[/iu_textarea]', '</'+'textar'+'ea>');
							html = html.replace('[/iu_form]', '</'+'fo'+'rm>');
							html = html.replace('[iu_textarea', '<'+'textar'+'ea');
							html = html.replace('[iu_form', '<'+'fo'+'rm');
							document.getElementById('text').value = html;

							oEdit1.cleanEmptySpan = function() { return true; };

							oEdit1.REPLACE("text");
						</script>
					</div>

				</div>

			</fieldset>

		</div>


		<div class="oneThree">
           	<fieldset>
				<div class="widget">
					<div class="title"><h6>Publishing options</h6></div>

	                <div class="formRow">
	                    <label>Date:</label>
	                    <div class="formRight">
	                        <input class="datepicker" style="width:75px !important;" type="text" name="date" value="<?php echo format_datepicker(empty($item) ? time() : $item->timestamp); ?>">
	                    </div>
	                    <div class="clear"></div>
	                </div>


	                <div class="formRow">
	                    <label>Time:</label>
	                    <div class="formRight">
	                        <input class="timepicker" style="width:75px !important;" type="text" name="time" value="<?php echo date('H:i', empty($item) ? time() : $item->timestamp); ?>">
	                    </div>
	                    <div class="clear"></div>
	                </div>

	                <?php if ($user->is_admin()): ?>
	                <div class="formRow">
                       	<label>Author:</label>
                       	<div class="formRight searchDrop">
                        	<select data-placeholder="<?php echo empty($item) ? __('Select author') : $item->user->name; ?>" class="chzn-select" style="width:300px;" tabindex="2" name="user_id">
                            	<option value=""></option>
                            	<?php foreach ($users as $us): ?>
                            	<option value="<?php echo $us->id; ?>" <?php echo (isset($item) && ($us->id !== $item->user_id)) ? '' : 'selected="selected"' ; ?>><?php echo $us->name; ?></option>
                           		<?php endforeach; ?>
                        	</select>
                       	</div>
                       	<div class="clear"></div>
                   	</div>
                   	<?php endif; ?>

				</div>
			</fieldset>
        </div>

        		<div class="oneThree">
           	<fieldset>
				<div class="widget">
					<div class="title"><h6>Image</h6></div>

                    	<div class="formRow">
	                        <label>Image:</label>
	                        <div class="formRight">
	                        <?php if (isset($item) && !empty($item->image)): ?>
	                        <div id="item-image">
								<img src="<?php echo Image::factory($item->image)->thumbnail(300)->url; ?>" alt="" /><br />
								(<a href="javascript:;" onclick="remove_image(<?php echo $item->id; ?>);">remove</a>)<br /><br />
							</div>
							<?php endif; ?>
	                        <input type="file" name="image" id="image" />
                        	<span class="formNote">Item image.</span></div>
	                        <div class="clear"></div>
                    	</div>

				</div>
			</fieldset>
        </div>

    </div>
</form>