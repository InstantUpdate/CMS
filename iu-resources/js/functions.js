if (CSSStyleDeclaration !== "undefined") {
	var isStyleFuncSupported = CSSStyleDeclaration.prototype.getPropertyValue != null;
	if (!isStyleFuncSupported) {
		CSSStyleDeclaration.prototype.getPropertyValue = function(a) {
			return this.getAttribute(a);
		};
		CSSStyleDeclaration.prototype.setProperty = function(styleName, value, priority) {
			this.setAttribute(styleName,value);
			var priority = typeof priority != 'undefined' ? priority : '';
			if (priority != '') {
				// Add priority manually
				var rule = new RegExp(RegExp.escape(styleName) + '\\s*:\\s*' + RegExp.escape(value) + '(\\s*;)?', 'gmi');
				this.cssText = this.cssText.replace(rule, styleName + ': ' + value + ' !' + priority + ';');
			}
		}
		CSSStyleDeclaration.prototype.removeProperty = function(a) {
			return this.removeAttribute(a);
		}
		CSSStyleDeclaration.prototype.getPropertyPriority = function(styleName) {
			var rule = new RegExp(RegExp.escape(styleName) + '\\s*:\\s*[^\\s]*\\s*!important(\\s*;)?', 'gmi');
			return rule.test(this.cssText) ? 'important' : '';
		}
	}
}

// Escape regex chars with backslash
RegExp.escape = function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
}

// The style function
$iu$.fn.style = function(styleName, value, priority) {
    // DOM node
	if (CSSStyleDeclaration !== "undefined") {
		var node = this.get(0);
		// Ensure we have a DOM node
		if (typeof node == 'undefined') {
			return;
		}
		// CSSStyleDeclaration
		var style = this.get(0).style;
		// Getter/Setter
		if (typeof styleName != 'undefined') {
			if (typeof value != 'undefined') {
				// Set style property
				var priority = typeof priority != 'undefined' ? priority : '';
				style.setProperty(styleName, value, priority);
			} else {
				// Get style property
				return style.getPropertyValue(styleName);
			}
		} else {
			// Get CSSStyleDeclaration
			return style;
		}
	} else {
		$iu$.css(styleName, value);
	}
}

/* FUNCTIONS */

/*unićođe štring bažinga */

function is_editing()
{
	return ($iu$.trim($iu$('body').data('editing')) == 'true');
}

function enable_snapeditor($el)
{
	var elid = $el.attr('id');
	//window.IU_SNAPS[elid].api.enable();
	CKEDITOR.inline(elid, {
		toolbar: [
			{ name: 'document', items: [ 'Inlinesave', '-', 'NewPage', '-', 'Templates', 'Advanced' ] },	// Defines toolbar group with name (used to create voice label) and items in 3 subgroups.
			[ 'Cut', 'Copy', 'Paste', 'PasteText', '-', 'Undo', 'Redo' ],			// Defines toolbar group without name.,																					// Line break - next group will be placed in new line.
			{ name: 'basicstyles', items: [ 'Bold', 'Italic', 'Link', 'Image', 'addImage' ] }
		]
	});
	$el.addClass('iu-editable');
	$el.attr('contenteditable', 'true');
}

function disable_snapeditor($el)
{
	var elid = $el.attr('id');
	//window.IU_SNAPS[elid].api.disable();
	var instance = CKEDITOR.instances[elid];
	instance.destroy();
	$el.removeClass('iu-editable');
	$el.attr('contenteditable', 'false');

}

function toggle_snapeditor(highlight_all)
{
	if (highlight_all == "undefined")
		highlight_all=true;

	$iu$(".iu-content-html").each(function () {

		var $this = $iu$(this);

		var can_edit = ($iu$.trim($this.data('canedit')) == 'true');
		if (!can_edit)
			return;

		var elid = $this.attr('id');

		if (is_editing())
		{
			disable_snapeditor($this);
		}
		else
		{
			enable_snapeditor($this);
		}

	});

	if (is_editing())
	{
		$iu$('body').data('editing', 'false');
		iu_growl('Editing is now disabled.', 'INFORMATION');
		$iu$('.iu-icon-toggle-on').addClass('iu-icon-toggle-off').removeClass('iu-icon-toggle-on');
	}
	else
	{
		$iu$('body').data('editing', 'true');
		iu_growl('Editing is now enabled.', 'INFORMATION');
		$iu$('.iu-icon-toggle-off').addClass('iu-icon-toggle-on').removeClass('iu-icon-toggle-off');

		if (highlight_all)
			iu_highlight('.iu-editable');
	}
}

