<!--?xml version="1.0" encoding="utf-8"?-->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head>
<meta content="IE=EmulateIE7" http-equiv="X-UA-Compatible">
<title><?php echo $title; ?></title>
<link charset="utf-8" href="<?php echo $template->base_url(); ?>assets/main.css" media="screen" rel="stylesheet" type="text/css">
<link charset="utf-8" href="<?php echo $template->base_url(); ?>assets/side.css" media="screen" rel="stylesheet" type="text/css">
<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
</head>
<body class="cube login" id="body-cube">
<div id="wrapper">

	<div id="body-container">
	<a href="http://www.instant-update.com/" id="logo-link"></a><img alt="cube home link" id="print-logo">
		<div id="body-header">

			
			<ul class="links hidden">
			</ul>
			<div id="tabs-login">
				<ul class="tabs">
					<?php foreach ($navigation as $text=>$link): ?>
					<li<?php echo ((reset(explode('/',$link)) == $system->uri->segment(2)) ? ' class="selected"' : "" ); ?>><a href="<?php echo site_url("setup/$link"); ?>"<?php if (in_array($link, array("sql", "database"))): ?> onclick="return false;"<?php endif; ?>><?php echo $text; ?></a></li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="ie6TabIssueFix">
			</div>
		</div>
		<div id="main" class="box-shadow">
			<div class="formbox" id="formbox-signup">
			