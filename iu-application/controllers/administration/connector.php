<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Connector extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);
	}

	//jQuery FileTree connector (pass regex of allowed extensions)
	private function filetree($extension_regex='/php[1-9]?|p?html?$/si')
	{
		$root = './';
		$dir = urldecode($this->input->post('dir', true));
		$proj_dirs = array('iu-application', 'iu-system', 'install', 'iu-resources', '.tmb', '.quarantine');

		if( file_exists($root . $dir) ) {
			$files = scandir($root . $dir);
			natcasesort($files);
			if( count($files) > 2 ) { /* The 2 accounts for . and .. */
				echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
				// All dirs
				foreach( $files as $file ) {
					if( file_exists($root . $dir . $file) && $file != '.' && $file != '..' && $file != '.tmb' && $file != '.quarantine' && !in_array($dir.$file, $proj_dirs) && is_dir($root . $dir . $file) ) {
						echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "/\">" . htmlentities($file) . "</a></li>";
					}
				}
				// All files
				foreach( $files as $file ) {
					if( file_exists($root . $dir . $file) && $file != '.' && $file != '..' && ($dir.$file !== 'index.php') && ($dir.$file !== 'installed.txt') && ($dir.$file !== 'license.txt') && !is_dir($root . $dir . $file) ) {
						$ext = preg_replace('/^.*\./', '', $file);

						if (!preg_match($extension_regex, $ext) || strlen($ext) > 5)
							continue;

						echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($dir . $file) . "\">" . htmlentities($file) . "</a></li>";
					}
				}
				echo "</ul>";
			}
		}
	}

	public function templates_onlyhtmlphp()
	{
		$this->filetree('/php[1-9]?|p?html?$/si');
	}

	public function templates()
	{
		$this->filetree('/php[1-9]?|p?html?|tpl|css|js|htc|htaccess|jpe?g|png|gif$/si');
	}

	//elFinder connector (pass regex of allowed extensions and root)
	public function elfinder($extension_regex=NULL, $root = '')
	{
		$this->load->helper('path');

		//default attributes
		$attributes = array(
			array(
			    'pattern' => '/error_log|iu-application|iu-resources|iu-system|install|license\.txt|installed\.txt|\.htaccess|\.htpasswd|\.tmb|\.quarantine/i',
			    'read' => false,
			    'write' => false,
			    'hidden' => true,
			    'locked' => false
			)
		);

		if (!empty($extension_regex))
		{
			$attributes[] = array(
			    'pattern' => $extension_regex,
			    'read' => false,
			    'write' => false,
			    'hidden' => true,
			    'locked' => false
			);
		}

		$opts = array(
			// 'debug' => true,
			'roots' => array(
				array(
					'driver' => 'LocalFileSystem',
					'alias' => __("Assets"),
					'path'   => set_realpath('./' . (empty($root) ? '' : $root)),
					'URL'    => site_url((empty($root) ? '' : $root)),
					// more elFinder options here
					'attributes' => $attributes,
					'' => ''
				)
			)
		);
		$this->load->library('elfinderlib', $opts);
	}

	public function browse()
	{
		$this->elfinder();
	}

	public function assets()
	{
		if($this->user->can('edit_all_assets'))
		{
			$this->elfinder(NULL, trim(Setting::value('assets_folder', 'iu-assets'), '/'));
		}
		else
		{
			@mkdir(Setting::value('assets_folder', 'iu-assets').'/'.$this->user->id, 0777, true);
			$this->elfinder(NULL, trim(Setting::value('assets_folder', 'iu-assets').'/'.$this->user->id, '/'));
		}

	}


}


?>