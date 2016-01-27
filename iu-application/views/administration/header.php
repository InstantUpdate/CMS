<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<script type="text/javascript">
var IU_TEMPLATE_URL = '<?php echo $template->base_url(); ?>';
</script>

<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<link href="<?php echo $template->base_url(); ?>css/main.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="<?php echo $template->base_url(); ?>css/font.css">
<title><?php echo $title; ?></title>

<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>/iu-resources/min/?g=admin-css" />

<script type="text/javascript" src="<?php echo site_url('iu-dynamic-js/init.js'); ?>"> </script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery.min.js"> </script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>ckeditor/ckeditor.js"> </script>

<script type="text/javascript" src="<?php echo base_url(); ?>/iu-resources/min/?g=admin-js"> </script>
<script type="text/javascript" src="<?php echo base_url(); ?>/iu-resources/min/?g=admin-js-2"> </script>
<script type="text/javascript" src="<?php echo site_url('iu-resources/js/functions.js'); ?>"> </script>

<!--<script src="<?php echo base_url(); ?>/iu-resources/lightbox/js/libs/Jacked.js" type="text/javascript"> </script>
<script src="<?php echo base_url(); ?>/iu-resources/lightbox/js/jackbox.js" type="text/javascript"> </script>-->
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/jquery-ui.min.js"> </script>

<?php //PluginManager::do_actions('header.head'); ?>

<?php if ($template->config['has_header']): ?>
<style type="text/css">
@media screen and (max-width: 1000px) {
	#rightSide {
		margin: -80px 0 0 0 !important;
	}
}
</style>
<?php endif; ?>


<script type="text/javascript">

$(document).ready(function() {
	var pull = $('#pull');
	menu = $('.iu-wrapper');
	menuHeight = menu.height();

	$(pull).on('click', function(e) {
		e.preventDefault();
		menu.slideToggle();
	});

	$(window).resize(function(){
		var w = $(window).width();
		if(w > 1000 && menu.is(':hidden'))
			menu.removeAttr('style');
	});

	//$('.lightbox').jackBox("init");

});

$(window).bind('load', function() {
	var footerHeight = 0,
		pageFoot = $('#footer');

	positionFooter();

	function positionFooter() {

		if (pageFoot.length < 1)
			return;

		footerHeight = pageFoot.innerHeight();

		if(($(document.body).height() + (footerHeight)) < $(window).height()) {
			pageFoot.css({
				position: 'fixed',
				bottom: 0,
				left :0,
				right: 0
			})
		} else {
			pageFoot.attr('style', 'margin: 30px 0 0 0');
		}
	}

	$(window).resize(positionFooter);
});

<?php //PluginManager::do_actions('header.dom_ready'); ?>

</script>


</head>


<?php if ($template->config['has_header']): ?>
<body class="iu-move-down">
	<!-- start sticky menu -->
	<div class="iu-topNav">
        <div class="iu-wrapper">
            <div class="iu-welcome">
            <a title="" href="<?php echo site_url('administration/users/edit/'.$user->id); ?>"><img alt="" src="<?php echo $user->get_profile_picture_thumb(20, 20); ?>"><span><?php echo $user->name; ?></span></a>
            </div>
            <div class="iu-userNav">
                <ul>
                    <li><a title="" href="<?php echo site_url('administration/dashboard'); ?>"><span>Dashboard</span></a></li>
                    <li><a title="" href="<?php echo site_url('administration/pages'); ?>"><span>Manage Pages</span></a></li>
                    <?php if ($user->can('edit_templates') || $user->can('edit_assets') || $user->can('edit_all_assets')): ?>
                    <li><a title="" href="<?php echo site_url('administration/templates'); ?>"><span>Manage Files</span></a></li>
                    <?php endif; ?>
                    <?php if ($user->can('manage_users')): ?>
                    <li class="iu-dd"><a title="" href="<?php echo site_url('administration/users'); ?>"><span>Manage Users</span></a></li>
                    <?php endif; ?>
<!--                     <li><a href="<?php echo site_url('administration/repeatables'); ?>" title=""><span>News/Blog</span></a></li>
                    <li><a href="<?php echo site_url('administration/galleries'); ?>" title=""><span>Gallery</span></a></li> -->
<!-- 
                    <li><a href="#">Plugins</a>
                    <ul>
                    <?php //PluginManager::do_actions('header.nav_list', array($user)); ?>
                    </ul>
					</li> -->


					<?php if ($user->can('edit_settings')): ?>
                    <li><a href="<?php echo site_url('administration/settings'); ?>" title=""><span>Settings</span></a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo site_url('administration/statistics'); ?>" title=""><span>Statistics</span></a></li>
					<li><a href="<?php echo site_url('administration/auth/logout'); ?>" title=""><span>Logout</span></a></li>
                    <li style="display: none !important;"><a title="" href="" class="iu-hide-menu">Hide menu</a></li>

				</ul>
            </div>
            <div class="iu-clear"></div>
        </div><a href="#" id="pull">Menu</a>
    </div>
	<!-- end sticky menu -->
<?php else: ?>
<body>
<?php endif; ?>

<?php //PluginManager::do_actions('body.top'); ?>

<!-- Right side -->
<div id="rightSide">
