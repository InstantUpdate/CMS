<?php

define('CS_ADMIN_CONTROLLER_FOLDER', 'administration');
define('CS_PRODUCT_NAME', "Instant Update");


class CS_Controller extends CI_Controller
{
	public $user = null;

	public function __construct()
	{
		parent::__construct();

		//$this->output->enable_profiler(TRUE);

		/*//reconstruct _GET array
		$qs = urldecode($_SERVER['QUERY_STRING']);
		if (strpos($qs, '?') !== FALSE)
		{
			$_GET = array();
			$cqs = end(explode('?', $qs));
			parse_str($cqs, $_GET);
		}//*/

		//check if config file is empty or it's not empty but script is not installed
		if ((is_db_conf_empty() || !is_installed()) && !defined('CS_EXTERNAL'))
			redirect("setup/index");

		//load database, datamapper and login manager
		$this->load->database();
		$this->load->library('datamapper');
		$this->load->library('translate');
		$this->load->library('loginmanager');

		//hack for datamapper prefix
		DataMapper::$config['prefix'] = $this->db->dbprefix;

		//set web site name in title
		$this->templatemanager->set_title(Setting::value('website_title', CS_PRODUCT_NAME));


		//test if should save uri
		$should = true;

		if ($this instanceof Process)
		{
			$uri = $path = trim($this->uri->uri_string());

			$file = new File();
			$file->path = $path;
			$mime = $file->mime_type();

			if ($mime !== 'text/html')
			{
				$should = false;
			}
		}




		//set current url for auth controller to know where to redirect
		if (!$this instanceof Auth && !$this instanceof JS && !$this->is_ajax_request() && $should)
			$this->loginmanager->set_redirect(current_url());

		//set time zone
		date_default_timezone_set(Setting::value('default_time_zone', 'Europe/Belgrade'));

		//set language
		$sess_lang = $this->session->userdata('lang');
		if (!empty($sess_lang))
		{
			if (is_numeric($sess_lang))
				$lang = Language::factory()->get_by_id((int)$sess_lang)->name;
			else
				$lang = $sess_lang;
		}
		else
			$lang = Setting::value('default_language', 'English');


		$this->translate->set_language($lang);

		//fetch user from the database if logged in
		if ($this->loginmanager->is_logged_in())
		{
			$this->user = User::factory($this->loginmanager->user->id);
			$this->templatemanager->assign('user', $this->user);
		}

		$this->templatemanager->set_template_name( $this->in_admin() ? "administration" : "" );

		if ($this->in_admin() && isset($_GET['iu-popup']))
			$this->templatemanager->popup();

	}

	public function in_admin()
	{
		$object = new ReflectionObject($this);
		$class_path = $object->getMethod('__construct')->getDeclaringClass()->getFilename();
		return ((strpos(current_url(), CS_ADMIN_CONTROLLER_FOLDER) !== false) && (strpos($class_path, CS_ADMIN_CONTROLLER_FOLDER) !== false));
	}

	public function http_get($url)
	{
		if (isset($this->curl) && $this->curl->is_enabled())
			return $this->curl->simple_get($url);
		else
			return file_get_contents($url);
	}

	public function is_ajax_request()
	{
		return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
	}

}

?>