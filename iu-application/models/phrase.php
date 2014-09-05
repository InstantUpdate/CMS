<?php

class Phrase extends Datamapper {

	public $has_one = array("language");

	public function __construct($id = NULL)
	{
		parent::__construct($id);
	}

	public static function factory()
	{
		return new Phrase();
	}

	public static function get_default($phrase, $filter)
	{
		$default = Phrase::factory()
			->where("phrase", $phrase)
			->where("filter", $filter)
			->where_related_language(Language::get_default())
			->get();

		if (!$default->exists())
		{
			$default->phrase = $default->translation = $phrase;
			$default->filter = $filter;
			$default->save(array(Language::get_default()));
		}
		return $default;
	}

}
?>