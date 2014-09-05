<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0; user-scalable=0;" />
<link href="<?php echo $template->base_url(); ?>css/main.css" rel="stylesheet" type="text/css" />
<title><?php echo $title; ?></title>
<link href="<?php echo $template->base_url(); ?>css/main.css" rel="stylesheet" type="text/css" />

<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/spinner/ui.spinner.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/spinner/jquery.mousewheel.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/charts/excanvas.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/charts/jquery.sparkline.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/uniform.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.cleditor.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.validationEngine-en.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.validationEngine.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/autogrowtextarea.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.maskedinput.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.dualListBox.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/jquery.inputlimiter.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/forms/chosen.jquery.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/wizard/jquery.form.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/wizard/jquery.validate.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/wizard/jquery.form.wizard.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/uploader/plupload.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/uploader/plupload.html5.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/uploader/plupload.html4.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/uploader/jquery.plupload.queue.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/tables/datatable.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/tables/tablesort.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/tables/resizable.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.tipsy.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.collapsible.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.prettyPhoto.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.progress.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.timeentry.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.colorpicker.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.jgrowl.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.breadcrumbs.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/ui/jquery.sourcerer.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/calendar.min.js"></script>
<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/plugins/elfinder.min.js"></script>

<script type="text/javascript" src="<?php echo $template->base_url(); ?>js/custom.js"></script>

</head>

<body class="nobg loginPage">
<!-- Main content wrapper -->
<div class="loginWrapper">
    <div class="loginLogo"><img src="<?php echo $template->base_url(); ?>images/loginLogo.png" alt="" /></div>
	<?php $template->load_template('notifications'); ?>
	<div class="widget">
        <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/files.png" alt="" class="titleIcon" /><h6>Login panel</h6></div>
        <form action="<?php echo site_url('administration/auth/verify'); ?>" id="validate" class="form" method="post" style="clear:left;">
            <fieldset>
                <div class="formRow">
                    <label for="login">E-mail:</label>
                    <div class="loginInput"><input type="text" name="email" class="validate[required]" id="login" /></div>
                    <div class="clear"></div>
                </div>

                <div class="formRow">
                    <label for="pass">Password:</label>
                    <div class="loginInput"><input type="password" name="password" class="validate[required]" id="pass" /></div>
                    <div class="clear"></div>
                </div>

                <div class="loginControl">
                    <div class="rememberMe"><a href="<?php echo site_url('administration/auth/forgot'); ?>">Forgot password?</a><!-- input type="checkbox" id="remMe" name="remember" /><label for="remMe">Remember me</label> --></div>
                    <input type="submit" value="Sign In" class="dredB logMeIn" />
                    <div class="clear"></div>
                </div>
            </fieldset>
        </form>
    </div>
</div>

</body>
</html>