var IU_ADV_ADDED = [];
function iu_insert_custom_opts(id)
{
	return;
	if (window.IU_ADV_ADDED[id] == "undefined")
		window.IU_ADV_ADDED[id] = false;

	var $snap = $iu$('.snapeditor_toolbar_frame:visible');

	if ($snap.length < 1)
		return false;

	var $ul = $snap.find('ul:first');

	if (IU_ADV_ADDED[id] != true)
	{
		var $div = $iu$('li.snapeditor_toolbar_divider:last')
		$div.after($iu$("<li style=\"iu_snap_advanced\"><a class=\"iu_option_advanced snapeditor_toolbar_icon_print\" href=\"javascript:void(null);\" onclick=\"iu_advanced_edit($iu$('#"+id+"'));\" title=\"Open Advanced Editor\"></a></li>"));
		window.IU_ADV_ADDED[id] = true;
	}
}

function iu_GET(name)
{
	name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
	var regexS = "[\\?&]" + name + "=([^&#]*)";
	var regex = new RegExp(regexS);
	var results = regex.exec(window.location.search);
	if(results == null)
		return "";
	else
		return decodeURIComponent(results[1].replace(/\+/g, " "));
}


function iu_repeatable_reload_page(cname)
{
	var $content = $iu$('#'+cname);
	var $pag = $content.find('.iu-pagination:first');

	var cP = $pag.pagination('currPage');
	iu_repeatable_load_page(cP, cname);
}

function iu_repeatable_load_page(pagenr, cname)
{

	var $parent = $iu$('#'+cname);

	//var $parent = window.$pagination_div.parent();
	var what = iu_content_type($parent);

	if (what == false)
	{
		$parent = $iu$('.iu-content-repeatable:first')
		what = iu_content_type($parent);
	}

	if (what == false)
	{
		$parent = $iu$('.iu-content-gallery:first')
		what = iu_content_type($parent);
	}

	//alert(what);

	if (what == 'Repeatable')
		var $item = $parent.find('.iu-item:first');
	else
		var $item = $parent.find('.iu-gallery-item:first');

	//alert(typeof $item);
	//alert($item.length);

	var item_outer_html = $iu$('<div>').append($item.clone()).html();

	if ($item.length > 0)
	{
		var page_id = $iu$('body').data('id');
		var div_id = $parent.data('id');
		var per_page = $iu$.trim($parent.data('per-page'));
		if (per_page == '')
			per_page = 0;

		$iu$('#iu-pagination-loader').show();
		//load page

		if (what == 'Repeatable')
			var url = IU_SITE_URL+'/administration/ajax/repeatable_page/'+page_id+'/'+div_id+'/'+per_page+'/'+pagenr;
		else
			var url = IU_SITE_URL+'/administration/ajax/gallery_page/'+page_id+'/'+div_id+'/'+per_page+'/'+pagenr;

		//alert(url);

		$iu$.post(url, { template: item_outer_html }, function(response) {

			var $parent = $iu$('#'+response.content);
			var what = iu_content_type($parent);

			if (what == false)
			{
				$parent = $iu$('.iu-content-repeatable:first')
				what = iu_content_type($parent);
			}

			if (what == false)
			{
				$parent = $iu$('.iu-content-gallery:first')
				what = iu_content_type($parent);
			}


			var per_page = $iu$.trim($parent.data('limit'));
			if (per_page == '')
				per_page = 10;

			$iu$('#iu-pagination-loader').hide();

			if (what == 'Repeatable')
			{
				$parent.find('.iu-item:visible').fadeOut(500, function() {
					$iu$(this).remove();
				});
			}
			else
			{
				$parent.find('.iu-gallery-item:visible').fadeOut(500, function() {
					$iu$(this).remove();
				});
			}

			window.setTimeout(function() {
				$parent.prepend(response.html);
				$iu$.scrollTo($parent, per_page*100);
			}, 500);

		}, 'json');

	} // end of if
}

