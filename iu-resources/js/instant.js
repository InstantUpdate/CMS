/* GLOBAL VARS */
var iu_remember = {}; //remember code prior to editing
var iu_editing = {}; //remember contents which are being edited

$iu$(document).ready(function() {

	var newpage = ($iu$.trim($iu$('body').data('newpage')) == 'true');
	var can_edit = ($iu$.trim($iu$('body').data('canedit')) == 'true');

	var menu_items = [];
	menu_items.push({'class': 'iu-icon-toggle-off', 'href': 'javascript:;', 'content': 'Toggle Edit',onClick:function($li,num){ toggle_snapeditor(true); } });
	menu_items.push({'class': 'iu-icon-dashboard', 'href': IU_SITE_URL+'/administration/dashboard', 'content': 'Dashboard'});

	if (can_edit)
		menu_items.push({'class': 'iu-icon-edit', 'href': IU_SITE_URL+'/administration/pages/edit/'+$iu$.trim($iu$('body').data('uri')), 'content': 'Edit Page'});

	if (iu_in_array('edit_templates', IU_USER_PERMISSIONS))
		menu_items.push({'class': 'iu-icon-source', 'href': IU_SITE_URL+'/administration/templates/edit/'+$iu$.trim($iu$('body').data('template')), 'content': 'Edit Source'});


		menu_items.push({'class': 'iu-icon-signout', 'href': 'javascript:void(0);', 'content': 'Sign Out ('+IU_USER.name+')', onClick:function($li,num){ location.href=IU_SITE_URL+'/administration/auth/logout'; } });


	icoroll({
		tip_distance:30,
		back_enabled:true,
		time:3000,
		type:'standard'
	},{
		position: IU_SETTINGS['menu_position'],
		elements:menu_items
	});





	//check editable divs
	//if (iu_count_editable_divs() < 1)
	//	iu_growl("This page's template has too few divs with assigned ID to work with them. To modify it (and add IDs), <a href='"+IU_SITE_URL+"/administration/templates/edit/"+$iu$('body').data('template')+"'>click here</a>.", "WARNING", true);

	//make links autocomplete for hallo
	$iu$(document).on('focus', '.snapeditor_dialog_content_link input.link_href', function() {
		var $this = $iu$(this);
		$this.attr('autocomplete', 'off');
		$this.autocomplete({
            minLength: 0,
            source: IU_PAGES,
            focus: function( event, ui ) {
                //$this.val( ui.item.url );
                return false;
            },
            select: function( event, ui ) {
                $this.val( ui.item.url );
                return false;
            }
        })
        .data( "autocomplete" )._renderItem = function( ul, item ) {
            return $iu$( "<li style=\"z-index: 2147483647 !important;\" class=\"iu-autocomplete-item\">" )
                .data( "item.autocomplete", item )
                .append( "<a><span class=\"iu-autocomplete-title\">" + item.title + "</span><br /><span class=\"iu-autocomplete-uri\">" + item.uri + "</span></a>" )
                .appendTo( ul );
        };
	});

	//highlight div, if specified
	var hlight = iu_GET('iu-highlight');
	if ($iu$.trim(hlight) != "")
	{
		var $el = $iu$('#'+hlight);
		if ($el.length > 0)
		{
			$iu$.scrollTo($el, 500);
			window.setTimeout(function() {
				iu_highlight($el);
				toggle_snapeditor(false);
			}, 500);
		}
	}


}); //document.ready

var IU_SNAPS = [];
var IU_SNAPCONF = {
	path: IU_BASE_URL + "iu-resources/js/snapeditor"
	,image: {
		insertByUpload: true
		,uploadURL: IU_SITE_URL+"/uploadimage?rnd="+Math.random(1,999999)
		//,uploadParams: { param1: "abc123" }
	}
	,onSave: function (e) {
		iu_quick_save(e.api.el);
		return true;
	}
};

$iu$(window).load(function() {

	//add menu to editables
	$iu$(".iu-content-html").each(function () {

		var $this = $iu$(this)

		var can_edit = ($iu$.trim($this.data('canedit')) == 'true');
		if (!can_edit)
			return;

		var elid = $this.attr('id');
		/*window.IU_SNAPS[elid] = new SnapEditor.InPlace(elid, window.IU_SNAPCONF);

		window.IU_SNAPS[elid].api.on("snapeditor.activate", function (e) {
			var $el = $iu$(e.api.el);
			iu_insert_custom_opts($el.attr('id'));
		});

		window.IU_SNAPS[elid].api.disable();//*/
		//$this.addClass('iu-editable');

	}); //.iu-content-html.each

	$iu$('body').data('editing', 'false');

	//mark globals
	$iu$('.iu-global').each(function() {
		var el = $iu$(this);
		var curr_bg = el.css('background-image').split(',');
		el.css('background-image', curr_bg[0] + ', url('+IU_SITE_URL+'/iu-resources/images/ribbon-global.png)');
		var curr_bg_repeat = el.css('background-repeat').split(',');
		el.css('background-repeat', curr_bg_repeat[0] + ', no-repeat');
		var curr_bg_pos = el.css('background-position').split(',');
		el.css('background-position', curr_bg_pos[0] + ', top left');
	});

	//mark empty repeatables
	//iu_mark_empty_repeatables();

});

if ( window.addEventListener )
	window.addEventListener( 'beforeunload', iu_warn_unsaved, false );
else
	window.attachEvent( 'onbeforeunload', iu_warn_unsaved );