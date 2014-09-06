<script type="text/javascript">

function remove_user(id, name)
{
	iu_confirm('Are you sure you want to remove user "'+name+'"?', function() {
		window.location.href = IU_SITE_URL + '/administration/users/remove/'+id;
	});
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Users</h5>
                <span>On this page you can edit and add new users. You can also set user roles with predefined permissions.</span>
            </div>
            <div class="subnavtitle">
	            <a href="<?php echo site_url('administration/users/add'); ?>" title="" class="button basic" style="margin: 5px;"><span>Create new user</span></a>
            	<?php if ($user->can('manage_user_roles')): ?>
	            	<a href="<?php echo site_url('administration/userroles'); ?>" title="" class="button brownB" style="margin: 5px;"><span>User Roles</span></a>
	           	<?php endif; ?>

            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

    <!-- Main content wrapper -->
    <div class="wrapper">

    <?php $template->load_template('notifications'); ?>

		<!-- Dynamic table -->
        <div class="widget">
            <div class="title"><h6><span class="icon-users"></span> Users</h6></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
            <thead>
            <tr>
            <th>Name</th>
            <th>E-mail</th>
            <th>Role</th>
            <th>Added</th>
            <th>Last Update</th>
			<th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
            <tr class="gradeA">
			<td><a href="<?php echo site_url('administration/users/edit/'.$user->id); ?>"><?php echo $user->name; ?></a></td>
			<td><?php echo $user->email; ?></td>
			<td class="center"><?php echo $user->userrole->name; ?></td>
            <td class="center"><?php echo empty($user->created) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $user->created).'">'.relative_time($user->created) . '</span>'; ?></td>
            <td class="center"><?php echo empty($user->last_updated_content()->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $user->last_updated_content()->updated).'">'.relative_time($user->last_updated_content()->updated) . '</span> '; ?></td>
			<td class="actBtns">
				<a title="Edit" href="<?php echo site_url('administration/users/edit/'.$user->id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/edit.png" alt=""></a>
				<a title="Remove" href="javascript:;" onclick="remove_user(<?php echo $user->id; ?>, '<?php echo $user->name; ?>');" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/remove.png" alt=""></a>
			</td>
			</tr>
            <?php endforeach; ?>

            </tbody>
            </table>
        </div>
    </div>