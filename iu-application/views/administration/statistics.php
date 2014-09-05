<script type="text/javascript">

$.fn.dataTableExt.oSort['int-asc']  = function(x,y) {
	x = parseInt($(x).text());
	y = parseInt($(y).text());
    return ((x < y) ? -1 : ((x > y) ?  1 : 0));
};

$.fn.dataTableExt.oSort['int-desc'] = function(x,y) {
	x = parseInt($(x).text());
	y = parseInt($(y).text());
    return ((x < y) ?  1 : ((x > y) ? -1 : 0));
};

$(document).ready(function() {

	$('.sparkline').sparkline('html',
		{
			width: '95px',
			height: '20px',
			lineColor: '#97c3cf',
			fillColor: '#f2f7f9',
			lineWidth: 2,
			spotColor: '#ed7a53',
			minSpotColor: '#467e8c',
			maxSpotColor: '#9fc569',
			highlightSpotColor: '#1fa856',
			highlightLineColor: '#f2d7ba',
			spotRadius: 3
		}
	);

	$('.dTableNum').dataTable({
		"bJQueryUI": true,
		"sPaginationType": "full_numbers",
		"sDom": '<""l>t<"F"fp>',
		"aaSorting": [[ 0, "desc" ]],
		"aoColumns": [
            { "sType": 'int' },
            null,
            null
        ],
        "iDisplayLength": 6
	});


	//pies
	$('div.piechart').each(function() {
		var vseries = $(this).data('series');
		$.plot($(this), vseries, {
	        series: {
	            pie: {
	                show: true,
	                innerRadius: 0.5,
  					radius: 1,
					label: {
						show: false,
						radius: 2/3,
						formatter: function(label, series){
							return '<div style="font-size:11px;text-align:center;padding:4px;color:white;">'+label+'<br/>'+Math.round(series.percent)+'%</div>';
						},
						threshold: 0.1
					}
	            }
	        },
			legend: {
				show: true,
				noColumns: 1, // number of colums in legend table
				labelFormatter: function(label, series){
					//return '<div>'+label+' ('+Math.round(series.percent)+'%)</div>';
					return '<div>'+label+'</div>';
				}, // fn: string -> string
				labelBoxBorderColor: "#ccc", // border color for the little label boxes
				container: null, // container (as jQuery object) to put legend in, null means default on top of graph
				position: "ne", // position of default legend container within plot
				margin: [5, 10], // distance from grid edge to default legend container within plot
				backgroundColor: "#efefef", // null means auto-detect
				backgroundOpacity: .5 // set to 0 to avoid background
			},
			grid: {
				hoverable: true,
				clickable: false
			}
		});

	}); //eo each

});

</script>

    <!-- Title area -->
    <div class="titleArea">
        <div class="wrapper">
            <div class="pageTitle">
                <h5>Website Statistics</h5>
                <span>Some statistical data presented in chronological order. You can create custom time period by entering number of days in the URL.</span>
            </div>
            <div class="subnavtitle">
                        <a href="<?php echo site_url('administration/statistics/days/365'); ?>" title="" class="button <?php echo ($days == 365) ? 'greyishB' : 'basic' ; ?>" style="margin: 5px;"><span>12 months</span></a>
                        <a href="<?php echo site_url('administration/statistics/days/183'); ?>" title="" class="button <?php echo ($days == 183) ? 'greyishB' : 'basic' ; ?>" style="margin: 5px;"><span>6 months</span></a>
                        <a href="<?php echo site_url('administration/statistics/days/30'); ?>" title="" class="button <?php echo ($days == 30) ? 'greyishB' : 'basic' ; ?>" style="margin: 5px;"><span>30 days</span></a>
                        <a href="<?php echo site_url('administration/statistics/days/7'); ?>" title="" class="button <?php echo ($days == 7) ? 'greyishB' : 'basic' ; ?>" style="margin: 5px;"><span>7 days</span></a>
                        <a href="<?php echo site_url('administration/statistics/days/1'); ?>" title="" class="button <?php echo ($days == 1) ? 'greyishB' : 'basic' ; ?>" style="margin: 5px;"><span>Last 24 hours</span></a>
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

               <div class="widget">
                    <div class="title"><h6>Website statistics</h6></div>
                    <table cellpadding="0" cellspacing="0" width="100%" class="sTable">
                        <thead>
                            <tr>
                                <td width="80px" >Value</td>
                                <td>Name</td>
                                <td width="100px">Line graph</td>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $hits; ?></a></td>
                                <td>Page views</td>
                                <td><span class="sparkline sparkline-live"><?php echo implode(',', $hitsflow); ?></span></td>
                            </tr>
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $unique; ?></a></td>
                                <td>Unique visits</td>
                                <td><span class="sparkline"><?php echo implode(',', $uniqueflow); ?></span></td>
                            </tr>

   							<tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $returning; ?></a></td>
                                <td>Returning visitors</td>
                                <td><span class="sparkline"><?php echo implode(',', $returningflow); ?></span></td>
                            </tr>

                            <!--ukloniti-->
                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $cnt_edits; ?></a></td>
                                <td colspan="2">Content edits</td>
                            </tr>

                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $new_pages; ?></a></td>
                                <td colspan="2">New pages</td>
                            </tr>

                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $repeatables; ?></a></td>
                                <td colspan="2">New repeatable items</td>
                            </tr>

                            <tr>
                                <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $new_users; ?></a></td>
                                <td colspan="2">New users</td>
                            </tr>


                            <!-- eo ukloniti -->

                        </tbody>
                    </table>
                </div>

            </div>


	       	<div class="oneTwo">

	           <div class="widget">
	                <div class="title"><h6>Popular pages</h6></div>
	                <table cellpadding="0" cellspacing="0" border="0" class="display sTable dTableNum">
	                    <thead>
	                        <tr>
	                            <th width="80px">Views</th>
	                            <th>Page Address</th>
	                            <th width="100px">Line graph</th>
	                        </tr>
	                    </thead>
	                    <tbody>
	                    <?php foreach ($pagehits as $hits): ?>
						<?php if (empty($hits->page->uri)) continue; ?>
	                        <tr class="gradeA">
	                            <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $hits->cnt; ?></a></td>
	                        	<td><a href="<?php echo site_url($hits->page->uri); ?>" class="tipN highlightLink" title="<?php echo $hits->page->title; ?>"><?php echo $hits->page->uri; ?></a></td>
	                            <td align="center"><span class="sparkline sparkline-live"><?php echo implode(',', $hits->timeflow); ?></span></td>
	                        </tr>
	                    <?php endforeach; ?>
	                    </tbody>
	                </table>
	            </div>

	        </div>

			<div class="clear"></div>
		</div>

