<?php
$saveid = (empty($page)) ? "" : "/".$page->id;
$global_cache_time = (int)Setting::value('cache_duration', 0);
?>
<script type="text/javascript">

$(document).ready(function() {
	$('#tags').tagsInput({width:'100%', defaultText: 'add keyword'});
	$('#cache_duration').spinner({
		min: 0
		,numberFormat: "n0"
	});

	$('#cache_duration').spinner('<?php echo (isset($page) && $page->custom_caching) ? 'enable' : 'disable'; ?>');
});


function remove_content(id, name)
{
	if (id == 0)
	{
		iu_alert("You can't delete content that doesn't exist!");
		return;
	}
	else
	{
		iu_confirm('Are you sure you want to remove content "'+name+'"?\n\n<br/><br/>Note that removing content will remove it`s value from database!', function() {
			window.location.href = IU_SITE_URL+'/administration/contents/remove/'+id;
		});
	}
}


function toggle_cache(el)
{
	var $el = $(el);
	//read if checked
	var checked = $el.is(':checked');

	//enable/disable text input
	$('#cache_duration').attr('disabled', !checked);

	//set focus
	if (checked)
		$('#cache_duration').spinner( "enable" ).focus();
	else
		$('#cache_duration').spinner( "disable" ).blur();
}


function ajax_remove_cache(id)
{
	var url = IU_SITE_URL + '/administration/ajax/remove_cache/'+id;
	$('#cache_status').text('Cache status: removing...');
	$.get(url, function(json) {
		$('#cache_status').text(json.message);
	}, 'json');
}


