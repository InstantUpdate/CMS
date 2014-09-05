<h2><?php echo CS_PRODUCT_NAME; ?> <?php echo CS_SETUP_VERSION; ?></strong> setup wizard</h2>

<p>Welcome to <strong><?php echo CS_PRODUCT_NAME; ?></strong> installation! This wizard will guide you through the process of installation or upgrade of <?php echo CS_PRODUCT_NAME; ?> by CubeScripts Media.</p>
<p>&nbsp;</p>
<h3>System reqirements</h3>
<p>&nbsp;</p>
<table class="cs-table">
<thead>
<tr>
	<th>Module</th>
	<th>Available</th>
</tr>
</thead>
<tbody>
<?php foreach ($modules as $module=>$exists): ?>
<tr>
	<td class="center"><?php echo $module; ?></td>
	<td class="center"><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$exists) ? "not-" : ""; ?>ok.png" title="<?php echo $module; ?> is <?php echo (!$exists) ? "not " : ""; ?>available" alt="<?php echo $module; ?> is <?php echo (!$exists) ? "not " : ""; ?>available" /></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<table class="cs-table">
<thead>
<tr>
	<th>Overall</th>
	<th><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$all_modules) ? "not-" : ""; ?>ok.png" title="<?php echo (!$all_modules) ? "NOT " : ""; ?>OK!" alt="<?php echo (!$all_modules) ? "NOT " : ""; ?>OK!" /></th>
</tr>
</thead>
</table>
<p>&nbsp;</p>
<h3>PHP configuration</h3>
<p>&nbsp;</p>
<table class="cs-table">
<thead>
<tr>
	<th>Variable</th>
	<th>Enabled</th>
</tr>
</thead>
<tbody>
<?php foreach ($inis as $ini=>$enabled): ?>
<tr>
	<td class="center"><?php echo $ini; ?></td>
	<td class="center"><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$enabled) ? "not-" : ""; ?>ok.png" title="&quot;<?php echo $ini; ?>&quot; is <?php echo (!$enabled) ? "not " : ""; ?>enabled" alt="<?php echo $ini; ?> is <?php echo (!$enabled) ? "not " : ""; ?>enabled" /></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<table class="cs-table">
<thead>
<tr>
	<th>Overall</th>
	<th><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$all_inis) ? "not-" : ""; ?>ok.png" title="<?php echo (!$all_inis) ? "NOT " : ""; ?>OK!" alt="<?php echo (!$all_inis) ? "NOT " : ""; ?>OK!" /></th>
</tr>
</thead>
</table>
<p>&nbsp;</p>
<h3>File/folder permissions:</h3>
<p>&nbsp;</p>
<table class="cs-table">
<thead>
<tr>
	<th>Path</th>
	<th>Writable</th>
</tr>
</thead>
<tbody>
<?php foreach ($files as $file=>$writable): ?>
<tr>
	<td class="center"><?php echo $file; ?></td>
	<td class="center"><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$writable) ? "not-" : ""; ?>ok.png" title="&quot;<?php echo $file; ?>&quot; is <?php echo (!$writable) ? "not " : ""; ?>writable" alt="<?php echo $file; ?> is <?php echo (!$writable) ? "not " : ""; ?>writable" /></td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<table class="cs-table">
<thead>
<tr>
	<th>Overall</th>
	<th><img src="<?php echo $template->base_url(); ?>assets/<?php echo (!$all_files_writable) ? "not-" : ""; ?>ok.png" title="<?php echo (!$all_files_writable) ? "NOT " : ""; ?>OK!" alt="<?php echo (!$all_files_writable) ? "NOT " : ""; ?>OK!" /></th>
</tr>
</thead>
</table>
<p>&nbsp;</p>
<?php if (!$all_files_writable): ?>
<p><img src="<?php echo $template->base_url(); ?>assets/not-ok.png" alt="Error" /> It seems not all files are writable. Please make look for them in "<?php echo realpath(FCPATH); ?>" and make them writable.</p>
<?php endif; ?>
<?php if (!$all_modules): ?>
<p><img src="<?php echo $template->base_url(); ?>assets/not-ok.png" alt="Error" /> It seems not all required modules are available. Please enable/install them if you administer this web server or ask your web hosting administrator to do that for you.</p>
<?php endif; ?>
<?php if (!$all_inis): ?>
<p><img src="<?php echo $template->base_url(); ?>assets/not-ok.png" alt="Error" /> It seems not all required PHP options are correctly set up. Please enable them if you administer this web server or ask your web hosting administrator to do that for you.</p>
<?php endif; ?>
<?php if ($all_modules && $all_files_writable && $all_inis): ?>
<p><img src="<?php echo $template->base_url(); ?>assets/ok.png" alt="Error" /> Everything seems to be fine. You may proceed with installation.</p>
<p>&nbsp;</p>
<form action="<?php echo site_url("setup/$next"); ?>" method="get">
<input class="submit" value="Proceed &raquo;" type="submit">
</form>
<?php endif; ?>
<p>&nbsp;</p>