<h2>Database connection</h2>

<p>Please review your database connection information:</p>
<p>&nbsp;</p>
<table class="cs-table">
<thead>
<tr>
	<th>Name</th>
	<th>Value</th>
</tr>
</thead>
<tbody>
<tr>
	<td class="center">Driver</td>
	<td class="center"><?php echo $database['dbdriver']; ?></td>
</tr>
<tr>
	<td class="center">Host</td>
	<td class="center"><?php echo $database['hostname']; ?></td>
</tr>
<tr>
	<td class="center">Name</td>
	<td class="center"><?php echo $database['database']; ?></td>
</tr>
<tr>
	<td class="center">User</td>
	<td class="center"><?php echo $database['username']; ?></td>
</tr>
<tr>
	<td class="center">Password</td>
	<td class="center"><?php echo empty($database['password']) ? "<i>empty</i>" : $database['password'] ; ?></td>
</tr>
<tr>
	<td class="center">Prefix</td>
	<td class="center"><strong><?php echo $database['dbprefix']; ?></strong></td>
</tr>
<tr>
	<td class="center">Charset</td>
	<td class="center"><?php echo $database['char_set']; ?></td>
</tr>

</tbody>
</table>
<p>&nbsp;</p>
<p>Ready when you are.</p>
<p>&nbsp;</p>
<form action="<?php echo site_url("setup/sql"); ?>" method="get">
<input class="submit" value="Proceed &raquo;" type="submit">
</form>
<p>&nbsp;</p>


<p>&nbsp;</p>
<p>&nbsp;</p>