function iu_in_array(el, arr)
{
	var l = arr.length;
	for (var i = 0; i < l; i++) {
        if (arr[i] === el)
            return true;
    }
    return false;
}

function iu_menuitem_selected(e, value, depth, role)
{
	//var S_URL = IU_SITE_URL.replace(/\/$/gi, '');

	switch (value) {
		case 'dashboard':
			location.href = IU_SITE_URL + '/administration/dashboard';
			break;
		case 'edit_page':
			location.href = IU_SITE_URL + '/administration/pages/edit/'+$iu$('body').data('uri');
			break;
		case 'edit_template':
			location.href = IU_SITE_URL + '/administration/templates/edit/'+$iu$('body').data('template');
			break;
		case 'users':
			location.href = IU_SITE_URL + '/administration/users';
			break;
		case 'settings':
			location.href = IU_SITE_URL + '/administration/settings';
			break;
		case 'logout':
			location.href = IU_SITE_URL + '/administration/auth/logout';
			break;
	}
}

function iu_menu_pos(setting)
{
	switch(setting) {
		case 'top right':
			return 10;
		case 'bottom right':
			return 9;
		case 'top left':
			return 8;
		default:
			return 7;
	} // switch
}

function iu_alert(text, type)
{
	if (type == "undefined")
		type = "alert";

	$iu$.msgbox(text, {type: type});
}

function iu_confirm(question, func)
{
	$iu$.msgbox(question, {
		type: "confirm",
		buttons : [
			{type: "submit", value: "Yes"},
			{type: "cancel", value: "No"}
		]
	}, function(result) {
		if (result != false)
			func.apply();
	});
}

function iu_root_url(absURL)
{
	var niz = absURL.split('/');
	niz.shift(); niz.shift(); niz.shift();
	return '/'+niz.join('/');
}

function iu_in_iframe()
{
	return (window.location != window.parent.location);
}

function iu_highlight(el)
{
	$iu$(el).fadeOut().fadeIn('slow');
}

function iu_popup(url, width, height)
{
	/*iu_topmenu_hide();

	$iu$('<div />').jackBox("newItem", {
        group: "popup"+ Math.floor((Math.random()*10000)+1),
        //title: "JackBox Just Works",
        href: url,
        width: width,
        height: height,
        trigger: true
    });//*/
}

function iu_popup_image(el, title)
{
	/*if (title === "undefined")
		var title = '';

	var url = IU_BASE_URL + $iu$(el).data('fullimg');
	//url = url.replace('//', '/');

	$iu$('<div />').jackBox("newItem", {
        group: "popup"+ Math.floor((Math.random()*10000)+1),
        title: title,
        href: url,
        trigger: true
    });

    return true;//*/

}

function iu_popup_gallery_image($el)
{
    /*var rel = $el.data('group');
    var href = IU_BASE_URL + $el.data('href');
    var len = window.IU_GLOBALS.iu_gallery_stack.length;

    for(var i = 0; i < len; i++) {
    	var item = window.IU_GLOBALS.iu_gallery_stack[i];
    	var item_href = $iu$(item).data('href');

    	//alert(item_href + ' = ' + href);

    	if (item_href == href)
    	{
    		//alert(dump($iu$._data(item, "events")));
    		item.trigger('click');
    	}
    }


	return false;//*/
}

function iu_popup_fs(url)
{
	iu_popup(url, $iu$(window).width()*0.90, $iu$(window).height()*0.90);
}

function iu_mark_global(el, opts)
{
	if ($iu$(this).hasClass('iu-global'))
		opts.push({"name": "<a href='javascript:;' class='iu-btn danger' title='This is a global content'>G</a>", "callback": iu_note_global });

	return opts;
}

function iu_note_global(name)
{
	iu_growl('Content "'+name+'" is marked as global. Modifying it may affect display of other pages.', 'GLOBAL CONTENT');
}

function iu_show_options()
{
	iu_growl('not implemented');
}

