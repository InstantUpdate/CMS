<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Extend extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$db_plugins = dm_column(Plugin::factory()->get(), 'slug');
		$fs_plugins = PluginManager::list_all();

		foreach ($fs_plugins as $plg)
		{
			if (!in_array($plg, $db_plugins))
			{
				$pl = PluginManager::plugin($plg);
				$pli = $pl->get_info();

				$dbp = new Plugin();
				$dbp->name = $pli['name'];
				$dbp->url = $pli['url'];
				$dbp->slug = $pli['slug'];
				$dbp->version = $pli['version'];
				$dbp->author = $pli['author'];
				$dbp->author_url = $pli['author_url'];
				$dbp->description = $pli['description'];
				$dbp->active = false;
				$dbp->save();

				$pl->install();

			}
		}

		$this->templatemanager->assign('db_plugins', $db_plugins);
		$this->templatemanager->assign('fs_plugins', $fs_plugins);

		$this->templatemanager->show_template('plugins_list');
	}

	public function toggle($slug)
	{
		$pl = Plugin::factory()->get_by_slug($slug);

		if ($pl->exists())
		{
			$pl->active = !$pl->active;
			$pl->save();
		}

		redirect('administration/extend');
	}



	/* special function for all plugin pages */
	public function page()
	{
		$num = func_num_args();

		if ($num <= 0)
			redirect('administration/dashboard');
		else
		{
			$args = func_get_args();

			$plugin = array_shift($args);

			$page = PluginManager::do_actions('plugin.page', $args, array($plugin));

			$title = $page['title'];
			$heading = isset($page['heading']) ? $page['heading'] : $page['title'] ;

			$this->templatemanager->set_title($title);
			$this->templatemanager->assign('heading', $heading);
			$this->templatemanager->assign('tagline', $page['tagline']);
			$this->templatemanager->assign('buttons', $page['buttons']);
			$this->templatemanager->assign('html', $page['html']);

			$this->templatemanager->show_template('plugins_page');
		}
	}
}