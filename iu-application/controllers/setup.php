<?php defined('BASEPATH') OR exit('No direct script access allowed');

define('CS_SETUP_VERSION', '0.1');

define('CS_CHECK_PHP_MODULE', 1);
define('CS_CHECK_SERVER_MODULE', 2);
define('CS_CHECK_FUNCTION', 3);
define('CS_CHECK_PHP_INI', 4);

class Setup extends CI_Controller {

	//set requirements [requirement name=>required?]
	private $require_writable_files = array("installed.txt"=>true, 'iu-assets/'=>true, 'iu-application/cache/'=>true, 'iu-resources/geoip/'=>true, 'iu-resources/images/'=>true, 'iu-resources/images/thumbs/'=>true, 'iu-resources/uploads/'=>true, 'iu-resources/uploads/thumbs/'=>true);
	private $require_php_modules = array("GD"=>true, "cURL"=>false, "MySQL"=>true);
	private $require_server_modules = array("mod_rewrite"=>false, "mod_env"=>false, "mod_deflate"=>false);
	private $require_php_ini = array("allow_url_fopen"=>false);

	//don't edit below
	private $upgrade = false;

	public function __construct()
	{
		parent::__construct();

		$this->templatemanager->set_template_name("setup");
		$this->templatemanager->set_title(CS_PRODUCT_NAME . " " . CS_SETUP_VERSION . " Setup");

		//check for possible upgrade
		if (is_installed())
		{
			if ((get_app_version() == CS_SETUP_VERSION) && ($this->uri->segment(2) != "help") && ($this->uri->segment(2) != "finish"))
				redirect("setup/help/same-version");
		}

		if (is_installed() && (get_app_version() != CS_SETUP_VERSION))
			$this->upgrade = true;

		$this->set_navigation();

		//$this->output->enable_profiler(TRUE);
	}

	private function set_navigation($nav = null)
	{
		$nav = array("Selfcheck"=>"selfcheck", "Database"=>"database", "Installation"=>"sql", "Help"=>"help");

		$this->templatemanager->assign("navigation", $nav);
	}

	private function check_module($name, $type = null)
	{
		$server = array('mod_security', 'mod_win32', 'mpm_winnt', 'http_core', 'mod_so', 'mod_actions', 'mod_alias', 'mod_asis', 'mod_auth_basic', 'mod_auth_digest', 'mod_authn_default', 'mod_authn_file', 'mod_authz_default', 'mod_authz_groupfile', 'mod_authz_host', 'mod_authz_user', 'mod_autoindex', 'mod_cgi', 'mod_dav_lock', 'mod_dir', 'mod_env', 'mod_headers', 'mod_include', 'mod_info', 'mod_isapi', 'mod_log_config', 'mod_mime', 'mod_negotiation', 'mod_proxy', 'mod_proxy_ajp', 'mod_rewrite', 'mod_setenvif', 'mod_ssl', 'mod_status', 'mod_php5', 'mod_perl');
		$php = array('bcmath', 'calendar', 'com_dotnet', 'ctype', 'date', 'ereg', 'filter', 'ftp', 'hash', 'iconv', 'json', 'mcrypt', 'SPL', 'odbc', 'pcre', 'Reflection', 'session', 'standard', 'mysqlnd', 'tokenizer', 'zip', 'zlib', 'libxml', 'dom', 'PDO', 'bz2', 'SimpleXML', 'wddx', 'xml', 'xmlreader', 'xmlwriter', 'apache2handler', 'Phar', 'curl', 'mbstring', 'exif', 'gd', 'gettext', 'imap', 'mysql', 'mysqli', 'pdo_mysql', 'PDO_ODBC', 'pdo_sqlite', 'soap', 'sockets', 'SQLite', 'sqlite3', 'xmlrpc', 'ming', 'mhash');

		$name = strtolower($name);

		if (empty($type))
		{
			if (in_array($name, $php))
				$type = CS_CHECK_PHP_MODULE;
			else if (in_array($name, $server))
				$type = CS_CHECK_SERVER_MODULE;
			else
				$type = CS_CHECK_FUNCTION;
		}

		switch ($type)
		{
			case CS_CHECK_PHP_MODULE:
				return extension_loaded($name);
				break;
			case CS_CHECK_SERVER_MODULE:
				if (function_exists('apache_get_modules'))
					return in_array($name, apache_get_modules());
				else
					return -1;
				break;
            case CS_CHECK_PHP_INI:
                return (boolean)ini_get($name);
                break;
			default:
				return function_exists($name);
				break;

		}
	}

	private function get_help_title($keyword)
	{
		if (is_file(FCPATH . "install/help/" . $keyword . ".txt"))
		{
			$rows = file(realpath(FCPATH . "install/help/" . $keyword . ".txt"));
			return strip_tags($rows[0]);
		}
		else
			return false;

	}

