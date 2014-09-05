<?php

class Language extends Datamapper {

	public static $default = "English";

	public $has_many = array("phrase");

	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	public static function factory($name=null)
	{
		$instance = new Language();
		if (!empty($name))
			$instance->where('name', $name)->limit(1)->get();
		return $instance;
	}

	public static function get_default()
	{
		$eng = Language::factory(self::$default);
		if (!$eng->exists())
		{
			$eng->name = self::$default;
			$eng->save();
		}

		return $eng;
	}

	public function __toString()
	{
		return $this->name;
	}

}
?>