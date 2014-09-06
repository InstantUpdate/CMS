<?php
$saveid = (empty($content)) ? "" : "/".$content->id;
?>

<script type="text/javascript">

function save($goback)
{
	var $content = window.oEdit1.getXHTMLBody();

	var editors_select = $('#editors');

	if (editors_select.length > 0)
	{
		var $editors = $.map($('#editors option:selected'),function(option) {
    		return option.value;
		});
	}
	else
	{
		var $editors = [];
	}


	$.post('<?php echo site_url("administration/contents/save".$saveid); ?>', { html: $content, pid: <?php echo $page->id; ?>, editors: $editors }, function(data)
	{
		if (iu_in_iframe())
		{
			//alert(window.parent);
			$('#<?php echo $content->div; ?>', window.parent.document).html($content);
			$('#<?php echo $content->div; ?>', window.parent.document).data('id', <?php echo $content->id; ?>);
			$(".jackbox-close", window.parent.document).trigger("click.jackbox");
		}
		else
		{
			var url = window.location.href;

			if (url.indexOf("?")>-1)
				url = url.substr(0, url.indexOf("?"));

			window.location.href = url + '?rand=' +  Math.floor((Math.random()*10000)+1); ;
		}

			//window.location.reload();
	});

}

function repeatable_remove(id, name)
{
	iu_confirm('Are you sure you want to remove "'+name+'"?\n\n<br/><br/>Note that this cannot be undone.', function() {
		window.location.href = IU_SITE_URL + '/administration/repeatables/remove/'+id;
	});
}

</script>

<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Edit Repeatable Content: <a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>"><?php echo basename($page->uri); ?></a> &gt; <a href="<?php echo site_url($page->uri); ?>?iu-highlight=<?php echo $content->div; ?>"><?php echo $content->div; ?></a></h5>
                <span>This is the list of items that repeat on content ID "<?php echo $content->div; ?>". You can also set options for this content. </span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>" title="" class="button basic" style="margin: 5px;"><span>Edit page</span></a>
                        <a href="<?php echo site_url($page->uri); ?>" title="" class="button blueB" style="margin: 5px;"><span>Edit page live</span></a>
                        <a href="javascript:;" onclick="$('#iu-repeatable-form').submit();" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>

            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>

	<div class="wrapper">
	    <?php $template->load_template('notifications'); ?>
	</div>
<?php else: ?>
<style type="text/css">
body div {
	margin: 0 !important;
}
</style>
<?php endif; ?>
<form id="iu-repeatable-form" action="<?php echo site_url('administration/contents/save'.$saveid); ?>" method="post" class="form">
<input type="hidden" name="pid" value="<?php echo $page->id; ?>" />
	<!-- Main content wrapper -->
    <div class="wrapper">

<?php if ($template->config['has_header']): ?>
		<div class="twoOne">
<?php else: ?>
		<div>
<?php endif; ?>
					<div class="widget" style="min-height: 600px;">
						<div class="title"><h6>Items in content "<?php echo $content->div; ?>"</h6><div class="num"><a href="<?php echo site_url('administration/repeatables/add/'.$content->id); ?>" class="greyNum">Add new...</a></div></div>
						<table cellpadding="0" cellspacing="0" width="100%" class="sTable">
                        <thead>
                            <tr>
                                <td>Title</td>
                                <td>Published</td>
                                <td>Published by</td>
                                <td>Actions</td>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                        	$items = $content->repeatableitems->order_by('timestamp DESC')->get();
                        	foreach ($items as $item):
                        ?>
                            <tr>
                                <td><a href="<?php echo site_url('administration/repeatables/edit/'.$item->id); ?>"><?php echo $item->title; ?></a></td>
								<td align="center"><?php echo empty($item->timestamp) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $item->timestamp).'">'.relative_time($item->timestamp) . '</span> '; ?></td>
                                <td align="center"><?php echo $item->user->get()->name; ?></td>
                                <td class="actBtns">
									<a title="Edit" href="<?php echo site_url('administration/repeatables/edit/'.$item->id); ?>"  class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
									<a title="Remove" style="margin-left:2px !important" href="javascript:;" onclick="repeatable_remove(<?php echo $item->id; ?>, '<?php echo str_replace("'", "`", $item->title); ?>');" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
								</td>
                            </tr>
                        <?php
                        	endforeach;
                       	?>

                        </tbody>
                    </table>

					</div>
        </div>

<?php $template->load_template('contents_sidebar'); ?>


    </div>
</form>