	// "pages"

	public function help($keyword = "welcome")
	{
		if (is_file(FCPATH . "install/help/" . $keyword . ".txt"))
		{
			$content = file_get_contents(realpath(FCPATH . "install/help/" . $keyword . ".txt"));

			$vars = array(
				'base_url'=>$this->templatemanager->base_url(),
				'site_url'=>site_url(),
				'product_name'=>CS_PRODUCT_NAME,
				'product_version'=>get_app_version(),
				'setup_version'=>CS_SETUP_VERSION
			);

			foreach ($vars as $var=>$value)
			{
				$content = str_replace('%'.$var.'%', $value, $content);
			}
		}
		else
			$content = "<p>No help file found: install/help/" . $keyword . ".txt</p>";

		$this->templatemanager->set_title("Help");
		$this->templatemanager->set_title($this->get_help_title($keyword));

		if ($keyword == "welcome")
		{
			$files = scandir(realpath(FCPATH . "install/help/"));
			array_shift($files); array_shift($files);
			$topics = array();

			foreach ($files as $file)
			{
				$title = $this->get_help_title(reset(explode('.', $file)));

				$topics[$title] = reset(explode('.', $file));
			}

			$content .= "\n<ul class=\"cs-help-topics\">\n";

			foreach ($topics as $title=>$link)
			{
				if ($link != "welcome" && !empty($title))
					$content .= '<li><a href="'.site_url("setup/help/".$link).'">'.$title.'</a></li>'."\n";
			}

			$content .= "\n</ul>\n";

		}

		$this->templatemanager->assign("content", $content);

		//$this->set_navigation(array("Selfcheck"=>"selfcheck", "Help"=>"help"));

		$this->templatemanager->show_template("help");
	}

	public function index()
	{
		redirect("setup/selfcheck");
	}

	public function selfcheck()
	{
		$this->templatemanager->set_title("System requirements");

		$writable = array();
		$all_writable = true;

		foreach ($this->require_writable_files as $file=>$required)
		{
			if (is_really_writable(realpath(FCPATH.$file)))
				$writable[$file] = true;
			else
			{
				$writable[$file] = false;
				if ($required)
					$all_writable = false;
			}
		}

		$this->templatemanager->assign("files", $writable);
		$this->templatemanager->assign("all_files_writable", $all_writable);


		$modules = array();
		$all_modules = true;
		foreach ($this->require_php_modules as $module=>$required)
		{
			if ($this->check_module($module, CS_CHECK_PHP_MODULE))
				$modules[$module] = true;
			else
			{
				$modules[$module] = false;
				if ($required)
					$all_modules = false;
			}
		}

		foreach ($this->require_server_modules as $module=>$required)
		{
			if ($this->check_module($module, CS_CHECK_SERVER_MODULE))
				$modules[$module] = true;
			else
			{
				$modules[$module] = false;
				if ($required)
					$all_modules = false;
			}
		}

        $this->templatemanager->assign('modules', $modules);
		$this->templatemanager->assign('all_modules', $all_modules);

        $confs = array();
        $all_confs = true;

        foreach ($this->require_php_ini as $module=>$required)
		{
			if ($this->check_module($module, CS_CHECK_PHP_INI))
				$confs[$module] = true;
			else
			{
				$modules[$module] = false;
				if ($required)
					$all_confs = false;
			}
		}


		$this->templatemanager->assign('inis', $confs);
		$this->templatemanager->assign('all_inis', $all_confs);

		$next = ($this->upgrade) ? "sql" : "database";
		$this->templatemanager->assign('next', $next);


		$this->templatemanager->show_template("selfcheck");

	}

	public function database()
	{

		if (!is_db_conf_empty())
		{
			//if config file exists, load libraries
			$this->load->database();
			$this->load->library('datamapper');

			DataMapper::$config['prefix'] = $this->db->dbprefix;
		}
		else
		{
			redirect("setup/help/database-config-missing");
		}



		$db_info = array();
		$db_info["dbprefix"] = DataMapper::$config['prefix'];
		$db_info["dbdriver"] = $this->db->dbdriver;
		$db_info["hostname"] = $this->db->hostname;
		$db_info["username"] = $this->db->username;
		$db_info["password"] = $this->db->password;
		$db_info["database"] = $this->db->database;
		$db_info["char_set"] = $this->db->char_set;

		$this->templatemanager->assign("database", $db_info);

		//$this->set_navigation(array("Selfcheck"=>"selfcheck", "Database"=>"database", "Help"=>"help"));

		$this->templatemanager->show_template("database");
	}

