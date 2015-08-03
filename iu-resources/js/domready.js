var $pagination_divs = false;

$iu$(document).ready(function () {

	//init pagination
	window.$pagination_divs = $iu$('.iu-pagination');

	if (window.$pagination_divs.length > 0)
	{
		window.$pagination_divs.each(function() {

			$pagination_div = $iu$(this);

			var $parent = $pagination_div.parents('div[id]:first,ol[id]:first,ul[id]:first,article[id]:first,content[id]:first,menu[id]:first,nav[id]:first');
			var parent_name = $parent.attr('id');

			var paged = $pagination_div.data('paged');
			var limit = $iu$.trim($parent.data('per-page'));
			if (limit == "")
				limit = 0;
			else
				limit = parseInt(limit);


			var slug = $iu$.trim($pagination_div.data('slug'));

			var theme = $iu$.trim($parent.data('theme'));
			if (theme == "")
				theme = 'light';

			var page_url = window.location.href;
			if (page_url.indexOf('#'+slug+'=') > -1)
				page_url = page_url.substr(0, page_url.indexOf('#'+slug+'='));

			var hasharr = document.location.hash.split('=');
			var currpage = $iu$.trim(hasharr[1]);

			if (currpage == "")
				currpage = 1;

			$pagination_div.pagination({
		        items: paged.total_rows,
		        itemsOnPage: limit,
		        cssStyle: theme+'-theme',
		        hrefText: page_url+'#'+slug+'=',
		        onPageClick: function(p) { iu_repeatable_load_page(p, parent_name); },
		        currentPage: parseInt(currpage)
	    	});

			if (currpage != 1)
				iu_repeatable_load_page(parseInt(currpage), parent_name);

			//if in list, make pagination below the gallery
			$pagination_div.parents('li:first').css({'display': 'block', 'clear': 'both', 'float': 'none'});
		});


	}


	//this must go at the very bottom of the dom ready function
	if (IU_SETTINGS.comments_enabled == 'Facebook')
	{
		var there = $iu$('#facebook_thread').length;
		if (there < 1)
			return;

		var d = document;
		var s = 'script';
		var id = 'facebook-jssdk';

		var js, fjs = d.getElementsByTagName(s)[0];
		if (d.getElementById(id)) return;
		js = d.createElement(s); js.id = id;
		js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId="+IU_SETTINGS.comments_engine_id;
		fjs.parentNode.insertBefore(js, fjs);
	}
	else if (IU_SETTINGS.comments_enabled == 'Disqus')
	{
		var there = $iu$('#disqus_thread').length;
		if (there < 1)
			return;

		var disqus_shortname = IU_SETTINGS.comments_engine_id;
		var disqus_identifier = iu_root_url(window.location.href);
		var disqus_url = window.location.href;

		var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
		dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
		(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
	}


});