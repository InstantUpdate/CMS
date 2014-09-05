<?php
$saveid = (empty($userEdited)) ? "" : "/".$userEdited->id;
?>

<script type="text/javascript">

function iu_load_permissions(id)
{
	$.get(IU_SITE_URL+'/administration/ajax/permissions_for/'+id, function(data)
	{
		//remove all permissions
		$('#permissions input[type="checkbox"]').each(function() {
			$(this).removeAttr('checked');
			$(this).closest('.checker > span').removeClass('checked');
		});

		for (i in data)
		{
			var el = '#perm_'+data[i];

			$(el).attr('checked', 'checked');
			$(el).closest('.checker > span').addClass('checked');
		}


	}, 'json');
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5><?php echo empty($userEdited) ? "Add New User" : "Edit User" ; ?></h5>
                <span>Here you can manage user profiles.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/users'); ?>" title="" class="basic button" style="margin: 5px;"><span>List all users</span></a>
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
			<form id="iu-page-form" action="<?php echo site_url('administration/users/save'.$saveid); ?>" method="post" class="form" class="validate" enctype="multipart/form-data">
				<fieldset>
		<div class="twoOne">

					<div class="widget">
						<div class="title"><h6>User <?php echo empty($userEdited) ? '' : $userEdited->name; ?></h6></div>

						<div class="formRow">
                        	<label>User name:</label>
                        	<div class="formRight"><input type="text" class="validate[required,minSize[5]]" name="name" value="<?php echo empty($userEdited) ? '' : $userEdited->name ; ?>" />
                        	<span class="formNote">This is the name of the user.</span></div>
                        	<div class="clear"></div>
                    	</div>

            			<div class="formRow">
                        	<label>E-mail address:</label>
                        	<div class="formRight"><input type="text" class="validate[required,custom[email]]" name="email" value="<?php echo empty($userEdited) ? '' : $userEdited->email ; ?>" />
                        	<span class="formNote">This is contact e-mail address of the user.</span></div>
                        	<div class="clear"></div>
                    	</div>

                    	<?php if ($user->can('manage_users')): ?>
						<div class="formRow">
                        	<label>User role:</label>
                        	<div class="formRight searchDrop">
	                        	<select onchange="iu_load_permissions($(this).val());" data-placeholder="<?php echo empty($userEdited) ? "Choose user role" : $userEdited->userrole->name; ?>" class="chzn-select" style="width:350px;" tabindex="2" name="userrole_id">
	                            	<option value=""></option>
	                            	<?php foreach ($roles as $role): ?>
	                            	<option value="<?php echo $role->id; ?>" <?php echo ($role->id !== $user->userrole->user_id) ? '' : 'selected="selected"' ; ?>><?php echo $role->name; ?></option>
	                           		<?php endforeach; ?>
	                        	</select>
                        	</div>
                        	<div class="clear"></div>
                    	</div>
                    	<?php endif; ?>

                    	<div class="formRow">
                        	<label>Password:</label>
                        	<div class="formRight"><input type="password" class="validate[required]" name="password" value="" />
                        	<span class="formNote">Set user's password.</span></div>
                        	<div class="clear"></div>
                    	</div>

            			<div class="formRow">
                        	<label>Confirm password:</label>
                        	<div class="formRight"><input type="password" class="validate[required,equals[password]]" name="password2" value="" />
                        	<span class="formNote">This field must match above field.</span></div>
                        	<div class="clear"></div>
                    	</div>


                    	<?php if ($user->can('manage_users')): ?>
                    	<div class="formRow">
	                        <label>Active:</label>
	                        <div class="formRight"><input type="checkbox" <?php echo (empty($userEdited) || !$userEdited->active) ? '' : 'checked="checked"'; ?> name="active" id="active" value="true" /><label for="active">yes</label></div>
	                        <div class="clear"></div>
                    	</div>
                    	<?php endif; ?>

                    	<div class="formRow">
	                        <label>User picture:</label>
	                        <div class="formRight"><?php if (!empty($userEdited->picture)): ?><img src="<?php echo $userEdited->get_profile_picture_url(); ?>" alt="" /><br /><br /><?php endif; ?>
	                        <div id="filebutton"><input type="file" name="picture" id="picture" /></div>
                        	<span class="formNote">Select the file and save the page.</span></div>
	                        <div class="clear"></div>
                    	</div>

					</div>

        </div>

        <?php if ($user->can('manage_users')): ?>
       	<div class="oneThree">
           	<div class="widget">
               	<div class="title"><h6>Permissions</h6></div>



						<div class="formRow" style="margin: 5px !important; padding: 5px !important;" id="permissions">
                        	<?php foreach ($permissions as $p): ?>
                        		<input type="checkbox" <?php echo (empty($userEdited) || !$p->is_related_to($userEdited)) ? '' : 'checked="checked"'; ?> name="permissions[]" id="perm_<?php echo $p->key; ?>" value="<?php echo $p->id; ?>" /><label for="perm_<?php echo $p->key; ?>"><?php echo $p->name; ?></label> <br /><br />
							<?php endforeach; ?>
                        	<div class="clear"></div>
                    	</div>


               </div>
        </div>
        <?php endif; ?>

        				</fieldset>
     	  	</form>

        <div class="clear"></div>

    </div>