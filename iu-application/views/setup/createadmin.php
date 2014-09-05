<h2>Create administrator</h2>

<p>Now when database setup is finished, you should create your first administrator user. Remember what you are entering as you will need this information to login into administration panel:</p>
<?php if (isset($error)) : ?>
<p><strong>Error:</strong> <span style="color: #ff0000;"><?php echo $error; ?></span></p>
<?php endif; ?>
<p>&nbsp;</p>
<form action="<?php echo site_url("setup/saveadmin"); ?>" method="post">
<table class="cs-table">
<thead>
<tr>
	<th>Field</th>
	<th>Value</th>
</tr>
</thead>
<tbody>
<tr>
	<td class="center">Name:</td>
	<td class="center"><input type="text" name="name" /></td>
</tr>
<tr>
	<td class="center">E-mail:</td>
	<td class="center"><input type="text" name="email" /></td>
</tr>
<tr>
	<td class="center">Password:</td>
	<td class="center"><input type="password" name="password" /></td>
</tr>
<tr>
	<td class="center">Repeat password:</td>
	<td class="center"><input type="password" name="password2" /></td>
</tr>

</tbody>
</table>
<p>&nbsp;</p>
<p>Just click on the button below and that is it.</p>
<p>&nbsp;</p>
<input class="submit" value="Proceed &raquo;" type="submit">
</form>
<p>&nbsp;</p>


<p>&nbsp;</p>
<p>&nbsp;</p>