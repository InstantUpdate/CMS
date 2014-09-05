<?php defined('BASEPATH') OR exit('No direct script access allowed');

class TemplateManager {

	public $tpl_data = array();
	private $framework;
	private $tpl_name = 'default';
	public $title_pieces = array();
	public $messages = array();
	public $embeds = array();
	public $domready = array();
	public $globalJS = array();

	public $config = array(
		'in_popup' => false,
		'has_header' => true,
		'has_footer' => true,
		'has_sidebar' => true
	);

	public function __construct()
	{
		$this->set_template_name();
		$this->framework = $this->tpl_data['system'] = &get_instance(); //can't use assign() because of by-referrence passing
		$this->tpl_data['template'] = &$this; //can't use assign() because of by-referrence passing


		$flashes = $this->framework->session->flashdata('notifications');
		if (!empty($flashes))
			$this->messages = $flashes;


	}

	public function on_dom_ready($dom_ready_js_file, $array_replacements = null)
	{
		is_file($this->base_path().$dom_ready_js_file) || show_error("No file found at {$this->base_path()}{$dom_ready_js_file}");

		$js_code = file_get_contents($this->base_path().$dom_ready_js_file);

		if ($array_replacements != null && is_array($array_replacements))
		{
			foreach ($array_replacements as $var=>$value)
			{
				$js_code = str_replace('%'.$var.'%', $value, $js_code);
			}
		}

		$this->domready[] = $js_code;
		return $this;
	}

	public function write_dom_ready_js()
	{
		echo implode("\n", $this->domready);
	}

	public function global_js_variable($var, $value=null)
	{
		if ($value === null)
		{
			$this->globalJS[] = "var $var;\n";
		}
		else
		{
			if (is_string($value))
				$this->globalJS[] = 'var $var = "'.str_replace('"', '\"', $value).'";'."\n";
			else
				$this->globalJS[] = "var $var = $value;\n";
		}

		return $this;
	}

	public function write_global_js_variables()
	{
		echo implode("\n", $this->globalJS);
	}



	public function embed($url, $type = null)
	{
		if ($type == null)
			$type = end(explode('.', $url));

		if ($type != "css")
			$type = "js";

		if (strpos($url, ':') == false)
			$url = $this->base_url() . $url;

		$index = count($this->embeds);
		$this->embeds[$index]['url'] = $url;
		$this->embeds[$index]['type'] = $type;
		return $this;
	}

	public function embed_all()
	{
		foreach ($this->embeds as $item)
		{
			if ($item['type'] == "css")
				echo '<link href="'.$item['url'].'" type="text/css" rel="stylesheet" media="screen" />'."\n";
			else
				echo '<script src="'.$item['url'].'" type="text/javascript"></script>'."\n";
		}
	}

	public function notify($text, $type = 'notice', $title = null)
	{
		if (empty($title))
			$title = ucfirst($type);

		$nr = count($this->messages);
		$this->messages[] = array('id'=>$nr+1, 'type'=>$type, 'text'=>$text, 'title'=>$title);
		return $this;
	}

	public function notify_next($text, $type = 'notice', $title = null)
	{
		if (empty($title))
			$title = __(ucfirst($type));

		$flashes = $this->framework->session->flashdata('notifications');
		if (empty($flashes))
			$flashes = array();

		$nr = count($flashes);
		$flashes[] = array('id'=>$nr+1, 'type'=>$type, 'text'=>$text, 'title'=>$title);
		$this->framework->session->set_flashdata('notifications', $flashes);
	}

	public function set_template_name($template_name = "default")
    {
        $this->tpl_name = $template_name;
        return $this;
    }

    public function get_template_name()
    {
		return $this->tpl_name;
	}

    public function show_template($filename)
	{
		$this->load_template('header')
				->load_template($filename)
				->load_template('footer');
	}

    public function load_template($filename)
    {
		$this->framework->load->view($this->get_template_name() . '/' . $filename, $this->tpl_data);
		return $this;
	}

	public function make_title($glue = " | ")
	{
		$this->assign('title', implode($glue, array_reverse($this->title_pieces) ));
		return $this;
	}

	public function set_title($title, $glue = " | ")
	{
		$this->title_pieces[] = $title;
		$this->make_title($glue);
		return $this;
	}

	public function reset_title($title = null, $glue = " | ")
	{
		$this->title_pieces = array();
		if (!empty($title))
		{
			$this->title_pieces[] = $title;
			$this->make_title($glue);
		}
		return $this;
	}

	public function assign($name, $value)
	{
		$this->tpl_data[$name] = $value;
		return $this;
	}

	public function assign_by_ref($name, &$value)
	{
		$this->tpl_data[$name] = &$value;
		return $this;
	}

	/**
	 * TemplateManager::popup()
	 *
	 * @param boolean $in_popup
	 * @return TemplateManager object
	 */
	public function popup($in_popup = true)
	{
		if (!$in_popup)
		{
			$this->config = array(
				'in_popup' => false,
				'has_header' => true,
				'has_footer' => true,
				'has_sidebar' => true
			);
		}
		else
		{
			$this->config = array(
				'in_popup' => true,
				'has_header' => false,
				'has_footer' => false,
				'has_sidebar' => false
			);
		}
		return $this;
	}

	/**
	 * TemplateManager::set_header()
	 *
	 * @param boolean $has_header
	 * @return TemplateManager object
	 */
	public function set_header($has_header = true)
	{
		$this->config['has_header'] = $has_header;
		return $this;
	}

	public function set_footer($has_footer = true)
	{
		$this->config['has_footer'] = $has_footer;
		return $this;
	}

	/**
	 * TemplateManager::set_sidebar()
	 *
	 * @param boolean $has_sidebar
	 * @return TemplateManager object
	 */
	public function set_sidebar($has_sidebar = true)
	{
		$this->config['has_sidebar'] = $has_sidebar;
		return $this;
	}

	/**
	 * TemplateManager::base_url()
	 *
	 * @return string URL to template folder
	 */
	public function base_url()
	{
		return base_url() . $this->base_path();
	}

	/**
	 * TemplateManager::base_path()
	 *
	 * @return string Relative path to template folder
	 */
	public function base_path()
	{
		return basename(realpath(APPPATH)) . '/views/' . $this->tpl_name . '/';
	}

}

?>