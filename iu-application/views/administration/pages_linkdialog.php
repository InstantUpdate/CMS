<html>
<head>
<title><?php _e("Choose page"); ?></title>
<script type="text/javascript" src="<?php echo site_url('iu-dynamic-js/init.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery.easing.1.3.js"></script>
<style type="text/css">

ul.folder-list {
	display: none;
}

li.folder span, li.file span {
	cursor: pointer;
}
ul#pages-tree  {
	font-family: Verdana, sans-serif;
	font-size: 11px;
	line-height: 18px;
	padding: 0px;
	margin: 0px;
	margin-top: 30px !important;
	margin-left: -15px !important;
}

ul#pages-tree li {
	list-style: none;
	padding: 0px;
	padding-left: 20px;
	margin: 0px;
	white-space: nowrap;
}
ul#pages-tree {
	list-style: none;
	padding: 0px;
	padding-left: 20px;
	margin: 0px;
	white-space: nowrap;
}
ul.folder-list li {
	list-style: none;
	padding: 0px;
	padding-left: 0px;
	margin: 0px;
	white-space: nowrap;
}
ul.folder-list {
	list-style: none;
	padding: 0px;
	padding-left: 0px;
	margin: 0px;
	white-space: nowrap;
}
li.folder { background: url(<?php echo $template->base_url(); ?>images/filetree/directory.png) left top no-repeat; }
li.open { background: url(<?php echo $template->base_url(); ?>images/filetree/folder_open.png) left top no-repeat !important; }
li.file { background: url(<?php echo $template->base_url(); ?>images/filetree/html.png) left top no-repeat; }

</style>
<script type="text/javascript">

function iu_root_url(absURL)
{
	var niz = absURL.split('/');
	niz.shift(); niz.shift(); niz.shift();
	return '/'+niz.join('/');
}

var pages = {
'': ''
<?php foreach ($pages as $p): ?>
,'<?php echo $p->uri; ?>': '<?php echo str_replace("'", "\'", $p->title); ?>'
<?php endforeach; ?>
};

$(document).ready(function() {

	$('li.file').each(function() {
		var uri = $(this).data('uri');
		$(this).attr('title', pages[uri]);
	});

	$('li.folder > span').click(function() {
		var folder = $(this).parent().children('.folder-list:first');
		var li = $(this).parent();

		if (folder.is(':visible'))
		{
			folder.slideUp('slow', 'easeOutBounce');
			li.removeClass('open');
		}
		else
		{
			folder.slideDown('slow', 'easeOutBounce');
			li.addClass('open');
		}

	});

	$('li.file > span').click(function() {
		var uri = $(this).parent('li').data('uri');
		var full_url = IU_SITE_URL.replace(/\/$/, '')+'/'+uri;
		var root_url = iu_root_url(full_url);
		var title = pages[uri];

		$('#inpURL', window.parent.document).val(root_url);
		$('#inpTitle', window.parent.document).val(title);
	});
});
</script>
</head>
<body>
<?php echo Page::html_tree(Page::array_tree($pages)); ?>


</body>
</html>