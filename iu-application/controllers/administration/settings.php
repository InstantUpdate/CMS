<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Settings extends CS_Controller {

	public function __construct()
	{
		parent::__construct();


		$allowed = $this->loginmanager->is_logged_in();
		if (!$allowed)
			redirect($this->loginmanager->login_url);

		if (!$this->user->can('edit_settings'))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit website settings!", 'failure');
			redirect('administration/dashboard');
		}

	}

	public function show($saved = false)
	{
		$this->update_templates();

		$groups = Setting::factory()->distinct()->select('group')->where('group !=', 'hidden')->order_by('id')->get_iterated();


		$br = 0;

		foreach ($groups as $group)
		{
			$grps[$br]['name'] = $group->group;
			$grps[$br]['settings'] = Setting::factory()->where('group', $group->group)->order_by('id')->get_iterated();
			$br++;
		}

		$this->templatemanager->set_title(__("Edit site-wide settings"));
		$this->templatemanager->assign('groups', $grps);
		$this->templatemanager->show_template('settings');
	}

	public function index($saved = false)
	{
		$this->show($saved);
	}

	public function save()
	{
		foreach(array_keys($_POST) as $option){
			$setting = Setting::factory($option);
			if ($setting->exists())
			{
				$setting->set_value($this->input->post($option));
				$setting->save();
			}
		}

		$this->templatemanager->notify_next(__("Settings are saved successfully."), "success");

		redirect('administration/settings');
	} //*/

	private function update_templates()
	{
		return true;
		$tmplts = scandir('./iu-application/views/');
		array_shift($tmplts);    // remove '.' from array
		array_shift($tmplts);    // remove '..' from array
		$templates = $tmplts;

		for ($i=0; $i<count($tmplts);$i++)
		{
			if (!is_dir('./iu-application/views/'.$tmplts[$i]))
				unset($templates[$i]);

			if (in_array($tmplts[$i], array('administration', 'setup')))
				unset($templates[$i]);

		}

		//get template setting
		$lngsett = Setting::factory("default_template");
		//save with new options
		$lngsett->set_options($templates)->save();
	}

}

?>