function choose_template()
{
	iu_popup('<?php echo site_url('administration/templates/choose_popup?iu-popup=true'); ?>', 400, 450);
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
            <?php if (isset($page)): ?>
                <h5>Edit Page: <a href="<?php echo site_url($page->uri); ?>"><?php echo $page->uri; ?></a></h5>
            <?php else: ?>
				<h5>Add New Page</h5>
			<?php endif; ?>
                <span>Page properties are located on the left side. On the right side you can see additional options for each page.</span>
            </div>
            <div class="subnavtitle">
            <a style="margin-left:2px !important" href="javascript:;" onclick="remove_page(<?php echo $page->id; ?>, '<?php echo str_replace("'", "`", $page->title); ?>');" class="button blueB" style="margin: 5px;"><span>Delete Page</span></a>
            	<?php if (isset($page)): ?>
                  <a href="<?php echo site_url($page->uri); ?>" title="" class="button blueB" style="margin: 5px;" target="_blank"><span>Edit page live</span></a>
                <?php endif; ?>
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
<form id="iu-page-form" action="<?php echo site_url('administration/pages/save'.$saveid); ?>" method="post" class="form">

    <!-- Main content wrapper -->
    <div class="wrapper">

		<div class="twoOne">
				<fieldset>
					<div class="widget">
						<div class="title"><h6>Page properties</h6></div>

						<?php if (!isset($page)): ?>
						<div class="formRow">
                        	<label>Page URL:</label>
                        	<div class="formRight"><span class="ff" style="text-align: right !important;"></span><input type="text" name="uri" value="" />
                        	<span class="formNote">Please enter your page URL path. For example: <strong>about.html</strong></span></div>
                        	<div class="clear"></div>
                   		</div>
						<?php endif; ?>

						<div class="formRow">
                        	<label>Page title:</label>
                        	<div class="formRight"><input type="text" name="title" value="<?php echo empty($page) ? '': $page->title ; ?>" />
                        	<span class="formNote">This is the title of your page.</span></div>
                        	<div class="clear"></div>
                    	</div>

						<div class="formRow">
                        	<label>Page keywords:</label>
                        	<div class="formRight"><input type="text" id="tags" name="keywords" value="<?php echo empty($page) ? '': $page->keywords ; ?>" />
                        	<span class="formNote">Type your keyword and press enter.</span></div>
                        	<div class="clear"></div>
                    	</div>

						<div class="formRow">
                        	<label>Page description:</label>
                        	<div class="formRight"><textarea class="lim" rows="4" cols="" name="description"><?php echo empty($page) ? '': $page->description ; ?></textarea>
                        	<span class="formNote">This is the description of your page. Optimal Length for Search Engines is 155 characters.</span></div>
                        	<div class="clear"></div>
                    	</div>

                    	<?php if ((isset($page) && $user->can_edit_page($page)) || (!isset($page))): ?>
						<div class="formRow">
                        	<label>Page owner:</label>
                        	<div class="formRight searchDrop">
	                        	<select data-placeholder="<?php echo empty($page) ? 'Select page owner' : $page->user->name; ?>" class="chzn-select" style="width:350px;" tabindex="2" name="user">
	                            	<option value=""></option>
	                            	<?php foreach ($users as $us): ?>
	                            	<option value="<?php echo $us->id; ?>" <?php echo (isset($page) && ($us->id !== $page->user_id)) ? '' : 'selected="selected"' ; ?>><?php echo $us->name; ?></option>
	                           		<?php endforeach; ?>
	                        	</select>
                        	</div>
                        	<div class="clear"></div>
                    	</div>
                    	<?php endif; ?>

                    	<?php if (empty($page) || ($page->user_id == $user->id) || $user->can('edit_templates')): ?>
						<div class="formRow">
                        	<label>Template file:</label>
                        	<div class="formRight searchDrop">
                        		<div class="oneThree">
	                        		<select data-placeholder="<?php echo empty($page) ? '' : $page->file->path; ?>" class="chzn-select" style="width:350px;" tabindex="2" name="template" id="template">
		                            	<option value=""></option>
		                            	<?php foreach ($files as $file): ?>
		                            	<option value="<?php echo $file->id; ?>" <?php echo (isset($page) && ($file->id !== $page->file_id)) ? '' : 'selected="selected"' ; ?>><?php echo $file->path; ?></option>
		                           		<?php endforeach; ?>
		                        	</select>
	                        	</div>
	                        	<div class="oneThree">&nbsp;</div>
                        	</div>
                        	<div class="clear"></div>
                    	</div>
                    	<?php endif; ?>


					</div>
				</fieldset>
        </div>

                <?php if (isset($divs)): ?>
        <div class="oneThree">
            <div class="widget">
                <div class="title"><h6>Page contents</h6>
                <div class="num"><a href="#" class="blueNum2" style="cursor:text">Global</a></div>
                <div class="num"><a href="#" class="redNum2" style="cursor:text">Nonexistent</a></div>
                </div>
          <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
          <thead>
          <tr>
          <th class="sortCol"><div>ID<span></span></div></th>
          <th class="sortCol"><div>Last modified<span></span></div></th>
          <th class="">Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($divs as $div_id): ?>
          <?php
            $content = Content::factory()->where('div', $div_id)
              ->group_start()
                ->where_related_page('id', $page->id)
                ->or_where('is_global', true)
              ->group_end()
              ->limit(1)->get();

          ?>
          <tr data-rel="<?php echo ($content->exists()) ? $content->id : 0 ; ?>" class="grade<?php echo (!$content->exists()) ? 'X' : (!empty($content->is_global) ? 'C' : 'A') ; ?>">
          <td class="center"><?php echo $div_id; ?></td>
                <td class="center"><?php echo (!$content->exists()) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), ($content->updated==0)?$content->created:$content->updated).'">'.relative_time(($content->updated==0)?$content->created:$content->updated) . '</span> ' . __('by %s', User::factory($content->editor_id)->name); ?></td>
          <td class="actBtns">
          <a title="Edit" href="<?php echo $content->exists() ? site_url('administration/contents/edit/'.$content->id.'/'.$div_id) : site_url('administration/contents/add/'.$page->id.'/'.$div_id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
          <a title="Remove" onclick="remove_content(<?php echo $content->exists() ? $content->id : 0; ?>, '<?php echo str_replace("'", "`", $content->exists() ? $content->div : 'undefined'); ?>');" href="javascript:;" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
          </td>
          </tr>
          <?php endforeach; ?>

          </tbody>
          </table>
               </div>
        </div>
        <?php endif; ?>

        <?php if (empty($page) || $user->can_edit_page($page)): ?>
        <div class="oneThree">
           	<fieldset>
				<div class="widget">
					<div class="title"><h6>Page editors</h6></div>

					<div class="formRow">

              				<select name="editors[]" data-placeholder="Choose editors" class="chzn-select" multiple="multiple" tabindex="6">
                                <option value=""></option>
                                <?php foreach ($roles as $r): ?>
                                <?php $users = $r->user->get(); ?>
                                <optgroup label="<?php echo strtoupper($r->name); ?>">
                                	<?php foreach ($users as $u): ?>
                                	<?php if (isset($page) && ($u->id == $page->user_id)) continue; ?>
                                    <option value="<?php echo $u->id; ?>" <?php echo (isset($page) && ($u->is_related_to('assigned_pages', $page->id))) ? ' selected="selected"' : '' ;?>><?php echo $u->name; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>

                       	<span class="formNote">These users can edit page and all it's contents. To allow users editing of only some contents, edit particular content and set it's editors instead!</span>
                       	<div class="clear"></div>
                   	</div>

				</div>
			</fieldset>
        </div>
        <div class="oneThree">
           	<fieldset>
				<div class="widget">
					<div class="title"><h6>Page caching</h6></div>

					<div class="formRow">
						<label>Cache:</label>
                       	<div class="formRight">
                       		<input onclick="toggle_cache(this);" type="checkbox" id="custom_caching" name="custom_caching" value="yes" <?php echo (isset($page) && $page->custom_caching) ? 'checked="checked"' : '' ; ?> /> <label for="custom_caching">Enable custom caching</label><br />
                       		<span class="formNote">If not enabled the page will <?php if ($global_cache_time>0): ?>be cached for <?php echo $global_cache_time; ?> minutes<?php else: ?>NOT be cached<?php endif; ?> (default).</span>
                       	</div>
                       	<div class="clear"></div>
                   	</div>

             		<div class="formRow">
						<label>Duration:</label>
                       	<div class="formRight">
                       		<input onchange="cache_status();" type="text" id="cache_duration" name="cache_duration" value="<?php echo (!isset($page)) ? 0 : $page->custom_caching_duration; ?>" <?php echo (isset($page) && !$page->custom_caching) ? 'disabled="disabled"' : '' ; ?> /> <span class="formNote">Duration is set in minutes. There are 60 minutes in one hour, 1440 in one day and 10080 minutes in one week.</span>
                       	</div>
                       	<div class="clear"></div>
                   	</div>
                   	<?php if (isset($page)): ?>
                   	<?php
                   	$system->load->library('cache');
                   	$system->cache->set_uri($page->uri);


                   	if ($system->cache->cache_exists($page->cache_duration()*60))
                   	{
                   		$cached = true;
                   		$cache_date = date(Setting::value('datetime_format', 'F j, Y @ H:i'), filemtime($system->cache->get_cache_file()));
                   	}
                   	else
                   	{
                   		$cached = false;
                   		$cache_date = 'cache doesn\'t exist';
                   	}

                   	?>
                   	<p id="cache_status">Cache status: <?php if ($cached): ?>saved on <?php echo $cache_date; ?> &bull; <a href="javascript:;" onclick="ajax_remove_cache(<?php echo $page->id; ?>);">Remove</a> <?php else: ?>not existing<?php endif; ?></p>
                   	<?php endif; ?>
				</div>
			</fieldset>
        </div>
        <?php endif; ?>



        <div class="clear"></div>

    </div>

	</form>