<?php if (is_file('./iu-resources/geoip/GeoIP.dat')): ?>
   <!-- countries' stats -->

    <div class="widgets">
       		<div class="twoOne" style="align: left;">

              	<div class="widget">
                   <div class="title"><h6>Popular countries</h6></div>
                   <table cellpadding="0" cellspacing="0" border="0" class="display sTable dTableNum">
                       <thead>
                           <tr>
                               <th width="80">Views</th>
                               <th>Country Name</th>
                               <th width="80px">Percentage</th>
                           </tr>
                       </thead>
                       <tbody>
                       <?php foreach ($countries as $obj): ?>
                           <tr class="gradeA">
                            <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $obj->cnt; ?></a></td>
                           	<td><?php echo empty($obj->country) ? __("(unknown)") : $obj->country; ?></td>
                           	<td align="center"><?php echo percent($obj->cnt, $countries->iu_total); ?>%</td>
                           </tr>
                       <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>

           </div>

            <div class="oneThree">
                <div class="widget">
                    <div class="title"><h6>Countries graph</h6></div>
                    <div class="body"><div style="width: 100%; height: 100%; min-height: 300px;" class="piechart" data-series='<?php echo json_encode($countries_series); ?>'></div></div>
                </div>
            </div>

            <div class="clear"></div>
      </div>
      <!-- end of countries' stats -->
<?php endif; ?>

    <!-- browsers' stats -->

    <div class="widgets">
       	<div class="twoOne" style="align: left;">

              <div class="widget">
                   <div class="title"><h6>Popular web browsers</h6></div>
                   <table cellpadding="0" cellspacing="0" border="0" class="display sTable dTableNum">
                       <thead>
                           <tr>
                               <th width="80px">Views</th>
                               <th>Browser Name</th>
                               <th width="80px">Percentage</th>
                           </tr>
                       </thead>
                       <tbody>
                       <?php foreach ($browsers as $obj): ?>
                           <tr class="gradeA">
                            <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $obj->cnt; ?></a></td>
                           	<td><?php echo $obj->browser; ?></td>
                           	<td align="center"><?php echo percent($obj->cnt, $browsers->iu_total); ?>%</td>
                           </tr>
                       <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>

           </div>

		<div class="oneThree">
               <div class="widget">
                   <div class="title"><h6>Browsers graph</h6></div>
                   <div class="body"><div style="width: 100%; height: 100%; min-height: 300px;" class="piechart" data-series='<?php echo json_encode($browsers_series); ?>'></div></div>
               </div>
           </div>


		<div class="clear"></div>
	</div>

    <!-- end of browsers' stats -->

   <!-- oses' stats -->

    <div class="widgets">
       	<div class="twoOne" style="align: left;">

              <div class="widget">
                   <div class="title"><h6>Popular operating systems</h6></div>
                   <table cellpadding="0" cellspacing="0" border="0" class="display sTable dTableNum">
                       <thead>
                           <tr>
                               <th width="80px">Views</th>
                               <th>Operating System</th>
                               <th width="80px">Percentage</th>
                           </tr>
                       </thead>
                       <tbody>
                       <?php foreach ($oses as $obj): ?>
                           <tr class="gradeA">
                            <td align="center"><a href="javascript:;" title="" class="webStatsLink"><?php echo $obj->cnt; ?></a></td>
                           	<td><?php echo $obj->os; ?></td>
                           	<td align="center"><?php echo percent($obj->cnt, $oses->iu_total); ?>%</td>
                           </tr>
                       <?php endforeach; ?>
                       </tbody>
                   </table>
               </div>

           </div>


			<div class="oneThree">
			    <div class="widget">
			        <div class="title"><h6>Operating systems graph</h6></div>
			        <div class="body"><div style="width: 100%; height: 100%; min-height: 300px;" class="piechart" data-series='<?php echo json_encode($oses_series); ?>'></div></div>
			    </div>
			</div>

		<div class="clear"></div>
	</div>

    <!-- end of oses' stats -->


    </div>