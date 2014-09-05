<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Content extends DataMapper {

	public $has_one = array('page', 'contenttype');
	public $has_many = array('contentrevision',

		'editor'=>array(
			'class'=>'user'
			,'other_field'=>'assigned_contents'
			,'join_self_as'=>'content'
			,'join_other_as'=>'user'
			,'join_table'=>'contents_users'
		)

		,'repeatableitem', 'galleryitem'

	);

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new Content();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public function process($cs_iu4_nwd)
	{
		$cs_iu4_cwd = getcwd();

		if (empty($cs_iu4_cwd) || !$this->is_processable())
			return $this->contents;

		$cs_iu4_data = $this->contents;
		$cs_iu4_path = realpath($this->path);

		if (!chdir($cs_iu4_nwd))
			return $cs_iu4_data;

		while (ob_get_level())
			ob_end_flush();

		ob_start(); ob_start();
		eval('?'.'>'.preg_replace("/;*\s*\?>/", "; ?".">", str_replace('<'.'?=', '<'.'?php echo ', $cs_iu4_data)));

		$cs_iu4_output = '';
		while (ob_get_level())
			$cs_iu4_output .= ob_get_clean();

		chdir($cs_iu4_cwd);

		return $cs_iu4_output;
	}

	public function is_processable()
	{
		if (empty($this->contents))
			return false;

		return strpos($this->contents, '<'.'?') !== false;
	}

}


?>