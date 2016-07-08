<?php defined('BASEPATH') OR exit('No direct script access allowed');

class ContentType extends DataMapper {

	public $has_many = array('content');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new ContentType();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}


}


?>