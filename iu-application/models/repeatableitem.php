<?php defined('BASEPATH') OR exit('No direct script access allowed');

class RepeatableItem extends DataMapper {

	public $table = 'repeatableitems';

	public $has_one = array('user', 'content');
	public $has_many = array();

    function __construct($id = NULL)
	{
		parent::__construct($id);
    }

	public static function factory($id = null)
	{
		$instance = new RepeatableItem();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public function remove()
	{
		if (!empty($this->image) && (trim($this->image) != 'iu-resources/images/no-image.png'))
		{
			$img = new Image($this->image);
			$img->remove_thumbnails();
			@unlink($img->path);
		}

		$this->delete();
	}

}


/* End of file template.php */
/* Location: ./application/models/template.php */
