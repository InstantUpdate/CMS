<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Galleries extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

	}
	
	public function index()
	{
		$reps = Content::factory()
			->where_related_contenttype('classname', 'Gallery')
			->order_by('updated DESC, created DESC')
			->get();

		$this->templatemanager->assign("galleries", $reps);

		$this->templatemanager->set_title("Manage Galleries");
		$this->templatemanager->show_template("galleries_list");
	}

	
}

?>