<html>
<head>
<script type="text/javascript" src="<?php echo site_url('iu-dynamic-js/init.js'); ?>"></script>
<script type="text/javascript" src="<?php echo site_url('iu-resources/js/functions.js'); ?>"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery.min.js"></script>
</head>
<body>
<script type="text/javascript">

$(document).ready(function() {
	window.parent.iu_file_browser.exec('reload');
	window.parent.iu_alert('Image is saved in your assets folder: <b><?php echo $image['path']; ?></b>', 'info');
	$(".jackbox-close", window.parent.document).trigger("click.jackbox");
});



</script>
</body>
</html>