function iu_pixlr_edit(what)
{
	//mark as edited by pixlr
	iu_remove_class('iu-pixlr-edited');
	$iu$(what).addClass('iu-pixlr-edited');

	//get image url
	var img_url = $iu$(what).data('fullimg');
	if (img_url === undefined)
		img_url = $iu$(what).attr('src');

	if (img_url.indexOf(':') == -1)
	{
		if ($iu$(what).hasClass('iu-item-image'))
			img_url = IU_BASE_URL + '/' + img_url;
		else
			img_url = $iu$('base:first').attr('href') + img_url;
	}

	//img_url = img_url.replace(/loc/, 'org'); //cs local fix
	img_url = encodeURI(img_url);

	//specify exit and target URL, and title
	var exit = encodeURI(IU_SITE_URL+'/popup/close');
	var target = encodeURI(IU_SITE_URL+'/administration/images/save');
	var title = $iu$(what).attr('title');
	if (title === undefined)
		title = $iu$(what).attr('alt');

	if (title === undefined)
		title = "Untitled";

	title = encodeURI(title);

	//popup editor
	iu_popup_fs("http://pixlr.com/express/?referrer=IU4&exit="+exit+"&image="+img_url+"&target="+target+"&title="+title);


}


function iu_image_replace(what)
{
	iu_remove_class('iu-image-replace');
	$iu$(what).addClass('iu-image-replace');
	iu_popup(IU_SITE_URL + "/administration/images/replace?iu-popup", 300, 300);
}

function iu_remove_class(classname, el)
{
	if (el === undefined)
		el = 'img';

	$iu$(el).each(function() {
		$iu$(this).removeClass(classname);
	});
}

function iu_content_type(what)
{
	if ($iu$(what).hasClass('iu-content-html'))
		return 'Html';
	else if ($iu$(what).hasClass('iu-content-repeatable'))
		return 'Repeatable';
	else if ($iu$(what).hasClass('iu-content-gallery'))
		return 'Gallery';
	else
		return false;
}


function iu_quick_edit(what)
{
	window.iu_remember[$iu$(what).attr('id')] = $iu$(what).html();

	$iu$(what).hallo({
		editable: true
		,plugins: {
			'halloformat': {},
			'hallojustify': {},
			'hallolink': {},
			/*'halloimage': {
				search: function(query, limit, offset, successCallback) {
					response = {offset: offset, total: limit + 1, assets: searchresult.slice(offset, offset+limit)};
					successCallback(response);
				},
				suggestions: null,
				uploadUrl: function() {
					return '/some/example/url'
				}
			},//*/
			'halloblock': {},
			'hallolists': {},
			'halloreundo': {}
		}
		,showAlways: (IU_SETTINGS.sticky_toolbar == 'yes')
	});

	iu_highlight(what);

	var jid = $iu$(what).data("jid");
	if (jid != undefined)
		$iu$("#"+jid).remove();

	$iu$(what).jConfigurator({
		//"width": 45,
		"separator": "",
		"backgroundColor": "transparent",
		"border": "#505D14",
		"labels":[
			{ "name": "<a href='javascript:;' class='iu-btn-green iu-btn' title='Save content \""+$iu$(what).attr('id')+"\"'>Save</a>", "callback": iu_quick_save }
			,{ "name": "<a href='javascript:;' class='iu-btn-red iu-btn' title='Cancel editing of \""+$iu$(what).attr('id')+"\"'>Cancel</a>", "callback": iu_reset_changes }

		]
	});

}


function iu_advanced_edit(what)
{
	var page_id = $iu$('body').data('id');
	var content_id = $iu$(what).data('id');
	var content_name = $iu$(what).attr('id');

	var url = '';

	if ((content_id !== "undefined") && (content_id > 0))
	{
		url = IU_SITE_URL+'/administration/contents/edit/'+content_id+'/'+content_name;
	}
	else
	{
		url = IU_SITE_URL+'/administration/contents/add/'+page_id+'/'+content_name;
	}

	window.open(url);

	//iu_popup(url, 800, $iu$(window).height() * 0.90);
}

