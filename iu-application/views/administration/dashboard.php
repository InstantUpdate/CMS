<script type="text/javascript">

var ping = <?php echo ($ping) ? 'true' : 'false'; ?>;

$(document).ready(function() {
	$('.sparkline').sparkline('html',
		{
			width: '75px',
			height: '20px',
			lineColor: '#97c3cf',
			fillColor: '#f2f7f9',
			lineWidth: 2,
			spotColor: '#D9534F',
			minSpotColor: '#467e8c',
			maxSpotColor: '#18A689',
			highlightSpotColor: '#1fa856',
			highlightLineColor: '#f2d7ba',
			spotRadius: 3
		}
	);

    if (ping) {
        $.ajax({
            url: "http://instant-update.com/ping.php",
            dataType: "jsonp",
            data: {
                domain: '<?php echo get_domain(site_url()); ?>',
                version: '<?php echo get_app_version(); ?>'
            },
            success: function( response ) {
                //alert(response.version);
            }
        });
    }
});


</script>

    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Dashboard</h5>
                <span>Welcome <?php echo $user->name; ?>! This is a good spot to check latest updates regarding your website.</span>
            </div>
            <div class="subnavtitle">
            <?php if ($user->can('manage_users')): ?>
            	<a href="<?php echo site_url('administration/users/add'); ?>" title="" class="button basic" style="margin: 5px;"><span>Add new user</span></a>
            <?php endif; ?>
            <?php if ($user->can('manage_settings')): ?>
				<a href="<?php echo site_url('administration/settings'); ?>" title="" class="button greyishB" style="margin: 5px;"><span>Settings</span></a>
            <?php endif; ?>
			</div>
            <div class="clear"></div>
        </div>
    </div>
    <div class="line"></div>
    <div class="wrapper">
    <?php $template->load_template('notifications'); ?>
    </div>

    <!-- Main content wrapper -->
    <div class="wrapper">

        <!-- Widgets -->
        <div class="widgets">
            <div class="oneTwo">

                <!-- Invoices stats widget --><!-- Website stats widget -->
                <div class="widget">
                    <div class="title"><h6><span class="icon-bars"></span> Website statistics</h6><div class="num"><a href="<?php echo site_url('administration/statistics'); ?>" class="greyNum">see all</a></div></div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
                        <thead>
                            <tr>
                                <td width="80">#</td>
                                <td>Desc</td>
                                <td width="80">Line graph</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $last15; ?></a></td>
                                <td>Unique visits in last 15 minutes</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $last15bymin); ?></span></td>
                            </tr>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $last4hrs; ?></a></td>
                                <td>Unique visits in last 4 hours</td>
                                <td><span class="sparkline"><?php echo implode(',', $last4hrsbymin); ?></span></td>
                            </tr>
                              <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $today; ?></a></td>
                                <td>Unique visits today (<?php echo date(Setting::value('date_format', 'F j, Y')); ?>)</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $todaybymin); ?></span></td>
                            </tr>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $yesterday; ?></a></td>
                                <td>Unique visits yesterday (<?php echo date(Setting::value('date_format', 'F j, Y'), time() - 3600*24); ?>)</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $yesterdaybymin); ?></span></td>
                            </tr>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $lastweek; ?></a></td>
                                <td>Unique visits in last 7 days</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $lastweekbymin); ?></span></td>
                            </tr>
                             <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $lastmonth; ?></a></td>
                                <td>Unique visits in last 30 days</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $lastmonthbymin); ?></span></td>
                            </tr>
                             <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $returning; ?></a></td>
                                <td>Returning visitors in last 30 days</td>
                                <td>&nbsp;</td>
                            </tr>
                             <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $new_pages; ?></a></td>
                                <td>New pages in last 30 days</td>
                                <td>&nbsp;</td>
                            </tr>
                             <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $countries; ?></a></td>
                                <td>New countries in last 30 days</td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php if ($pagehits->result_count() > 0): ?>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $pagehits->cnt; ?></a></td>
                                <td>Most popular page in last 7 days: <?php echo anchor($pagehits->page->uri, $pagehits->page->uri); ?></td>
                                <td>&nbsp;</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Collapsible. Opened by default -->
                <div class="clear"></div>

                <!-- Collapsible. Closed by default -->
            	<div class="clear"></div>
                <!-- Latest update widget -->
