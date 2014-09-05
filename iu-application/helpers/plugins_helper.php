<?php defined('BASEPATH') OR exit('No direct script access allowed');

class PluginManager {

	protected $plugins = array();
	protected $actions = array();
	protected static $instance = false;
	protected $dir = 'iu-resources/plugins/';

	private function __construct($dir = false)
	{
		if (!empty($dir))
			$this->dir = rtrim($dir, '/') . '/';

		self::$instance = $this;

		$this->load_plugins();
	}

	public static function plugin($slug)
	{
		if (is_file(self::instance()->dir . $slug . '/' . $slug.'.php'))
		{
			$reporting = error_reporting(E_ERROR);
			require_once(realpath(self::instance()->dir . $slug . '/' . $slug.'.php'));
			error_reporting($reporting);

			$classname = str_replace('-', '_', $slug);

			return new $classname;

		}
		else
			return false;
	}

	protected function load_plugins()
	{
		$this->plugins = Plugin::factory()->where('active', true)->get();


		for ($i=0; $i<count($this->plugins->all); $i++)
		{
			$plugin = $this->plugins->all[$i];

			if (is_file($this->dir . $plugin->slug . '/' . $plugin->slug.'.php'))
			{
				$reporting = error_reporting(E_ERROR);
				require_once(realpath($this->dir . $plugin->slug . '/' . $plugin->slug.'.php'));
				error_reporting($reporting);

				$classname = str_replace('-', '_', $plugin->slug);

				$this->plugins->all[$i]->object = new $classname;

			}
			else
			{
				$pluginn = $plugin->name;
				$plugin->delete();

				$this->controller()->templatemanager->notify("Plugin $pluginn is missing, therefore it's removed from the system!");
			}
		}
	}

	public static function all_loaded()
	{
		return self::instance()->plugins->all;

	}

	public static function list_all()
	{
		$all = scandir(self::instance()->dir);

		for ($i=0;$i<count($all); $i++)
		{
			$curr = $all[$i];
			if (!is_dir(self::instance()->dir . $curr) || $curr == '.' || $curr == '..')
			{
				unset($all[$i]);
			}
		}

		return $all;
	}

	public static function plugin_info($slug)
	{
		$plg = self::instance()->plugin($slug);

		if (!$plg)
			return false;
		else
			return $plg->get_info();
	}

	public static function instance($dir = false)
	{
		//var_dump(self::$instance);

		if (self::$instance == false)
			self::$instance = new PluginManager($dir);

		return self::$instance;
	}

	public static function add_action($hook_name, $function, $priority = 10, $plugin_slug = NULL)
	{
		if (empty($plugin_slug))
			$plugin_slug = $function[0]->get_info('slug');

		//var_dump($plugin); die;

		if (empty(self::instance()->actions[$hook_name]))
			self::instance()->actions[$hook_name] = array();

		self::instance()->actions[$hook_name][] = array('plugin'=>$plugin_slug, 'function'=>$function, 'priority'=>$priority);

		return true;
	}

	public function do_actions($hook_name, $args = array(), $plugins = array())
	{
		$actions = isset(self::instance()->actions[$hook_name]) ? self::instance()->actions[$hook_name] : NULL ;

		if (empty($actions))
			return $args;

		if (!empty($plugins) && !is_array($plugins))
			$plugins = array($plugins);

		usort($actions, array('PluginManager', 'sort_actions'));

		$return = $args;
		foreach ($actions as $action)
		{
			if (empty($plugins) || in_array($action['plugin'], $plugins))
				$return = call_user_func_array($action['function'], is_array($args) ? $args : array($args));
		}
		return $return;
	}

	protected static function sort_actions($a, $b, $by_what = 'priority')
	{
		if (!isset($a[$by_what]) || !isset($b[$by_what]))
			return 0;

		if ($a[$by_what] == $b[$by_what])
			return 0;

		return ($a[$by_what] < $b[$by_what]) ? -1 : 1;
	}

}

class PluginException extends Exception {

}



abstract class PluginBase {

	protected $info = array();
	protected $_ci = NULL;
	protected $_orm = NULL;
	public $page_slug = 'extend';