function iu_quick_save(editor)
{
	var what = editor.editable().$;
	var contents = $iu$(what).html();

	var id = $iu$(what).data('id');
	if (id == undefined)
		id = 0;

	$iu$.post(IU_SITE_URL+"/administration/contents/save_instant", {
		'div': $iu$(what).attr('id')
		,'page_uri': $iu$('body').data('uri')
		,'contents': contents
		,'id': id
	}, function(data) {

		if (data.status == 'OK')
		{
			iu_growl(data.message, "SUCCESS");
			$iu$(what).data('id', data.id);
			editor.resetDirty();
			iu_cancel_edit(what);
		}
		else
		{
			iu_growl(data.message, "ERROR");
		}

	}, 'json').error(function(event, jqXHR, ajaxSettings, thrownError)
	{
		iu_growl("Error #" + event.statusCode() + " : " + event.responseText, "ERROR");
	});

}

function iu_reset_changes(what)
{
	var id = $iu$(what).attr('id');
	iu_cancel_edit(what);

	$iu$(what).html(window.iu_remember[id]);
	window.iu_remember[id] = '';
}

function iu_cancel_edit(what)
{
	$iu$(what).hallo({editable: false});

	var jid = $iu$(what).data("jid");
	if (jid != undefined)
		$iu$("#"+jid).remove();

	$iu$(what).jConfigurator({
		//"width": 20,
		"separator": "",
		"backgroundColor": "transparent",
		"border": "#505D14",
		"labels":[
			{ "name": "<a href='javascript:;' class='iu-btn' title='Edit content \""+$iu$(what).attr('id')+"\"'>Instant</a>", "callback": iu_quick_edit }
			,{ "name": "<a href='javascript:;' class='iu-btn' title='Advanced edit content \""+$iu$(what).attr('id')+"\"'>Advanced</a>", "callback": iu_advanced_edit }

		]
	});

}

function iu_growl(msg, title, sticky)
{
	if (sticky === undefined)
		sticky = false;

	var opts = {};

	if (title !== undefined)
		opts.header = title;

	opts.sticky = sticky;

	if (!sticky)
		opts.life = 1500;

	$iu$('#iu-jgrowl').jGrowl(msg, opts);
}

function iu_topmenu_hide()
{
	return;
	$iu$('.iu-topNav').hide();
	$iu$('.iu-show-menu').show();
	$iu$('body').css('margin-top', 0);
}

function iu_topmenu_show()
{
	return;
	$iu$('.iu-show-menu').hide();
	$iu$('.iu-topNav').show();
	$iu$('body').css('margin-top', 34);
}

function iu_count_editable_divs()
{
	//check all divs with ID if they have class iu-content-*, and count them
	var iu_editables = 0;

	$iu$('div[id],ol[id],ul[id],article[id],section[id],aside[id],content[id],menu[id],nav[id]').each(function() {

		var classez = $iu$(this).attr('class');
		if ((classez != undefined) && (classez.indexOf('iu-content-') > -1))
			iu_editables++;
	});

	return iu_editables;
}

function dump(arr,level) {
	var dumped_text = "";
	if(!level) level = 0;

	//The padding given at the beginning of the line.
	var level_padding = "";
	for(var j=0;j<level+1;j++) level_padding += "    ";

	if(typeof(arr) == 'object') { //Array/Hashes/Objects
		for(var item in arr) {
			var value = arr[item];

			if(typeof(value) == 'object') { //If it is an array,
				dumped_text += level_padding + "'" + item + "' ...\n";
				dumped_text += dump(value,level+1);
			} else {
				dumped_text += level_padding + "'" + item + "' => \"" + value + "\"\n";
			}
		}
	} else { //Stings/Chars/Numbers etc.
		dumped_text = "===>"+arr+"<===("+typeof(arr)+")";
	}
	return dumped_text;
}

//now something for news

