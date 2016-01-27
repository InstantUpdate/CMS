<script type="text/javascript">

function remove_gallery(id, name)
{
	iu_confirm('Are you sure you want to remove gallery content "'+name+'"?\n\n<br/><br/>Note that this cannot be undone.', function() {
		window.location.href = IU_SITE_URL + '/administration/contents/remove/'+id+'/galleries';
	});
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Galleries</h5>
                <span>Here you can manage all galleries in your website.</span>
            </div>
            <div class="subnavtitle">
                     <a href="<?php echo site_url(); ?>" class="button blueB" style="margin: 5px;" target="_blank"><span>Edit pages live</span></a>
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
            <div class="title"><h6><span class="icon-images"></span> Gallery contents</h6><div class="num"><a href="" style="cursor: text" class="greyNum"><?php echo $galleries->result_count(); ?></a></div></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
            <thead>
            <tr>
            <th>Content Name</th>
            <th>Page Address</th>
            <th>Last modified</th>
			<th>Images</th>
			<th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php $l = 60; foreach ($galleries as $gal): ?>
            <?php
            $page = $gal->page->get();
            if (!$user->can_edit_content($gal))
            	continue;
            ?>

            <tr class="gradeA">
            <td><span><a href="<?php echo site_url('administration/contents/edit/'.$gal->id.'/'.$gal->div); ?>"><?php echo $gal->div; ?></a></span></td>
			<td><span<?php echo (strlen($page->uri)>$l) ? ' class="tipW" title="'.$page->uri.'"' : "" ; ?>><a href="<?php echo site_url($page->uri); ?>" class="highlightLink"><?php echo ellipsize($page->uri, $l, .5); ?></a></span><span style="display: none;"><?php echo $page->uri; ?></span></td>
            <td class="center"><?php echo empty($gal->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $gal->updated).'">'.relative_time($gal->updated) . '</span> ' . __('by %s', User::factory($gal->editor_id)->name); ?></td>
			<td class="center"><?php $fnd = $gal->galleryitem->get()->result_count(); echo $fnd; ?> image<?php echo($fnd<>1)?'s':''; ?> found</td>
            <td class="actBtns">
				<a title="Edit" href="<?php echo site_url('administration/contents/edit/'.$gal->id.'/'.$gal->div); ?>"  class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
                <a title="View live" style="margin-left:2px !important" target="_blank" href="<?php echo site_url($page->uri); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/globe.png" alt=""></a>
				<a title="Remove" style="margin-left:2px !important" href="javascript:;" onclick="remove_gallery(<?php echo $gal->id; ?>, '<?php echo str_replace("'", "`", $gal->div); ?>');" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
			</td>
			</tr>
            <?php endforeach; ?>
            </tbody>
            </table>
        </div>

    </div>
