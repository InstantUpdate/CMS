<?php
$saveid = (empty($role)) ? "" : "/".$role->id;
?>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5><?php echo empty($role) ? "Add New User Role" : "Edit User Role" ; ?></h5>
                <span>Here you can set user roles with predefined permissions.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/userroles'); ?>" title="" class="button basic" style="margin: 5px;"><span>User roles</span></a>
                        <a href="javascript:;" onclick="$('#iu-page-form').submit();" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>

            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

	<div class="wrapper">
	    <?php $template->load_template('notifications'); ?>
	</div>

    <!-- Main content wrapper -->
    <div class="wrapper">

		<div class="twoOne">
			<form id="iu-page-form" action="<?php echo site_url('administration/userroles/save'.$saveid); ?>" method="post" class="form">
				<fieldset>
					<div class="widget">
						<div class="title"><h6>User Role <?php echo empty($role) ? '' : $role->name; ?></h6></div>

						<div class="formRow">
                        	<label>Role name:</label>
                        	<div class="formRight"><input type="text" name="name" value="<?php echo empty($role) ? '' : $role->name ; ?>" />
                        	<span class="formNote">This is the name of your user role group.</span></div>
                        	<div class="clear"></div>
                    	</div>

						<div class="formRow">
                        	<label>Permissions:</label>
                        	<div class="formRight">
                        	<?php foreach ($permissions as $p): ?>
                        		<input type="checkbox" <?php echo (empty($role) || !$p->is_related_to($role)) ? '' : 'checked="checked"'; ?> name="permissions[]" id="perm_<?php echo $p->key; ?>" value="<?php echo $p->id; ?>" /><label for="perm_<?php echo $p->key; ?>"><?php echo $p->name; ?></label> <br /><br />
							<?php endforeach; ?>
							</div>
                        	<div class="clear"></div>
                    	</div>



					</div>
				</fieldset>
     	  	</form>
        </div>

       	<div class="oneThree">
           	<div class="widget">
               	<div class="title"><h6>Users with this role</h6></div>
					<table cellpadding="0" cellspacing="0" border="0" class="display dTable">
					<thead>
					<tr>
					<th class="sortCol"><div>Name</div></th>
					<th class="">Actions</th>
					</tr>
					</thead>
					<tbody>
					<?php
					$users = empty($role) ? null : $role->user->get();
					if (!empty($users) && $users->result_count() > 0):
						foreach ($users as $u):
					?>
					<tr class="gradeA">
					<td class="center"><?php echo $u->name; ?></td>
					<td class="actBtns">
						<a title="Edit" href="<?php echo site_url('administration/users/edit/'.$u->id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/edit.png" alt=""></a>
					</td>
					</tr>
					<?php
						endforeach;
					endif;
					?>

					</tbody>
					</table>
               </div>
        </div>
        <div class="clear"></div>

    </div>