	public function sql()
	{
		//we cannot install if database config file is empty
		if (!is_db_conf_empty())
		{
			//if config file exists, load libraries
			$this->load->database();
			$this->load->library('datamapper');
			$this->load->library('multiquery');

			DataMapper::$config['prefix'] = $this->db->dbprefix;
		}
		else
		{
			redirect("setup/help/database-config-missing");
		}


		//set db prefix for multiquery
		$this->multiquery->assign('prefix', DataMapper::$config['prefix']);

		//tell template if we're upgrading
		$this->templatemanager->assign('upgrade', $this->upgrade);

		if (!$this->upgrade)
		{
			//clean install
			$ok = $this->multiquery->execute_file(realpath(FCPATH . "install/sql/tables.sql"));
			$errors = $this->multiquery->errors;

			if (!$ok)
				$this->templatemanager->assign('errors', $errors);
		}
		else
		{
			//upgrade
			$old_version = get_app_version(true);
			$new_version = format_version(CS_SETUP_VERSION, true);

			for ($i = $old_version+1; $i <= $new_version; $i++)
			{
				$step = format_version($i);

				$steps = array();

				$step_has_sql = is_file(FCPATH . "install/sql/$step.sql");
				$step_has_php = is_file(FCPATH . "install/php/$step.php");
				$step_has_clear_sql = is_file(FCPATH . "install/sql/$step-clear.sql");

				//execute sql upgrade (if exists)
				if ($step_has_sql)
				{
					$ok = $this->multiquery->execute_file(realpath(FCPATH . "install/sql/$step.sql"));
					$steps[$step]['ver'] = $step;
					if ($ok)
					{
						$steps[$step]['ok'] = true;
					}
					else
					{
						$steps[$step]['ok'] = false;
						$steps[$step]['errors'] = $this->multiquery->errors;
					}

				}

				//execute php upgrade logic (if exists)
				if ($step_has_php)
				{
					ob_start();
					require_once(FCPATH . "install/php/$step.php");
					$steps[$step]['upgrade-output'] = '';

					while(ob_get_level())
						$steps[$step]['upgrade-output'] .= ob_get_clean();
				}

				//clear sql
				if ($step_has_clear_sql)
					$this->multiquery->execute_file(realpath(FCPATH . "install/sql/$step-clear.sql"));

				//write upgraded version to "installed.txt"
				if ($step_has_sql || $step_has_php)
					file_put_contents(FCPATH . "installed.txt", $step);

				$this->templatemanager->assign('steps', $steps);

			}
		}

		$this->templatemanager->show_template("sql");
	}

	public function createadmin()
	{
		if (is_db_conf_empty())
			redirect('setup/help/database-config-missing');

		$this->templatemanager->show_template("createadmin");

	}

	private function saveadmin_error($msg)
	{
		$this->templatemanager->assign("error", $msg);
		$this->templatemanager->show_template("createadmin");
		//die();
	}

	public function saveadmin()
	{
		//we cannot save admin if database config file is empty
		if (!is_db_conf_empty())
		{
			//if config file exists, load libraries
			$this->load->database();
			$this->load->library('datamapper');
			DataMapper::$config['prefix'] = $this->db->dbprefix;
		}
		else
		{
			redirect("setup/help/database-config-missing");
		}

		//now create the mofo admin
		$role = UserRole::factory()
					->where('name', 'Administrator')
					->limit(1)
					->get();

		$user = new User();

		$perms = Permission::factory()
					//->where_related_userrole('id', $role->id)
					->get();

		$name = $this->input->post('name');
		$email = $this->input->post('email');

		if (empty($name))
		{
			$this->saveadmin_error("You need to specify administrator's name.");
		}
		else if (empty($email))
		{
			$this->saveadmin_error("You need to specify administrator's e-mail address.");
		}
		else
		{
			$user->name = $name;
			$user->email = $email;
			$user->active = 1;

			$password = trim($this->input->post('password'));
			$password2 = trim($this->input->post('password2'));

			if (empty($password))
			{
				$this->saveadmin_error("You must enter administrator's password.");
			}
			else
			{
				if ($password != $password2)
				{
					$this->saveadmin_error("Entered passwords differ.");
				}
				else
				{
					$user->password = $password;
					$user->save(array($role, $perms->all));
					redirect("setup/finish");
				}

			}

		}

	}

	public function serverinfo()
	{
		if (!is_installed())
			phpinfo();
		else
			die('Not allowed!');
	}


	public function finish()
	{
		if (is_db_conf_empty())
			redirect('setup/help/database-config-missing');

		file_put_contents(FCPATH."installed.txt", format_version(CS_SETUP_VERSION));

		$this->templatemanager->assign('login_url', 'administration/dashboard');
		$this->templatemanager->show_template("finish");

	}



}

?>
