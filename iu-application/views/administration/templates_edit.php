<?php
//parameteres:
// $user - user info object
// $file - page to edit (empty if new file is being added)

$saveid = (empty($file)) ? "" : "/".$file->id;

?>
<script type="text/javascript" charset="utf-8">

var path = '<?php echo $file->path; ?>';

function duplicate()
{
	var filename = '<?php echo basename($file->path); ?>';
	var dir = '<?php echo $file->directory(); ?>';
	var exts = filename.split('.');
	var ext = exts[exts.length-1];

	var newname = filename.replace(new RegExp('\.'+ext), '-copy');

	$.msgbox("<p>In order to duplicate this template you must provide name of the new template.</p>", {
		type    : "prompt",
		inputs  : [
			{type: "text", label: "Name of the new file:", value: newname, required: true},
		],
		buttons : [
			{type: "submit", value: "OK"},
			{type: "cancel", value: "Exit"}
		]
	}, function(name) {
		if (name)
		{
			name = name.replace(/\s/g, '-');

			var redir = '<?php echo site_url('administration/templates/duplicate/'.$file->id); ?>/'+name+'.'+ext;

			window.location.href = redir;
		}
	});
}

function save()
{
	$('#form_edit').submit();
}


function revert(fid, rid)
{
	iu_confirm("Are you sure you want to revert revision #"+rid+"?", function() {
		location.href = IU_SITE_URL + '/administration/templates/revert/'+fid+'/'+rid;
	});
}

<?php
$ext = end(explode('.', $file->path));
$syntax = 'php';

if ($ext == 'js')
	$syntax = 'javascript';
elseif ($ext == 'css')
	$syntax = 'css';
elseif ($ext == 'xml')
	$syntax = 'xml';
?>

var editor;

$(document).ready(function() {

	var html = $('#editarea').text();
	html = html.replace('[/textarea]', '</'+'textar'+'ea>');
	html = html.replace('[/form]', '</'+'for'+'m>');
	$('#editarea').text(html);

	editor = CodeMirror.fromTextArea(document.getElementById("editarea"), {
		lineNumbers: true,
		matchBrackets: true,
		mode: "<?php echo $syntax; ?>",
		indentUnit: 4,
		indentWithTabs: true,
		enterMode: "keep",
		tabMode: "shift",
		lineWrapping: true
	});

	$('.revTable').dataTable({
		"bJQueryUI": true,
		"bAutoWidth": false,
		"sPaginationType": "full_numbers",
		"sDom": '<"H"l>t<"F"fp>',
		"aaSorting": [[0, 'desc']],
		"iDisplayLength": 5
	});
});

$(window).load(function () {
	editor.refresh();
});
</script>

<?php if ($template->config['has_header']): ?>
	<!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Edit Template: <a href="" style="cursor:text"><?php echo $file->path; ?></a></h5>
                <span>Changing this template can affect all pages connected to it.</span>
            </div>
            <div class="subnavtitle">
                        <a href="javascript:;" onclick="duplicate();" title="" class="button basic" style="margin: 5px;"><span>Duplicate</span></a>
                        <a href="javascript:;" onclick="save();" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

    <!-- Main content wrapper -->
    <div class="wrapper">
    <?php $template->load_template('notifications'); ?>
    </div>

	<div class="wrapper">
		<!-- Dynamic table -->
		<div class="twoOne">
	        <div class="widget" style="margin-bottom:80px">
	            <div class="title"><img src="<?php echo $template->base_url(); ?>images/icons/dark/docs.png" alt="" class="titleIcon" /><h6>Editing template file <?php echo $file->path; ?></h6></div>

				<form style="clear:left;" action="<?php echo site_url('administration/templates/save'.$saveid); ?>" method="post" id="form_edit">
					<textarea class="notranslate" id="editarea" name="editarea" style="font-family: 'Open Sans', sans-serif; font-size: 12px; width: auto; height: <?php echo ($template->config['in_popup']) ? "300" : "500" ;?>px"><?=(empty($file)) ? "" : str_replace(array('</textarea>', '</form>'), array('[/textarea]', '[/form]'), $file->contents()) ?></textarea>
				</form>


	        </div>
                    </div>

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
		            <?php $revs = FileRevision::factory()->where_related_file('id', $file->id)->order_by('created DESC')->get();
	            	foreach ($revs as $rev): ?>
		            <tr class="gradeA">
		            	<td><?php echo $rev->id; ?></td>
						<td><?php echo empty($rev->created) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $rev->created).'">'.relative_time($rev->created) . '</span> ' . __('by %s', $rev->user->get()->name); ?></td>
						<td class="actBtns2">
							<a title="Compare with this version" href="<?php echo site_url('administration/templates/diff/'.$file->id.'/'.$rev->id); ?>" class="iu-btn">Compare</a>
                           <a title="Revert to this version" style="margin-left:2px !important" href="javascript:;" onclick="revert(<?php echo $file->id?>, <?php echo $rev->id; ?>);" class="iu-btn danger">Revert</a>

						</td>
					</tr>
		            <?php endforeach; ?>

	            </tbody>
	            </table>

	        </div>
		</div>

        <div class="oneThree">
            	<div class="widget">
                	<div class="title"><h6>This template affects following pages:</h6></div>
                    <p><span>
                <?php
				if (empty($pages))
                {
                	echo "(no pages associated to this template yet)";
				}
				else
				{
					$i = 0;
					foreach ($pages as $page)
					{
						$i++;
						echo '<a class="highlightLink tipN" style="margin-top: 6px" title="'.$page->title.'" href="'.site_url('administration/pages/edit/'.$page->uri).'">'.$page->uri.'</a>';

						if ($i < $pages->result_count())
							echo ' &nbsp; ';
					}

				}
				?></span></p>
                </div>
            </div>

    </div>