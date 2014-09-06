<script type="text/javascript">

function remove(id, name)
{
	if (id == 0)
		return;

	iu_confirm('Are you sure you want to remove content "'+name+'"?\n\n<br/><br/>Note that removing content will also remove all data associated to it!', function() {
		window.location.href = IU_SITE_URL + '/administration/contents/remove/'+id;
	});
}

function revert(fid, rid)
{
	iu_confirm("Are you sure you want to revert revision #"+rid+"?", function() {
		location.href = IU_SITE_URL + '/administration/contents/revert/'+fid+'/'+rid;
	});
}

$(document).ready(function() {
	$('.revTable').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"H"l>t<"F"fp>',
		"aaSorting": [[0, 'desc']],
		"iDisplayLength": 5
	});
});

$(window).load(function() {
	$('.chzn-search').hide();
});
</script>

<?php if ($template->config['has_header']): ?>
	<?php if ($user->owns_page($content->page->get()) || $user->can('edit_all_pages')): ?>
        <div class="oneThree">
           	<fieldset>
				<div class="widget">
					<div class="title"><h6>Content settings</h6></div>

<!-- 					<div class="formRow">
                       	<label>Type:</label>
                       	<div class="formRight">
							<select id="type" name="type" class="chzn-select" style="width:160px;">
								<?php foreach ($types as $t): ?>
                                <option value="<?php echo $t->id; ?>" <?php echo ($t->id == $content->contenttype_id) ? 'selected="selected"' : '' ; ?>><?php echo $t->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="formNote">Choose news/blog if you want to create repeatable content.</span>
                       	</div>
                       	<div class="clear"></div>
                   	</div> -->

					<div class="formRow">
                       	
                       	<div class="formRight">
                       		<input class="checker" type="checkbox" id="global" name="global" value="yes" <?php echo ($content->is_global) ? 'checked="checked"' : '' ; ?> /> <label for="global">GLOBAL on all pages containing ID "<?php echo $content->div; ?>".</label>
                       	</div>
                       	<div class="clear"></div>
                   	</div>
                   						<div class="formRow">

              				<select name="editors[]" id="editors" data-placeholder="Choose editors" class="chzn-select" multiple="multiple" tabindex="6">
                                <option value=""></option>
                                <?php foreach ($roles as $r): ?>
                                <?php $users = $r->user->get(); ?>
                                <optgroup label="<?php echo strtoupper($r->name); ?>">
                                	<?php foreach ($users as $u): ?>
                                	<?php if ($u->id == $page->user_id) continue; ?>
                                    <option value="<?php echo $u->id; ?>" <?php echo ($u->is_related_to('assigned_contents', $content->id)) ? ' selected="selected"' : '' ;?>><?php echo $u->name; ?></option>
                                    <?php endforeach; ?>
                                </optgroup>
                                <?php endforeach; ?>
                            </select>

                       	<span class="formNote">Selected users can edit this content regardless of their page/user permissions! To allow users to edit all contents on a certain page, edit that page and set it's editors.</span>
                       	<div class="clear"></div>
                   	</div>
				</div>
			</fieldset>
        </div>

        <?php if ($content->contenttype->classname == 'Html'): ?>
        <div class="oneThree">
       		<div class="widget">
	            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Revision history</h6></div>
				<table cellpadding="0" cellspacing="0" border="0" class="display revTable">
	            <thead>
		            <tr>
		            	<th>#</th>
			            <th>Created</th>
						<th>Actions</th>
		            </tr>
	            </thead>
	            <tbody>
		            <?php $revs = ContentRevision::factory()->where_related_content('id', $content->id)->order_by('created DESC')->get();
		            foreach ($revs as $rev): ?>
		            <tr class="gradeA">
		            	<td><?php echo $rev->id; ?></td>
						<td><?php echo empty($rev->created) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $rev->created).'">'.relative_time($rev->created) . '</span> ' . __('by %s', $rev->user->get()->name); ?></td>
						<td class="actBtns2">
							<a title="Compare with this version" href="<?php echo site_url('administration/contents/diff/'.$content->id.'/'.$rev->id); ?>" class="iu-btn">Compare</a>
                            <a title="Revert to this version" style="margin-left:2px !important" href="javascript:;" onclick="revert(<?php echo $content->id; ?>, <?php echo $rev->id; ?>);" class="iu-btn danger">Revert</a>

						</td>
					</tr>
		            <?php endforeach; ?>

	            </tbody>
	            </table>

	        </div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

       	<div class="oneThree" style="display: none;">
           	<div class="widget">
               	<div class="title"><h6>Other contents on page <span title="<?php echo str_replace('"', '\"', $page->title); ?>" class="tipN"><?php echo basename($page->uri); ?></span></h6>
                <div class="num"><a href="#" class="blueNum" style="cursor:text">Global</a></div>
                <div class="num"><a href="#" class="redNum" style="cursor:text">Nonexistent</a></div>
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
						if ($div_id == $content->div)
							continue;

						$contentO = Content::factory()->where('div', $div_id)
								->group_start()
									->where_related_page('id', $page->id)
									->or_where('is_global', true)
								->group_end()
								->limit(1)->get();

						if (!$user->can_edit_content($contentO))
							continue;
					?>
					<tr class="grade<?php echo (!$contentO->exists()) ? 'X' : (!empty($contentO->is_global) ? 'C' : 'A') ; ?>">
					<td class="center"><?php echo $div_id; ?></td>
					<td class="center"><?php echo empty($contentO->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $contentO->updated).'">'.relative_time($contentO->updated) . '</span> ' . __('by %s', User::factory($contentO->editor_id)->name); ?></td>					<td class="actBtns">
					<a title="Edit" href="<?php echo $contentO->exists() ? site_url('administration/contents/edit/'.$contentO->id.'/'.$div_id) : site_url('administration/contents/add/'.$page->id.'/'.$div_id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
					<a title="Remove" onclick="remove(<?php echo $content->exists() ? $content->id : 0; ?>, '<?php echo str_replace("'", "`", $content->exists() ? $content->div : 'undefined'); ?>');" href="javascript:;" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
					</td>
					</tr>
					<?php endforeach; ?>

					</tbody>
					</table>
               </div>
        </div>

        <div class="clear"></div>
<?php endif;  ?>