<?php
$saveid = (empty($content)) ? "" : "/".$content->id;
?>

<script type="text/javascript">


function edit_image_data(id)
{
	$('#img_id').val(id);
	var $img = $('#image_'+id).find('img:first');
	var title = $img.attr('title');
	var desc = Base64.decode($img.data('desc'));

	$.msgbox("",
		{
			type    : "prompt",
			inputs  : [
				{type: "text", label: "Title:", value: title, required: false},
				{type: "text", label: "Description (HTML allowed):", value: desc, required: false}
			],
			buttons : [
				{type: "submit", value: "Save"},
				{type: "cancel", value: "Cancel"}
			]
		},
		function (title, desc) {
			if (title)
			{
				status('Saving...');
				$.post('<?php echo site_url(); ?>/administration/ajax/gallery_save_image_data/'+id, {
					'title': title,
					'desc': desc
				}, function(json) {

					if (json.status == 'OK')
					{
						$img.attr('title', title);
						$img.data('desc', Base64.encode(desc));
						status('Saved.');
					}
					else
						status(json.message);

					window.setTimeout(reset_status, 3000);

				}, 'json');
			}
		}
	);

}


function reset_status()
{
	$('#status').fadeOut();
}

function status(msg)
{
	var $status = $('#status');
	$status.text(msg);
	if (!$status.is(':visible'))
		$status.show();
}

function gallery_remove_image(id)
{
	iu_confirm('Are you sure you want to remove this image?\n\n<br/><br/>Note that this cannot be undone.', function() {
		status('Deleting...');
		$.get('<?php echo site_url(); ?>/administration/ajax/gallery_remove_image/'+id, function(data) {
			if (data.status == 'OK')
			{
				$('#image_'+id).fadeOut('slow', function(){ $(this).remove(); });
				status('Deleted.');
			}
			else
				status(data.message);

			window.setTimeout(reset_status, 3000);
		}, 'json');
	});
}

$(document).ready(function() {
    $('#file_upload').uploadify({
    	'debug'    : false,
        'swf'      : '<?php echo $template->base_url(); ?>js/uploadify/uploadify.swf'
		,'buttonText' : 'Upload images'
        ,'uploader' : '<?php echo site_url('administration/ajax/gallery_upload/'.$content->id.'/'.$user->id); ?>'
        ,'onQueueComplete': function() { window.location.reload(); }
    });

    $("#sortable").sortable({
    	update: function(event, ui) {
            var newOrder = $(this).sortable('toArray').toString();
            status('Saving order...');
            $.post('<?php echo site_url('administration/ajax/gallery_save_order'); ?>', { 'order': newOrder }, function(json) {
            	if (json.status != "undefined")
            		status('Saved!');
            	else
            		status('Error!');

            	window.setTimeout(reset_status, 3000);

            }, 'json');
        }
    });



});

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Edit Gallery: <a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>"><?php echo basename($page->uri); ?></a> &gt; <a href="<?php echo site_url($page->uri); ?>?iu-highlight=<?php echo $content->div; ?>"><?php echo $content->div; ?></a></h5>
                <span>This is the gallery that appears in "<?php echo $content->div; ?>". You can also set options for this content. </span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/galleries'); ?>" title="" class="button basic" style="margin: 5px;"><span>List all galleries</span></a>
                        <a href="<?php echo site_url($page->uri); ?>" title="" class="button blueB" style="margin: 5px;"><span>Edit gallery live</span></a>
                        <a href="javascript:;" onclick="$('#iu-gallery-form').submit();" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>

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
<form id="iu-gallery-form" action="<?php echo site_url('administration/contents/save'.$saveid); ?>" method="post" class="form">
<input type="hidden" name="pid" value="<?php echo $page->id; ?>" />
	<!-- Main content wrapper -->
    <div class="wrapper">

<?php if ($template->config['has_header']): ?>
		<div class="twoOne" style="min-height: 600px;">
<?php else: ?>
		<div>
<?php endif; ?>
			<div class="widget">
				<div class="title"><h6>Images in gallery "<?php echo $content->div; ?>" (drag and drop to re-order)</h6><div class="num"><a class="greyNum" id="status" style="display:none"></a></div></div>
				<!-- gallery -->
				<div class="gallery">
	               <ul id="sortable">
	               <?php $images = GalleryItem::factory()->where_related_content('id', $content->id)->get(); ?>
	               <?php foreach ($images as $img): ?>
	                    <li id="image_<?php echo $img->id; ?>"><img src="<?php echo Image::factory($img->image)->thumbnail(101, 101)->url; ?>" alt="<?php echo $img->title; ?>" title="<?php echo $img->title; ?>" data-desc="<?php echo base64_encode($img->text); ?>" />
	                        <div class="actions">
	                            <a href="javascript:;" title="Edit" onclick="edit_image_data(<?php echo $img->id; ?>);"><img src="<?php echo $template->base_url(); ?>images/icons/update.png" alt="" /></a>
	                            <a href="javascript:;" title="Remove" onclick="gallery_remove_image(<?php echo $img->id; ?>);"><img src="<?php echo $template->base_url(); ?>images/icons/delete.png" alt="" /></a>
	                        </div>
	                    </li>
	                <?php endforeach; ?>

	               </ul>
            			<div class="fix"></div>
            			</div>
				<!-- eo gallery -->

				<p></p>
				<p><input type="file" name="file_upload" id="file_upload" /></p>

			</div>
        </div>

<?php $template->load_template('contents_sidebar'); ?>


    </div>
</form>