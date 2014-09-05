<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Page extends DataMapper {

	public $has_many = array('hit', 'content',

		'editor'=>array(
			'class'=>'user'
			,'other_field'=>'assigned_pages'
			,'join_self_as'=>'page'
			,'join_other_as'=>'user'
			,'join_table'=>'pages_users'
		)

	);
	public $has_one = array('file', 'user');


	public $auto_populate_has_one = true;

	protected $dom = null;
	protected $html;
	//protected $admin_menu = null;

	public static $DOCTYPES = array(
		'HTML 4.01' => array(
			'Strict' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
	"http://www.w3.org/TR/html4/strict.dtd">'
			,'Transitional' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
	"http://www.w3.org/TR/html4/loose.dtd">'
			,'Frameset' => '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN"
	"http://www.w3.org/TR/html4/frameset.dtd">'
		)
		,'XHTML 1.0' => array(
			'Strict' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">'
			,'Transitional' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">'
			,'Frameset' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">'
		)
		,'XHTML 1.1' => array(
			'DTD' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">'
			,'Basic' => '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.1//EN"
	"http://www.w3.org/TR/xhtml-basic/xhtml-basic11.dtd">'
		)
		,'HTML 5' => '<!DOCTYPE html>'
	);

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    	//$this->admin_menu = new AdminMenu('iu-admin-menu', true);
    }

    public static function factory($id = null)
    {
		$instance = new Page();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public function dom()
	{
		if (empty($this->dom))
		{
			$this->dom = new htmldom();
			$this->dom->load($this->html());
		}

		return $this->dom;

	}

	public function menu()
	{
		return $this->admin_menu;
	}

	public function html($value=NULL)
	{
		if (!empty($value))
		{
			$this->html = $value;
			$this->dom = new htmldom();
			$this->dom->load($this->html);
			return true;
		}

		if (!empty($this->html))
			return $this->html;

		$file = $this->file->get();

		if (!$file->exists())
		{
			if (is_file($this->uri))
			{
				$file = new File();
				$file->path = $this->uri;
			}
			else
				return self::ensure_proper_html("");
		}

		$html = $file->process();
		$html = self::fix_js_comments($html);
		$this->html(self::ensure_proper_html($html));
		$this->dom()->load($this->html);
		return $this->html;
	}

	public function get_title()
	{
		$html = $this->html();

		$dom = new htmldom();
		$dom->load($html);
		$title = $dom->find('title', 0)->innertext;
		if (empty($title))
			return "";

		return $title;
	}

	public static function ensure_proper_html($html)
	{
		//$html = $this->html();
		/*if (empty($html))
		{
			trigger_error("Can not fix empty HTML.", E_USER_WARNING);
			return false;
		}//*/

		//load html into dom
		$dom = new htmldom();
		$dom->load($html);

		//check html tag
		$tag_html = $dom->find('html', 0);
		if (empty($tag_html))
		{
			$dom->load("<html><head><title></title></head><body>".$html."</body></html>");
			$tag_html = $dom->find('html', 0);
		}

		//check head tag
		$tag_head = $dom->find('head', 0);
		if (empty($tag_head))
		{
			$tag_html->innertext = "<head><title></title></head>" . $tag_html->innertext;
			$tag_head = $dom->find('head', 0);
		}

		//check body tag
		$tag_body = $dom->find('body', 0);
		if (empty($tag_body))
		{
			$tag_html->innertext = $tag_html->innertext . "<body></body>";
			$tag_body = $dom->find('body', 0);
		}

		$fhtml = $dom->save();

		//check doctype
		if (!preg_match('/<!DOCTYPE(.*?)>/si', $fhtml))
		{
			$fhtml = self::$DOCTYPES['HTML 5'] . "\n" . $fhtml;
		}
		/*else
		{
			$lines = preg_split("/(\r\n|\n|\r)/", $fhtml);
			$lines[0] = self::$DOCTYPES['HTML 5'];
			$fhtml = implode("\n", $lines);
		}//*/

		return $fhtml;

	}

	public function set_title($title)
	{
		$html = $this->html();
		if (empty($html))
		{
			trigger_error("Can not set title on empty HTML code!", E_USER_WARNING);
			return $this;
		}
		else
		{
			$title = htmlspecialchars($title);

			$dom_title = $this->dom()->find('title', 0);

			if ($dom_title == null)
			{
				$head = $this->dom()->find('head', 0);
				$head->innertext .= "<title>{$title}</title>";
			}
			else
			{
				$dom_title->innertext = $title;
			}

			$this->html($this->dom()->save());
			return $this;
		}
	}

	public function set_base_href($url=null)
	{
		$head = $this->dom()->find('head', 0);
		$base = $this->dom()->find('head base', 0);

		if (empty($url))
		{
			$dir = dirname($this->file->path);

			if ($dir == '.')
				$prefix = '';
			else
				$prefix = $dir . '/';

			$url = rtrim(site_url($prefix), '/') . '/';
		}

		if (empty($base))
		{
			$head->innertext = '<base href="'.$url.'" />' . "\n"
				. $head->innertext;
		}
		else
		{
			$base->href = $url;
		}

		$this->html($this->dom()->save());
		return $this;

	}

	public function title()
	{
		if (!empty($this->title))
			return $this->title;
		else
			return $this->get_title();
	}

	public function embed_jquery($uri='iu-resources/js/jquery.js')
	{
		$scripts = $this->dom()->find('script[src]');

		//find existing jQuery and remove it
		if (!empty($scripts))
		{
			foreach ($scripts as $script)
			{
				$src = $script->src;
				$bname = basename($src);
				if (strpos($bname, 'jquery') === 0)
				{
					$script->outertext = '';
					break;
					//$script->src = site_url('iu-resources/js/empty.js');
				}
			}
		}

		if (strpos($uri, ':') === false)
			$uri = site_url($uri);

		$head = $this->dom()->find('head', 0);
		$head->innertext = '<script src="'.$uri.'" type="text/javascript"> </script>' .
			"\n" . $head->innertext;


		$this->html($this->dom()->save());

		return $this;
	}

	public function embed($url, $type = null, $bottom = true, $attrs = array())
	{
		if ($type == null)
			$type = end(explode('.', $url));

		if ($type != "css")
			$type = "js";

		if (strpos($url, ':') === false)
		{
			if (strpos($url, 'iu-resources/min/?g=') !== false)
				$url = base_url() . $url;
			else
				$url = site_url($url);
		}

		if ($type == "css")
		{
			$all = $this->dom()->find('link[href]');
			$what = 'href';
		}
		else
		{
			$all = $this->dom()->find('script[src]');
			$what = 'src';
		}

		foreach ($all as $item)
		{
			if ($item->{$what} == $url)
				return $this;
		}

		$head = $this->dom()->find('head', 0);


		$add = '';
		foreach ($attrs as $attr=>$value)
			$add.=" $attr=\"$value\"";


		if ($type == "css")
		{
			if ($bottom)
				$head->innertext .= '<link href="'.$url.'"'.$add.' type="text/css" rel="stylesheet" media="screen" />'."\n";
			else
				$head->innertext = '<link href="'.$url.'"'.$add.' type="text/css" rel="stylesheet" media="screen" />'."\n" . $head->innertext;
		}
		else
		{
			if ($bottom)
				$head->innertext .= '<script src="'.$url.'"'.$add.' type="text/javascript"> </script>'."\n";
			else
				$head->innertext = '<script src="'.$url.'"'.$add.' type="text/javascript"> </script>'."\n" . $head->innertext;
		}

		$this->html($this->dom()->save());

		return $this;
	}

	public function embed_IE($url, $ltgt, $ver, $type = null, $bottom = false, $attrs = array())
	{
		if ($ltgt != "lt")
			$ltgt = "gt";

		$ver = (int)$ver;

		if ($type == null)
			$type = end(explode('.', $url));

		if ($type != "css")
			$type = "js";

		if (strpos($url, ':') === false)
			$url = site_url($url);

		if ($type == "css")
		{
			$all = $this->dom()->find('link[href]');
			$what = 'href';
		}
		else
		{
			$all = $this->dom()->find('script[src]');
			$what = 'src';
		}

		foreach ($all as $item)
		{
			if ($item->{$what} == $url)
				return $this;
		}

		$head = $this->dom()->find('head', 0);


		$add = '';
		foreach ($attrs as $attr=>$value)
			$add.=" $attr=\"$value\"";


		if ($type == "css")
		{
			if ($bottom)
				$head->innertext .= '<!--[if '.$ltgt.' IE '.$ver.']><link href="'.$url.'"'.$add.' type="text/css" rel="stylesheet" media="screen" /><![endif]-->'."\n";
			else
				$head->innertext = '<!--[if '.$ltgt.' IE '.$ver.']><link href="'.$url.'"'.$add.' type="text/css" rel="stylesheet" media="screen" /><![endif]-->'."\n" . $head->innertext;
		}
		else
		{
			if ($bottom)
				$head->innertext .= '<!--[if '.$ltgt.' IE '.$ver.']><script src="'.$url.'"'.$add.' type="text/javascript"> </script><![endif]-->'."\n";
			else
				$head->innertext = '<!--[if '.$ltgt.' IE '.$ver.']><script src="'.$url.'"'.$add.' type="text/javascript"> </script><![endif]-->'."\n" . $head->innertext;
		}

		$this->html($this->dom()->save());

		return $this;
	}

	public function set_encoding($enc='utf-8')
	{
		//force utf-8 encoding
		$meta = $this->dom()->find('head meta[content^=text]', 0);
		$tag_head = $this->dom()->find('head', 0);

		if (!empty($meta))
		{
			$meta->content = 'text/html; charset='.$enc;
		}
		else
		{

			if (!empty($tag_head))
				$tag_head->innertext = '<meta http-equiv="Content-Type" content="text/html; charset='.$enc.'" />'."\n".$tag_head->innertext;
		}

		$this->html($this->dom()->save());
	}

	public function prefix_relative_links($prefix=null)
	{
		if (empty($prefix))
		{
			$dir = dirname($this->file->path);

			if ($dir == '.')
				$prefix = '';
			else
				$prefix = $dir . '/';

		}

		$prefix = rtrim(site_url($prefix), '/') . '/';

		$links = $this->dom()->find('*[src], link[href], a[href]');

		//$html = $this->html();

		foreach ($links as $link)
		{
			//if ($link->tag == 'script')
				//continue;

			$what = 'src';
			if (in_array(strtolower($link->tag), array('link', 'a')))
				$what = 'href';

			$uri = $link->{$what};

			//skip no protocol loading (//domain.com), http[s]:, ftp[s]:, javascript:, #, or any other custom protocol [keep only relative links]
			if (strpos($uri, ':') !== false || strpos($uri, '#') === 0 || substr($uri, 0, 2) == '//')
				continue;

			$lnk_html = $link->outertext;

			$link->{$what} = $prefix.$uri;

			if (in_array(strtolower($link->tag), array('link', 'a')))
			{
				//var_dump($prefix);
			}

			//$html = str_replace($lnk_html, $link->outertext, $html);


		}
		//var_dump(current_url());

		//$this->html($html);
		$this->html($this->dom()->save());
	}

	public function head()
	{
		return $this->dom()->find('head', 0);
	}

	public function body()
	{
		return $this->dom()->find('body', 0);
	}

	public function get_page_contents()
	{
		return $this->content->get();
	}

	public function get_div_ids()
	{
		if ($this->file->is_processable())
			return dm_column($this->get_page_contents(), 'div');
		else
		{
			$divs = array();

			$blocks = $this->dom()->find('div[id]');

			foreach ($blocks as $block)
			{
				//skip div if it has class iu-skip
				if (stripos((string)$block->class, 'iu-skip') !== false)
					continue;

				//skip div if it contains other divs with id (except if it's marked with iu-content)
				$subdivs = $block->find('div[id]');
				if ($subdivs != null && (stripos((string)$block->class, 'iu-content') === false))
					continue; //*/

				if (!empty($block->id))
					$divs[] = $block->id;
			}

			return $divs;
		}

	}

	public function get_css_files($absolute = TRUE)
	{
		$f = $this->get_clone();

		if ($absolute)
			$f->prefix_relative_links();

		$csss = array();
		$links = $f->dom()->find('link[href]');

		foreach ($links as $l)
			$csss[] = $l->href;

		return $csss;
	}

	public function set_meta($name, $content)
	{
		$meta = $this->dom()->find('head meta[name='.$name.']', 0);

		if (!empty($meta))
		{
			$meta->content = $content;
		}
		else
		{
			$content = str_replace('"', '\"', $content);
			$tag_head = $this->dom()->find('head', 0);
			if (!empty($tag_head))
				$tag_head->innertext .= '<meta name="'.$name.'" content="'.$content.'" />'."\n";
		}

		$this->html($this->dom()->save());
	}

	public function cache_duration()
	{
		if ($this->custom_caching)
			return $this->custom_caching_duration;
		else
			return (int)Setting::value('cache_duration', 0);
	}

	public static function array_tree($set = null)
	{
		if (empty($set))
		{
			$set = self::factory();
			$set->get();
		}

		$arr = array();

		foreach ($set as $p)
			$arr[$p->uri] = $p->uri;

		return explode_tree($arr, '/', true);
	}

	public static function html_tree($array_tree=null, $root=true)
	{
		if (empty($array_tree))
			$array_tree = self::array_tree();

		if ($root)
			$return = '<ul id="pages-tree">'."\n";
		else
			$return = '<ul class="folder-list">'."\n";

		foreach ($array_tree as $el=>$path)
		{
			if (is_array($path))
			{
				//folder
				$return .= '<li class="folder"><span>' . $el."</span>\n";
				$return .= self::html_tree($path, false);
				$return .= '</li>'."\n";
			}
			else
			{
				//file
				$ext = end(explode('.', $path));
				if (empty($ext))
					$ext = 'na';

				$return .= '<li class="file ext_'.$ext.'" data-uri="'.$path.'"><span>'.$el.'</span></li>'."\n";
			}
		}

		$return .= '</ul>'."\n";
		return $return;
	}

	public static function fix_js_comments($html)
	{
		//match script tags
		preg_match_all('%<script.*>(.*)</script>%simxU', $html, $matches);

		//get whole tags and only codes
		$whole_tags = $matches[0];
		$codes = $matches[1];

		//loop through all the tags
		for ($i=0; $i<count($whole_tags); $i++)
		{
			$tag = $whole_tags[$i];
			$code = $codes[$i];
			$trimmed_code = trim($code);

			//skip empty script tags (<script src...)
			if (empty($trimmed_code))
				continue;

			//match comments
			preg_match_all('%[^:\'"=]//(.*)%imx', $code, $comments);

			$comments = $comments[0];

			$newtag = $tag;

			for ($j=0; $j<count($comments); $j++)
			{
				//get comment
				$comment = $comments[$j];
				//convert //comment to /* comment */
				$newcomment = str_replace('//', '/* ', $comment) . '*/';
				//put new comment into tag
				$newtag = str_replace($comment, $newcomment, $newtag);
			}

			//put new tag into html
			$html = str_replace($tag, $newtag, $html);

		}

		return $html;
	}

	public static function gzip_output($html)
	{
		$HTTP_ACCEPT_ENCODING = $_SERVER['HTTP_ACCEPT_ENCODING'];
		if( headers_sent() ){
			$encoding = false;
		}elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
			$encoding = 'x-gzip';
		}elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
			$encoding = 'gzip';
		}else{
			$encoding = false;
		}

		if( $encoding ){
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($html);
			$html = gzcompress($html, 9);
			$html = substr($html, 0, $size);
		}

		die($html);
	}

	public function url()
	{
		return site_url($this->uri);
	}
}


?>