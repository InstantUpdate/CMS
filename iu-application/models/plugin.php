<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Plugin extends DataMapper {

	public $has_many = array('content');
	public $table = 'plugins';

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

	public function factory($id = NULL)
	{
		$obj = new Plugin();
		if (!empty($id))
			$obj->get_by_id($id);

		return $obj;
	}

	public function activate()
	{
		$this->active = true;
		$this->save();

		return $this->active;
	}

	public function deactivate()
	{
		$this->active = false;
		$this->save();

		return !$this->active;
	}
}


?>