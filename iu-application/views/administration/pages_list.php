<script type="text/javascript">

function remove_page(id, name)
{
	iu_confirm('Are you sure you want to remove page "'+name+'"?\n\n<br/><br/>Note that removing page from here won`t remove page template file, only it`s reference in the database.', function() {
		window.location.href = IU_SITE_URL + '/administration/pages/remove/'+id;
	});
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Pages</h5>
                <span>Page is a document suitable for the viewing through the Internet Browser. Click on name of the page to edit details.</span>
            </div>
            <div class="subnavtitle">
            	<?php if ($this->user->can('add_pages')): ?>
                        <a href="<?php echo site_url('administration/pages/add'); ?>" title="" class="button basic" style="margin: 5px;"><span>Create new page</span></a>
                <?php endif; ?>
						<a href="<?php echo site_url(); ?>" class="button blueB" style="margin: 5px;" target="_blank"><span>Edit site live</span></a>


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
            <div class="title"><h6><span class="icon-copy3"></span> Pages</h6><div class="num"><?php echo $pages->result_count(); ?></div></div>
            <table cellpadding="0" cellspacing="0" border="0" class="display dTable">
            <thead>
            <tr>
            <th>Title</th>
            <th>Page Address</th>
            <th>Owner</th>
            <th>Last modified</th>
            <th>Contents</th>
			<th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php $l = 60; foreach ($pages as $page): ?>
            <tr class="gradeA">
            <td><a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>" ><span<?php echo (strlen($page->title)>$l) ? ' class="tipW" title="'.$page->title.'"' : "" ; ?>><?php echo empty($page->title) ? "(untitled)" : ellipsize($page->title, $l); ?></span></a><span style="display: none;"><?php echo $page->title; ?></span></td>
			<td><span<?php echo (strlen($page->uri)>$l) ? ' class="tipW" title="'.$page->uri.'"' : "" ; ?>><a href="<?php echo site_url($page->uri); ?>" class="highlightLink"><?php echo ellipsize($page->uri, $l, .5); ?></a></span><span style="display: none;"><?php echo $page->uri; ?></span></td>
			<td class="center"><?php echo $page->user->get()->name; ?></td>
			<td class="center"><?php echo empty($page->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $page->updated).'">'.relative_time($page->updated) . '</span> ' . __('by %s', User::factory($page->editor_id)->name); ?></td>
            <td class="center"><?php echo count($page->get_div_ids()); ?> contents found</td>
			<td class="actBtns">
				<a title="Edit" href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>"  class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
                <a title="View page live" style="margin-left:2px !important" href="<?php echo site_url($page->uri); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/globe.png" alt=""></a>
				<a title="Remove" style="margin-left:2px !important" href="javascript:;" onclick="remove_page(<?php echo $page->id; ?>, '<?php echo str_replace("'", "`", $page->title); ?>');" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
			</td>
			</tr>
            <?php endforeach; ?>

            </tbody>
            </table>
        </div>

    </div>