function iu_newsitem_add(what)
{
	var parent = $iu$(what);
	var copy = $iu$(what).find('.iu-item:hidden:first').clone();

	$iu$(copy).find('.iu-item-id:first').val('0');

	//set current date
	var datefield = $iu$(copy).find('.iu-item-date:first');
	if ($iu$(datefield).length > 0)
	{
		var format = $iu$(datefield).data('format');
		if (undefined == format)
			format = 'Y-m-d H:i';

		var dateS = date(format, Math.round(new Date().getTime() / 1000));
		$iu$(datefield).text(dateS);
	}

	//set author
	var authorfield = $iu$(copy).find('.iu-item-author:first');
	if ($iu$(authorfield).length > 0)
	{
		$iu$(authorfield).text(IU_USER.name);
	}

	//set title
	var titlefield = $iu$(copy).find('.iu-item-title:first');
	if ($iu$(titlefield).length > 0)
	{
		//$iu$(titlefield).text("This is new item title! Click to edit!");
	}

	//set author
	var textfield = $iu$(copy).find('.iu-item-text:first');
	if ($iu$(textfield).length > 0)
	{
		//$iu$(textfield).html("<span style=\"font-weight: bold;\">This is sample content text. Click to edit!</span> And following is just a dummy text used to fill up the space: Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean at sapien sit amet mauris adipiscing gravida. Proin sed interdum purus. Proin mauris purus, tincidunt non varius sed, volutpat eu lectus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce tempor, dolor ac viverra sollicitudin, neque felis viverra turpis, in condimentum nisi turpis eu velit...");
	}

	$iu$(copy).append('<input type="hidden" class="iu-item-id" value="0" />');

	$iu$(copy).hide();
	$iu$(copy).removeClass('iu-invisible');
	$iu$(parent).prepend(copy);
	$iu$(copy).fadeIn('fast');

	iu_mark_empty_repeatables();

	//start editing
	iu_newsitem_edit(copy);
}


function iu_mark_empty_repeatables()
{
	$iu$('.iu-content-repeatable, .iu-content-gallery').each(function() {
		var visible_items = $iu$(this).find('.iu-item:visible, .iu-gallery-item:visible');
		if (visible_items.length < 1)
			$iu$(this).addClass('iu-empty');
		else
			$iu$(this).removeClass('iu-empty');
	});
}

function iu_newsitem_edit(what)
{
	var titlefield = $iu$(what).find('.iu-item-title:first');
	var textfield = $iu$(what).find('.iu-item-text:first');
	var id = $iu$(what).find('.iu-item-id:first').val();
	var remID = $iu$(what).parent().attr('id')+'_'+id;

	/*window.iu_remember[remID] = {
		'title': $iu$(titlefield).text()
		,'text': $iu$(textfield).html()
	};

	window.iu_editing[remID] = true;

	$iu$(titlefield).hallo({
		editable: true
		,plugins: {
			'halloreundo': {}
		}
		,showAlways: (IU_SETTINGS.sticky_toolbar == 'yes')
	});

	$iu$(textfield).hallo({
		editable: true
		,plugins: {
			'halloformat': {},
			'hallojustify': {},
			'hallolink': {},
			'halloblock': {},
			'hallolists': {},
			'halloreundo': {}
		}
		,showAlways: (IU_SETTINGS.sticky_toolbar == 'yes')
	});//*/

	//window.IU_SNAPS[$iu$(what).parent().attr('id')] = new SnapEditor.InPlace($iu$(what).parent().attr('id'), window.IU_SNAPCONF);
	//alert($iu$(what).parent().attr('id'));
	//window.IU_SNAPS[textfield.attr('id')] = new SnapEditor.InPlace(textfield.attr('id'), window.IU_SNAPCONF);


	iu_highlight(titlefield);
	iu_highlight(textfield);

}

function iu_newsitem_cancel(what)
{
	var titlefield = $iu$(what).find('.iu-item-title:first');
	var textfield = $iu$(what).find('.iu-item-text:first');
	var id = $iu$(what).find('.iu-item-id:first').val();


	if (id == undefined || id == 0)
		return iu_newsitem_remove(what);

	//restore from remember!
	$iu$(titlefield).hallo({editable: false});
	$iu$(textfield).hallo({editable: false});

	var remID = $iu$(what).parent().attr('id')+'_'+id;
	var remObj = window.iu_remember[remID];

	$iu$(titlefield).text(remObj.title);
	$iu$(textfield).html(remObj.text);

	window.iu_remember[remID] = false;
	window.iu_editing[remID] = false;
}

