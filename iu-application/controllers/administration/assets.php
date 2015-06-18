<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assets extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);


		//if can edit everything
		//if ($this->user->can('edit_templates'))
		//	redirect('administration/templates');

	}

	public function index()
	{
		$this->templatemanager->set_title("Manage Assets");

		$mini = $this->input->get('mini', true);
		if ($mini)
		{
			$this->templatemanager->set_header(false);
			$this->templatemanager->set_sidebar(false);
			$this->templatemanager->set_footer(false);
		}


		$this->templatemanager->show_template("asset_manager");
	}

}

?>