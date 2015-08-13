<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Process extends CS_Controller {

	private $page = null;

	public function __construct()
	{
		parent::__construct();
		error_reporting(E_ALL);

		$this->load->helper('cookie');
		$this->load->helper('text');
	}

	public function uri()
	{
		//get request uri
		$uri = $path = trim($this->uri->uri_string(), '/');

		if ($path == '.htaccess')
			show_404($path);

		//if uri is empty assign index.html or index.php (if index.html doesn't exist)
		if (empty($uri))
		{

			$uri = $path = "index.htm";

			if (!is_file($path))
				$path = "index-original.htm";

			if (!is_file($path))
				$uri = $path = "index.html";

			if (!is_file($path))
				$path = "index-original.html";

			if (!is_file($path))
			{
				$uri = "index.php";
				$path = "index-original.php";
			}
		}
		//check if directory and assing index.html/index.php
		elseif (is_dir($path))
		{
			$p = rtrim($path, '/') . '/index.htm';

			if (!is_file($p))
				$p = rtrim($path, '/') . '/index.html';

			if (!is_file($p))
				$p = rtrim($path, '/') . '/index.php';

			//$uri = $path = $p;

			redirect($p);
		}

		//get page
		$this->page = $page = Page::factory()->get_by_uri($uri);
		//get file obj from db
		if (!$page->exists())
			$file = File::factory()->get_by_path($path);
		else
			$file = $page->file->get();

		//if file doesn't exists in database, check if it needs to be saved in the
		// db and save it (if needed)
		if (!$file->exists())
		{
			$file->path = $path;

			//if file doesn't exists on disk either, show 404
			if (!is_file($file->path))
				show_404($path);

			$file->checksum = md5_file($file->path);

			//if user is logged in, add him as last editor
			if (!empty($this->user))
				$file->editor_id = $this->user->id;

			//if html file, save it to the database
			if ($file->mime_type() == 'text/html')
				$file->save();

		}

		//if file exists in the database, check it and push it :)
		if ($file->mime_type() !== 'text/html')
			$file->push();


		$newpage = false;

		//if page doesn't exist
		if (!$page->exists())
		{
			if (!$file->exists())
			{
				//no page and no file!?
				show_404($uri);
			}
			else
			{
				$page->title = $file->get_title();
				$page->keywords = $file->get_meta('keywords');
				$page->description = $file->get_meta('description');

				$save2 = array($file);

				if (!empty($this->user))
					$save2[] = $this->user;

				$page->uri = $uri;
				$page->save($save2);
			}

			//new page!
			$newpage = true;
		}
		else
		{
			//check for file
			$f = $page->file->get();

			if ($f->exists())
			{
				//if there is a file, assign it;
				$file = $f;
			}
			else
			{
				//assign default file
				if (is_file($page->uri))
				{
					$file = new File();
					$file->path = $page->uri;
					$file->checksum = md5_file($file->path);

					if (!empty($this->user))
						$file->editor_id = $this->user->id;

					$file->save();
				}
				else
					$file = File::factory()->order_by('default DESC')->limit(1)->get();

				$page->save(array($file));
			}
		}

		//write stats if not logged in
		if (empty($this->user))
		{
			//read cookie and hit variables (ip address, user agent, etc)
			$cookie = $this->input->cookie('__iuvfc');
			$ip_address = $this->input->ip_address();
			$user_agent = $this->input->user_agent();
			$this->load->library('user_agent');

			//create and fill hit
			$hit = new Hit();
			$hit->os = BrowserOS::get_os($user_agent);
			$hit->browser = BrowserOS::get_browser_no_version($user_agent);
			$hit->ip_address = $ip_address;
			$hit->returning = !empty($cookie);

			//get page referer
			$referer = $this->agent->referrer();

			if (!empty($referer))
			{
				//if referer is not coming from our site, write it to hit
				$domain = str_replace('www.', '', parse_url($referer, PHP_URL_HOST));
				$this_domain = str_replace('www.', '', parse_url(site_url(), PHP_URL_HOST));

				if ($domain != $this_domain)
				{
					$hit->referer = $referer;
					$hit->referer_domain = $domain;
				}
			}

			//set cookie for returning visitors
			$cookie = array(
			    'name'   => '__iuvfc',
			    'value'  => time(),
			    'expire' => 3600*24*30*12
			);

			$this->input->set_cookie($cookie);

			//define geoip database path
			$geoip_db_filename = './iu-resources/geoip/GeoIP.dat';

			//if geoip database exists
			if (is_file($geoip_db_filename))
			{
				//we have a geoip db, store country
				$this->load->helper('geoip');
				$gi = @geoip_open($geoip_db_filename, GEOIP_STANDARD);
				$country_name = @geoip_country_name_by_addr($gi, $ip_address);
				if (empty($country_name))
					$country_name = null;

				$hit->country = $country_name;
			}

			if (!empty($hit->os) && !empty($hit->browser))
				$hit->save(array($page));
		}

		//cache if logged out, not an ajax request and
		$this->load->library('cache');
		$this->cache->set_uri($page->uri);
		$cache_time = $page->cache_duration();

		if (empty($this->user) && !$this->is_ajax_request() && ($file->mime_type() == 'text/html'))
		{
			if (($cache_time > 0) && $this->cache->cache_exists($cache_time*60))
			{
				if (Setting::value('use_tidy', 'yes')=='yes')
					die(html_tidy($this->cache->load_cache(false)));
				else
					die($this->cache->load_cache(false));
			}
		}


		/***** START PROCESSING HTML *****/

		if (!empty($page->title))
		{
			$title = $page->title;
			$append_sitename = Setting::value('append_sitename_titles', 'yes') == 'yes';
			if ($append_sitename)
				$title .= ' | ' . Setting::value('website_title');

			$page->set_title($title);
		}


		//add meta tags
		if (!empty($page->keywords))
			$page->set_meta('keywords', $page->keywords);

		if (!empty($page->description))
			$page->set_meta('description', $page->description);

		//$page->set_meta('generator', 'Instant Update '.get_app_version());

		$page->set_encoding('utf-8');
		//$page->set_base_href();

		//embed jquery
		$page->embed('iu-resources/js/jquery.js');

		//embed jquery ui
		//$page->embed('iu-resources/js/jquery-ui.min.js');
		//$page->embed('iu-resources/css/bootstrap/bootstrap.css');

		$page->embed('iu-resources/min/?g=base-css', 'css');

		//dynamically define js variables
		$page->embed('iu-dynamic-js/init.js', null, false);

		//lightbox
		//$page->embed('http://fonts.googleapis.com/css?family=Mako', 'css');
		//$page->embed('iu-resources/lightbox/css/jackbox.css');
		//$page->embed_IE('iu-resources/lightbox/css/jackbox-ie8.css', 'lt', 9);
		$page->embed_IE('http://html5shiv.googlecode.com/svn/trunk/html5.js', 'lt', 9);
		/*$page->embed_IE('iu-resources/lightbox/css/jackbox-ie9.css', 'gt', 8);
		$page->embed('iu-resources/lightbox/js/libs/Jacked.js');
		$page->embed('iu-resources/lightbox/js/jackbox.js');*/

		$page->embed('iu-resources/min/?g=base-js', 'js');

		//load functions
		//$page->embed('iu-resources/js/phpjs.js');
		//$page->embed('iu-resources/js/functions.js');

		//embed webfont
		//$page->embed('http://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js');
		//$page->embed('iu-resources/js/webfont.js');

		//embed jQ pagination
		//$page->embed('iu-resources/js/jquery.simplePagination.js');
		//$page->embed('iu-resources/css/simplePagination.css');
		//$page->embed('iu-resources/js/jquery.masonry.min.js');

		//$page->embed('iu-resources/js/jquery.scrollTo.min.js');

		//buttons
		//$page->embed('iu-resources/css/buttons.css');


		if (!empty($this->user))
		{
			$page->embed('iu-application/views/administration/ckeditor/ckeditor.js');
			$page->embed('iu-resources/min/?g=user-js', 'js');
			//embed hallo (admin)
			//$page->embed('iu-resources/js/rangy/rangy-core.js');
			//$page->embed('iu-resources/js/hallo.js');
			//$page->embed('iu-resources/css/hallo.css');
			//$page->embed('iu-resources/css/image.css');
			//$page->embed('iu-resources/fontawesome/css/font-awesome.css');
			//$page->embed_IE('iu-resources/fontawesome/css/font-awesome-ie7.css', 'lt', 7);

			//splight
			//$page->embed('iu-resources/js/jquery.spotlight.pack.js');

			//embed jqconfigurator (admin only)
			//$page->embed('iu-resources/js/jqconfigurator.js');

			//msgbox
			//$page->embed('iu-resources/msgbox/jquery.msgbox.min.js');
			//$page->embed('iu-resources/msgbox/jquery.msgbox.css');

			//embed jgrowl (admin)
			//$page->embed('iu-resources/css/jquery.jgrowl.css');
			//$page->embed('iu-resources/js/jquery.jgrowl.min.js');

			//menu
			//$page->embed('iu-resources/css/pathmenu.min.css');
			//$page->embed('iu-resources/js/pathmenu.3.2.min.js');

			//embed header
			//$page->embed('iu-resources/css/iu-header.css');
		}



		//this loads at the end
		//$page->embed('iu-resources/css/style.css');

		//...if user is logged in
	//	if (!empty($this->user))
		//	$page->embed('iu-resources/js/instant.js', null, true);

		//$page->embed('iu-resources/js/domready.js', null, true);

		//process contents

		$blocks = $page->dom()->find('div[id],article[id],section[id],aside[id],content[id],menu[id],nav[id]');

		//load contentparser abstract class
		require_once("./iu-application/libraries/contentprocessor.php");

		foreach ($blocks as $block)
		{
			//skip div if it has class iu-skip
			if (stripos((string)$block->class, 'iu-skip') !== false)
				continue;

			//skip div if it contains other divs with id (except if it's marked with iu-content)
			$subdivs = $block->find('div[id],ol[id],ul[id],article[id],section[id],aside[id],content[id],menu[id],nav[id]');
			if ($subdivs != null && (stripos((string)$block->class, 'iu-content') === false))
				continue; //*/

			$div_id = $block->id;
			$c = Content::factory()->where('div', $div_id)
				->group_start()
					->where_related_page('id', $page->id)
					->or_where('is_global', TRUE)
				->group_end()
			->limit(1)->get();


			//get classes
			if (empty($block->class))
				$classes = array();
			else
				$classes = explode(' ', $block->class);

			//assume html type
			$ctype_class = 'Html';

			//if content exists in the database, get it's content type
			/*if ($c->exists())
			{
				$ctype_class = $c->contenttype->get()->classname;
			}
			//otherwise, guess content type from assigned class
			else
			{
				//loop over all classes and process those starting with iu-content-
				foreach ($classes as $classname)
				{
					$classname = strtolower($classname);
					if (strpos($classname, 'iu-content-') === 0)
					{
						$parts = explode('-', $classname);
						if (count($parts) != 3)
							continue;

						$ctype_class = ucfirst($parts[2]);
						break;
					}
				}

			}//*/

			//load class if it isn't loaded
			if (!class_exists($ctype_class))
				require_once("./iu-application/libraries/contents/$ctype_class.php");

			//process block
			$instance = &get_instance();
			$ctype = new $ctype_class($instance);
			$block = call_user_func(array($ctype, 'process'), $block, $c, $page);

			//process via plugin
			//$block = PluginManager::do_actions('process.content', array($block, $c, $page));

			if (is_array($block))
				$block = $block[0];

			//add DB ID if exists in DB
			if ($c->exists())
				$block->setAttribute('data-id', $c->id);

			//get classes
			if (empty($block->class))
				$classes = array();
			else
				$classes = explode(' ', $block->class);

			//if no classes starting with "iu-" - assume editable (StaticHTML)
			if (strpos($block->class, 'iu-content-') === false)
				$classes[] = "iu-content-html";

			//mark as global
			if (!empty($c->is_global))
				$classes[] = "iu-global";

			$block->class = implode(' ', $classes);

			//if can edit
			if (!empty($this->user) && $this->user->can_edit_content($c))
				$block->setAttribute('data-canedit', 'true');

		}

		//change title/desc for single item
		/*$single = $this->input->get('read');
		if (!empty($single))
		{
			$parts = explode('-', $single);
			$id = (int)$parts[0];
			$item = RepeatableItem::factory($id);
			$append_sitename = Setting::value('append_sitename_titles', 'yes') == 'yes';
			$title = strip_tags($item->title);

			if ($append_sitename)
				$title .= ' | ' . Setting::value('website_title');

			$page->set_title($title);
			$page->set_meta('description', character_limiter(strip_tags($item->text), 150));
			$page->set_meta('keywords', strip_tags($item->title));
		}//*/


		$page->body()->setAttribute('data-uri', $page->uri);
		$page->body()->setAttribute('data-id', $page->id);
		$page->body()->setAttribute('data-template', $page->file->path);
		if ($newpage)
			$page->body()->setAttribute('data-newpage', 'true');

		//var_dump($page->body()->{'data-uri'}); die;


		if (!empty($this->user) && $this->user->can_edit_page($page))
			$page->body()->setAttribute('data-canedit', 'true');

		/*$submenu = new AdminMenuItem('Page Summary', 'iu-icon-info', null, true);
			$item2 = new AdminMenuItem(
								"URI: <strong>/". $page->uri."</strong><br />\n"
								. "Title: <strong>". character_limiter( $page->get_title(), 30 )."</strong><br />\n"
								. "Last modified: <strong>".(empty($page->updated)? 'Never' : date('d.m.Y. \@ H:i', $page->updated))."</strong><br />\n"
								. "File: <strong>/".$file->path."</strong><br />\n"
								, null, null, false);
			$item21 = new AdminMenuItem("Edit Page Settings", 'iu-icon-page-gear', site_url('administraton/pages/edit/'.$page->uri), false);

		$submenu->add_item($item2);
		$submenu->add_item($item21);

		$item = new AdminMenuItem('Edit Source File', 'iu-icon-file-edit', site_url('administration/files/edit/'.$file->path), true);


		$page->menu()->add_item($submenu);
		$page->menu()->add_item($item);
		$page->menu()->add_item(new AdminMenuItem('Administration Area', 'iu-icon-home', site_url('administration/dashboard'), true, 'iu-logout-icon'));
		$page->menu()->add_item(new AdminMenuItem('Log Out', 'iu-icon-logout', site_url('administration/auth/logout'), true, 'iu-logout-icon'));
//*/

		if (!empty($this->user))
		{
			$page->body()->innertext = '<div id="fb-root" class="iu-skip"></div><div id="iu-menu" class="iu-skip"></div><div id="iu-jgrowl" class="iu-skip"></div>' . $page->body()->innertext;
		}

		//analytics
		$analytics_id = Setting::value('google_analytics_id', false);
		if (!empty($analytics_id))
		{

			$page->body()->innertext .= "<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', '$analytics_id']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
  })();

</script>";

		}


		$page->prefix_relative_links();

		//$page = PluginManager::do_actions('process.page', $page);

		$html_code = $page->dom()->save();

		if (empty($this->user) && !$this->is_ajax_request() && ($file->mime_type() == 'text/html') && ($cache_time > 0))
		{
			$this->cache->save_cache($html_code);
		}

		if (Setting::value('use_tidy', 'yes')=='yes')
		{
			$this->load->library('format');
			die($this->format->HTML($html_code));
			//die(html_tidy($html_code));
		}
		else
			die($html_code);

	}

	public function page($uri=null)
	{
		if (empty($uri))
			return ($this->page != null) ? $this->page->stored : null;
		elseif (is_numeric($uri))
			return Page::factory((int)$uri)->stored;
		else
			return Page::factory()->get_by_uri($uri)->stored;
	}


}
?>