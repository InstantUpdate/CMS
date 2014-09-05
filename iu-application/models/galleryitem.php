<?php defined('BASEPATH') OR exit('No direct script access allowed');

class GalleryItem extends DataMapper {

	public $table = 'galleryitems';

	public $has_one = array('user', 'content');
	public $has_many = array();

	public $default_order_by = array('order' => 'asc', 'id' => 'desc');

    function __construct($id = NULL)
	{
		parent::__construct($id);
    }

	public static function factory($id = null)
	{
		$instance = new GalleryItem();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}
}

/* End of file template.php */
/* Location: ./application/models/template.php */
