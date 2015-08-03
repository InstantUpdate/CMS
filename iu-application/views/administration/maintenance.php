<script type="text/javascript">

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


	<!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Website Maintenance</h5>
                <span>This is a place where you can find various website maintenance tools.</span>
            </div>
            <div class="subnavtitle" style="display: none;">
				<a href="javascript:;" onclick="duplicate();" title="" class="button greyishB" style="margin: 5px;"><img src="<?php echo $template->base_url(); ?>images/icons/light/download.png" alt="" class="icon" /><span>Duplicate</span></a>
				<a href="javascript:;" onclick="save();" title="" class="button redB" style="margin: 5px;"><span>Save</span></a>
            </div>
            <div class="clear"></div>
        </div>
    </div>

    <div class="line"></div>

	<div class="wrapper">
	    <?php $template->load_template('notifications'); ?>
	</div>

	<div class="wrapper">
		<form action="<?php echo site_url('administration/maintenance/prunerevs'); ?>" method="post">
			<div class="oneThree">
				<fieldset>
					<div class="widget">
						<div class="title"><h6>Database backup</h6></div>

						<p>Use buttons below to download complete database backup of your website (excluding images and media). Note that both buttons return same result, while latter will pack it in the ZIP archive before letting you download it.</p>

						<p align="center">
							<a href="<?php echo site_url('administration/maintenance/exportsql'); ?>" title="" class="button redB" style="margin: 5px;"><span>Download .SQL (raw)</span></a>

							<a href="<?php echo site_url('administration/maintenance/exportsql/zip'); ?>" title="" class="button redB" style="margin: 5px;"><span>Download .SQL (zip)</span></a>
						</p>
					</div>
				</fieldset>
			</div>

			<div class="oneThree">
				<fieldset>
					<div class="widget">
						<div class="title"><h6>GeoIP database update</h6></div>

						<p>Use button below to update your GeoIP database automatically. After that you will have more accurate statistics about your website visitors' locations.</p>

						<p align="center">
							<a href="<?php echo site_url('administration/maintenance/getgeoip'); ?>" title="" class="button redB" style="margin: 5px;"><span>Update GeoIP database</span></a>
						</p>

						<p>GeoIP file should be placed in <strong>iu-resources/geoip/GeoIP.dat</strong></p>
					</div>
				</fieldset>
			</div>

			<div class="oneThree">
				<fieldset>
					<div class="widget">
						<div class="title"><h6>Prune revisions</h6></div>

						<p>Use button below to delete all content and template revisions older than chosen age.</p>

						<div class="formRow">
							<label>Older than:</label>
							<div class="formRight">
								<select name="age" id="age" class="chzn-select" tabindex="6" style="width: 150px;">
									<option value="1">1 month</option>
									<option value="3">3 months</option>
									<option value="6" selected="selected">6 months</option>
									<option value="12">12 month</option>
								</select>
							</div>
							<span class="formNote">&nbsp;</span>
		                   	<div class="clear"></div>
						</div>

						<p align="center">
							<a href="javascript:;" onclick="$(this).parents('form:first').submit();" title="" class="button redB" style="margin: 5px;"><span>Prune revisions</span></a>
						</p>
					</div>
				</fieldset>
			</div>
		</form>
	</div>

