<html>
<head>
<script type="text/javascript" src="<?php echo site_url('iu-dynamic-js/init.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('iu-resources/js/functions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery.min.js"></script>
</head>
<body>
<script type="text/javascript">

$(document).ready(function() {

	var img = <?php echo json_encode($image); ?>;

	var img_tag = window.parent.$('img.iu-pixlr-edited');

	if (img_tag.length < 1)
	{
		window.parent.iu_growl('No image found!');
	}
	else
	{
		img_tag.attr('src', img.url);

		//img_tag.removeClass('iu-pixlr-edited');

		var parent = img_tag.parents('div[class*="iu-content-"]:first');
		var parent_type = iu_content_type(parent);

		var width = img_tag.width();
		var height = img_tag.height();

		$.post(IU_SITE_URL+'/administration/images/make_thumb/'+width+'/'+height, { image: img.path }, function (data) {
			if ($.trim(data) !== 'FALSE')
			{
				if (parent_type == 'Html')
				{
					window.parent.iu_quick_save(parent);
				}
				else if (parent_type == 'Repeatable')
				{
					img_tag.attr('src', data);
					window.parent.$('img.iu-pixlr-edited').data('fullimg', img.path);

					var item_div = img_tag.parents('div.iu-item:first');
					var id = item_div.find('.iu-item-id:first').val();

					if ((id !== undefined) && (id !== 0))
						window.parent.iu_newsitem_save(item_div);
				}
			}

			img_tag.removeClass('iu-pixlr-edited');

			$(".jackbox-close", window.parent.document).trigger("click.jackbox");

		}, 'text');

	}

});



</script>
</body>
</html>

