<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Permission extends DataMapper {

	public $table = 'permissions';
	public $has_many = array('user', 'userrole');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new Permission();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

}


?>