function iu_newsitem_remove(what)
{
	var id = $iu$(what).find('.iu-item-id:first').val();
	var title = $iu$(what).find('.iu-item-title:first').text();

	if (id != 0 && id != undefined)
	{

		iu_confirm("Are you sure you want to remove this item?", function() {

			$iu$.post(IU_SITE_URL+'/administration/repeatables/ajax_remove', { id: id, title: title }, function(data) {

				if (data.status == 'OK')
				{

					$iu$(what).fadeOut('fast');
					setTimeout(function() {
						$iu$(what).remove();
						iu_growl(data.message, "SUCCESS");
						iu_mark_empty_repeatables();
					}, 500);
				}
				else
					iu_growl(data.message, "ERROR");

			}, 'json').error(function(event, jqXHR, ajaxSettings, thrownError)
			{
				iu_growl("Error #" + event.statusCode() + " : " + event.responseText, "ERROR");
			}); //eo $iu$.post

  		}); //eo of iu_confirm
	}
	else
	{
		$iu$(what).fadeOut('fast');
		setTimeout(function() {
			$iu$(what).remove();
			iu_mark_empty_repeatables();
		}, 500);
	}


}

function iu_newsitem_save(what)
{
	var id = $iu$(what).find('.iu-item-id:first').val();
	if (id == undefined)
		id=0;

	var titlefield = $iu$(what).find('.iu-item-title:first');
	var textfield = $iu$(what).find('.iu-item-text:first');
	var page_id = $iu$('body').data('id');
	var div_id = $iu$(what).parent().attr('id');
	var img_uri = $iu$(what).find('img.iu-item-image:first');

	var post = {
		id: id
		,title: $iu$(titlefield).text()
		,text: $iu$(textfield).html()
		,page_id: page_id
		,div: div_id
	};


	if (img_uri.length > 0)
	{
		post.image = img_uri.data('fullimg');
	}

	$iu$.post(IU_SITE_URL+'/administration/repeatables/ajax_save', post, function(data) {

		if (data.status == 'OK')
		{
			iu_growl(data.message, "SUCCESS");
			$iu$(what).find('.iu-item-id:first').val(data.id)
			$iu$(titlefield).hallo({editable: false});
			$iu$(textfield).hallo({editable: false});

			//mark as non editing
			var id = $iu$(what).find('.iu-item-id:first').val();
			var remID = $iu$(what).parent().attr('id')+'_'+id;
			window.iu_editing[remID] = false;
		}
		else
		{
			iu_growl(data.message, "ERROR");
		}

	}, 'json').error(function(event, jqXHR, ajaxSettings, thrownError)
	{
		iu_growl("Error #" + event.statusCode() + " : " + event.responseText, "ERROR");
	});



}

////////////////// GALLERY

function iu_gallery_remove_image(what)
{
	var elems = $iu$(what).attr('id').split('_');
	var id = elems[2];

	iu_confirm('Are you sure you want to remove this image?\n\n<br/><br/>Note that this cannot be undone.', function() {
		var url2 = IU_SITE_URL+'/administration/ajax/gallery_remove_image/'+id;
		$iu$.get(url2, function(data) {
			if (data.status == 'OK')
			{
				var $im = $iu$('#iu_image_'+id).parents('.iu-gallery-item:first');
				$im.fadeOut('slow', function(){ $iu$(this).remove(); });
				iu_growl(data.message, "SUCCESS");
				iu_repeatable_reload_page();
			}
			else
				iu_growl(data.status, "ERROR");

		}, 'json');
	});
}

function iu_gallery_add_new(what)
{
	var $content = false;

	if ($iu$(what).hasClass('iu-content-gallery'))
		$content = $iu$(what);
	else
		$content = $iu$(what).parents('.iu-content-gallery:first');

	var id = $content.data('id');
	var cname = $content.attr('id');
	var page_id = $iu$('body').data('id');

	if ($iu$.trim(id) == "")
		iu_popup(IU_SITE_URL + "/administration/images/new_content/"+cname+"/"+page_id+"?iu-popup", 300, 400);
	else
		iu_popup(IU_SITE_URL + "/administration/images/gallery_add_new/"+id+"/"+cname+"/"+page_id+"?iu-popup", 300, 400);
}

function iu_warn_unsaved( e )
{
	var asuume = false;
	for (i in CKEDITOR.instances)
	{
		if ( CKEDITOR.instances[i].checkDirty() )
		{
			assume = true;
			return e.returnValue = "You will lose the changes made in the editor.";
		}
	}
}