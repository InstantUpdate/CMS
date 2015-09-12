<script type="text/javascript">

function remove(id, name)
{
	iu_confirm('Are you sure you want to remove template file "'+name+'"?\n\n<br/><br/>Note that removing template from here won`t remove template file, only it`s reference in the database.', function() {
		window.location.href = IU_SITE_URL + '/administration/templates/remove/'+id;
	});
}

var iu_file_browser = null;

$(document).ready(function(){
	window.iu_file_browser = $('#elfinder').elfinder({
		url : '<?php echo site_url('administration/connector/browse'); ?>'  // connector URL (REQUIRED)
		,defaultView: 'list'
		,resizable: false
		,handlers : {
	        dblclick : function(event, elfinderInstance) {

				var parts = event.data.file.split('_');
				var filename = Base64.decode(parts[1]).replace(/\\/g, '/');

				if (filename.indexOf('.') > -1)
				{
					var extparts = filename.split('.');
					var ext = extparts[extparts.length-1].toLowerCase();

					var arr = filename.split('/');
					var basename = arr[arr.length-1];
					//var path = '/'.filename.replace(basename, '');

					//alert(path);

					var image_pattern = /jpe?g|png|gif/g;
					var text_pattern = /php[1-9]?|p?html?|log|tpl|css|js|txt|ini|xml|htaccess/g;

					if (image_pattern.test(ext))
					{
						//image
						var img_url = $.trim(IU_BASE_URL + filename);

						//img_url = img_url.replace(/loc/, 'org'); //cs local fix
						img_url = encodeURI(img_url).replace(/%00/gi, '');

						var exit = encodeURI(IU_SITE_URL+'/popup/close');
						var target = encodeURI(IU_SITE_URL+'/administration/images/save_reload');
						var title = encodeURI(basename.replace('.'+ext, '').replace(/_/gi, ' ').replace(/-/gi, ' '));

						//popup editor
						iu_popup_fs("http://pixlr.com/express/?referrer=IU4&exit="+exit+"&image="+img_url+"&target="+target+"&title="+title);


					}
					else if(text_pattern.test(ext))
					{
						//txt
						var redir = '<?php echo site_url('administration/templates/edit'); ?>/'+filename
						window.location.href = redir;
					}
					else
					{
						window.location.href = IU_BASE_URL+filename;
					}

					event.preventDefault();
					event.stopPropagation();
					return false;

				} // eo if

       		} //eo func
    	}
		,uiOptions : {
			// toolbar configuration
			toolbar : [
				['back', 'forward'],
				['reload'],
				['home', 'up'],
				['mkdir', 'mkfile', 'upload'],
				//['open', 'download', 'getfile'],
				['info'],
				['quicklook'],
				['copy', 'cut', 'paste'],
				['rm'],
				['duplicate', 'rename'],
				//['extract', 'archive'],
				['search'],
				['view'],
				['help']
			],

			// directories tree options
			tree : {
				// expand current root on init
				openRootOnLoad : true,
				// auto load current dir parents
				syncTree : true
			},

			// navbar options
			navbar : {
				minWidth : 150,
				maxWidth : 500
			},

			// current working directory options
			cwd : {
				// display parent directory in listing as ".."
				oldSchool : false
			}
		}
		,contextmenu : {
			// navbarfolder menu
			navbar : ['open', '|', 'copy', 'cut', 'paste', 'duplicate', '|', 'rm', '|', 'info'],

			// current directory menu
			cwd    : ['reload', 'back', '|', 'upload', 'mkdir', 'mkfile', 'paste', '|', 'info'],

			// current directory file menu
			files  : [
				'getfile', '|', 'quicklook', '|', 'download', '|', 'copy', 'cut', 'paste', 'duplicate', '|',
				'rm', '|', 'rename', '|', 'archive', 'extract', '|', 'info'
			]
		}
	}).elfinder('instance');

});
</script>


<?php if ($template->config['has_header']): ?>
    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Manage Files</h5>
                <span>This is the place where you can manage website files and templates. To upload new files click on icon <img src="<?php echo $template->base_url(); ?>images/icons/dark/add.png" alt="" class="icon" height="12px" width="12px" /></span>
            </div>
            <div class="subnavtitle">
            	<?php if ($this->user->can('add_pages')): ?>
                        <a href="<?php echo site_url('administration/pages/add'); ?>" title="" class="button basic" style="margin: 5px;"><span>Create new page</span></a>
                <?php endif; ?>
						<a href="<?php echo site_url(); ?>" class="button blueB" style="margin: 5px;"><span>Edit pages live</span></a>


            </div>
            <div class="clear"></div>
         </div>
    </div>

    <div class="line"></div>
<?php endif; ?>

    <!-- Main content wrapper -->
    <div class="wrapper">
	    <div class="twoOne">
	    	<div class="widget">
	            <div class="title"><h6><span class="icon-file-xml"></span> Template files</h6></div>
		        <div id="elfinder"></div>
	        </div>
	    </div>

	    <div class="oneThree">
	    	<div class="widget">
	    		<div class="title"><h6><span class="icon-file-xml"></span> Database templates</h6></div>
	    		<table cellpadding="0" cellspacing="0" border="0" class="display dTable">
	            <thead>
		            <tr>
			            <th>Path</th>
			            <th>Last modified</th>
						<th>Actions</th>
		            </tr>
	            </thead>
	            <tbody>
		            <?php $l=30; foreach ($templatez as $tpl): ?>
		            <?php if (is_file($tpl->path)) continue; ?>
		            <tr class="gradeA">
						<td><span<?php echo (strlen($tpl->path)>$l) ? ' class="tipW" title="'.$tpl->path.'"' : "" ; ?>><?php echo ellipsize($tpl->path, $l, .5); ?></span><span style="display: none;"><?php echo $tpl->path; ?></span></td>
						<td class="center"><?php echo empty($tpl->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $tpl->updated).'">'.relative_time($tpl->updated) . '</span> ' . __('by %s', User::factory($tpl->editor_id)->name); ?></td>
						<td class="actBtns">
							<a title="Edit" href="<?php echo site_url('administration/templates/edit/'.$tpl->path); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/pencil.png" alt=""></a>
							<a title="Remove" href="<?php echo site_url('administration/templates/remove/'.$tpl->id); ?>" class="tipN"><img src="<?php echo $template->base_url(); ?>images/icons/dark/close.png" alt=""></a>
						</td>
					</tr>
		            <?php endforeach; ?>

	            </tbody>
	            </table>
			</div>
	    </div>

	    <div class="clear"></div>
    </div>