	public function __construct()
	{
		$this->info = $this->info();

		$slug = isset($this->info['slug']) ? trim($this->info['slug']) : FALSE ;
		if (empty($slug))
			$this->info['slug'] = $this->slug();

		//version check
		$ver = get_app_version(true);
		$min_ver = $this->get_info('min_ver');

		if (!empty($min_ver))
		{
			$min_ver = format_version($min_ver, true);

			if ($ver < $min_ver)
			{
				//not required version, disallow!
				$plg = Plugin::factory()->get_by_slug($this->get_info('slug'));
				if ($plg->exists())
				{
					$plg->active = FALSE;
					$plg->save();
				}

				$ver = format_version($ver);
				$min_ver = format_version($min_ver);
				$lnk = site_url('administration/dashboard');
				$plglnk = anchor($this->get_info('url'), $this->get_info('name'));

				show_error("Version mismatch for plugin $plglnk! ".
					"You are running version $ver and minimum required version is $min_ver. ".
					"Plugin is disabled, please <a href='$lnk'>click here</a>!");
			}
		}


	}

	public static abstract function info();

	public function install()
	{
		/* do nothing by default */
	}

	public function uninstall()
	{
		/* do nothing by default */
	}

	public function get_info($item = NULL)
	{
		if (empty($item))
			return $this->info;
		else
		{
			if (isset($this->info[$item]))
				return $this->info[$item];
			else
				return FALSE;
		}
	}


	protected function db()
	{
		if (empty($this->_orm))
			$this->_orm = Plugin::factory()->get_by_slug($this->get_info('slug'));

		return $this->_orm;
	}

	protected function add_action($hook_name, $function, $priority = 10)
	{
		return PluginManager::add_action($hook_name, array($this, $function), $priority, $this->get_info('slug'));
	}

	protected function slug()
	{
		if (isset($this->info['slug']))
			return $this->info['slug'];

		$name = isset($this->info['name']) ? trim($this->info['name']) : false ;
		if (empty($name))
			throw new PluginException("Plugin name not defined! Can not create slug!");

		if (function_exists('cyr_url_title'))
			return cyr_url_title($name);
		else
			return url_title($name, 'dash', true);
	}

	protected function iu()
	{
		if (!empty($this->_ci))
			return $this->_ci;
		else
		{
			return $this->_ci = &get_instance();
		}
	}

	protected function set_data($key, $value)
	{
		$data = empty($this->db()->data) ? array() : json_decode($this->db()->data);

		$data[$key] = $value;
		$this->db()->data = json_encode($data);
		$this->db()->save();

		return !empty($data[$key]);
	}

	protected function get_data($key=NULL)
	{
		$data = empty($this->db()->data) ? array() : json_decode($this->db()->data);

		if (empty($key))
			return $data;
		else if (isset($data[$key]))
			return $data[$key];
		else
			return FALSE;

	}

	protected function page_url($uri)
	{
		return site_url('administration/'.$this->page_slug.'/'.$this->get_info('slug').'/'.$uri);
	}

	protected function page_uri()
	{
		$page = ltrim( str_replace('administration/'.$this->page_slug.'/'.$this->get_info('slug'), '', $this->controller()->uri->uri_string()), '/');

		return (empty($page)) ? 'index' : $page;
	}

	protected function page_args($n=5, $default = array())
	{
		return $this->iu()->uri->uri_to_assoc($n, $default);
	}

	protected function plugin_url($uri='')
	{
		return site_url($this->plugin_dir().$uri);
	}

	protected function plugin_dir()
	{
		return 'iu-resources/plugins/'.$this->get_info('slug').'/';
	}

	protected function load_file($filename, $variables=array())
	{
		ob_start();
		$plugin = &$this;
		foreach ($variables as $var=>$val)
			$$var = $val;

		if (is_file($this->plugin_dir().$filename))
		{
			require $this->plugin_dir().$filename;
		}
		else
		{
			echo "File $filename not found in ". $this->plugin_dir()."!";
		}
		return ob_get_clean();
	}

	protected function embed($file_url, $type=NULL)
	{
		echo embed($this->plugin_url($file_url), $type);
	}

	public function json_output($output)
	{
		die(json_encode($output));
	}


}

//EOF