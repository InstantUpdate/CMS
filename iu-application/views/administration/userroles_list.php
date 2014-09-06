<script type="text/javascript">

function remove_userrole(id, name)
{
	iu_confirm('Are you sure you want to remove user role "'+name+'"?', function() {
		window.location.href = IU_SITE_URL + '/administration/userroles/remove/'+id;
	});
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage User Roles</h5>
                <span>If you add user to certain role it will adopt all permissions set for that role.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/users'); ?>" title="" class="button basic" style="margin: 5px;"><span>List all users</span></a>
                        <a href="<?php echo site_url('administration/userroles/add'); ?>" title="" class="button brownB" style="margin: 5px;"><span>Create new role</span></a>


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
            <div class="title"><h6><span class="icon-user"></span> User Roles</h6></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
            <thead>
            <tr>
            <th>Name</th>
            <th>Permissions</th>
			<th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($roles as $role): ?>
            <tr class="gradeA">
			<td><?php echo $role->name; ?></td>
			<td><?php
			$perms = $role->permission->get();

			$perms_arr = array();
			foreach ($perms as $p)
				$perms_arr[] = str_replace('_', ' ', $p->key);

			echo implode(', ', $perms_arr);
			?></td>
			<td class="actBtns">
				<a title="Edit" href="<?php echo site_url('administration/userroles/edit/'.$role->id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/edit.png" alt=""></a>
				<a title="Remove" href="javascript:;" onclick="remove_userrole(<?php echo $role->id; ?>, '<?php echo $role->name; ?>');" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/remove.png" alt=""></a>
			</td>
			</tr>
            <?php endforeach; ?>

            </tbody>
            </table>
        </div>

    </div>