<!--                 <div class="widget">
                    <div class="title"><h6><span class="icon-quill"></span> Blog/News latest</h6><div class="num"><a href="<?php echo site_url('administration/repeatables'); ?>" class="greyNum">see all</a></div></div>

                    <div class="updates">
                    	<?php foreach ($last_repeatables as $rep): ?>
                    	<div class="newUpdate">
                            <div class="uDone">
                                <a href="<?php echo site_url($rep->content->get()->page->get()->uri); ?>" title="<?php echo $rep->title; ?>" target="_blank"><strong><?php echo character_limiter(strip_tags($rep->title), 50); ?></strong></a> in <strong><?php echo $rep->content->get()->div; ?></strong>
                                <span><?php echo character_limiter(strip_tags($rep->text), 70); ?></span>
                            </div>
                            <div class="uDate"><span class="uDay"><?php echo date('d', $rep->timestamp); ?></span><?php echo strtolower(date('M', $rep->timestamp)); ?></div>
                            <div class="clear"></div>
                        </div>
                        <?php endforeach; ?>



                    </div>
                </div> -->

            <!-- widget with fixed height and custom scroll --></div>

        	<!-- 2 columns widgets -->
            <div class="oneTwo">

            	<!-- Big buttons as widgets -->
            	<div class="oneTwo"><a href="<?php echo site_url(); ?>" title="" class="wContentButton bluewB"><span class="icon-pencil"></span> Edit website live</a></div>
            	<?php if ($user->can('edit_templates') || $user->can('edit_assets') || $user->can('edit_all_assets')): ?>
                <div class="oneTwo"><a href="<?php echo site_url('administration/templates'); ?>" title="" class="wContentButton redwB"><span class="icon-upload3"></span> Upload files</a></div>
                <?php endif; ?>
                <div class="clear"></div>

                <!-- Search -->
                <!--<div class="searchWidget">
                    <form action="">
                        <input type="text" name="search" placeholder="Search website content..." />
                        <input type="submit" name="find" value="" />
                    </form>
                </div>-->

                <div class="clear"></div>

            	<!-- My tasks table widget -->
                <div class="widget">
                    <div class="title"><h6><span class="icon-loop2"></span> Latest content updates</h6><div class="num greyNum">revisions +<?php echo $revisions; ?></div></div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="sTable taskWidget">
                        <thead>
                            <tr>
                                <td>Content name</td>
                                <td>Page</td>
                                <td width="200px">Modified</td>
                            </tr>
                        </thead>
                        <tbody>
                        	<?php foreach($last_contents as $con): ?>
                            <tr>
                            <?php $page = $con->page->get(); ?>
                                <td class="taskPr"><a class="tipN" href="<?php echo site_url($page->uri.'?iu-highlight='.$con->div); ?>" title="Edit live <?php echo $con->div; ?>"><?php echo $con->div; ?></a></td>
                                <td style="text-align: left;">Edit in page: <a href="<?php echo site_url('administration/pages/edit/'.$page->uri); ?>" class="tipN highlightLink" title="<?php echo $page->title; ?>"><?php echo $page->uri; ?></a></td>
                       			<td class="center"><?php echo empty($con->updated) ? "&mdash;" : '<span class="tipN" title="'.date(Setting::value('datetime_format', 'F j, Y @ H:i'), $con->updated).'">'.relative_time($con->updated) . '</span> ' . __('by %s', User::factory($con->editor_id)->name); ?></td>
							</tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- New users widget -->
            <!--     <div class="widget">
                    <div class="title"><h6><span class="icon-user4"></span> New users</h6></div>
                    <?php foreach ($users as $user): ?>
				    <div class="wUserInfo">
                        <a href="<?php echo site_url('administration/users/edit/'.$user->id); ?>" title="" class="wUserPic"><img src="<?php echo $user->get_profile_picture_thumb(36, 36); ?>" alt="<?php echo $user->name; ?>" /></a>
                        <ul class="leftList">
                            <li><a href="<?php echo site_url('administration/users/edit/'.$user->id); ?>" title=""><strong><?php echo $user->name; ?></strong></a></li>
                            <li>Created: <strong><?php echo date(Setting::value('datetime_format', 'F j, Y @ H:i'), $user->created); ?></strong></li>
                        </ul>
                        <ul class="rightList">
                        <?php $updates = $user->contentrevision->order_by_related_content('updated', 'DESC')->get(); $cnt = $updates->result_count(); ?>
                            <li><strong><?php echo $cnt; ?> content update<?php echo ($cnt != 1) ? 's' : '' ; ?></strong></li>
                            <li>Last update: <?php echo ($cnt > 0) ? date(Setting::value('datetime_format', 'F j, Y @ H:i'), $updates->all[0]->content->get()->updated) : 'never'; ?></li>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <div class="cLine"></div>
                    <?php endforeach; ?>



                </div> -->

            <!-- Accordion --><!-- Widget with ajax loader --><!-- Tabs -->
            </div>

            <div class="clear"></div>

        </div>

    </div>