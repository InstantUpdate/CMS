<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ContentRevision extends DataMapper {

	public $has_one = array('content', 'user');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new ContentRevision();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

}


?>