<?php defined('BASEPATH') OR exit('No direct script access allowed');

class FileRevision extends DataMapper {

	public $has_one = array('file', 'user');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new FileRevision();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

}


?>