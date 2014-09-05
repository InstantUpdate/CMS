<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Translate {

	private $language;
	private $phrases;

	public function __construct()
	{
		$this->set_language(Language::$default);
	}

	public function set_language($lang_name)
	{
		$this->language = Language::factory($lang_name);
		$phrases = Phrase::factory()->where_related_language($this->language)->get();
		foreach ($phrases as $phrase)
		{
			$this->phrases[$phrase->phrase]['translation'] = $phrase->translation;
			$this->phrases[$phrase->phrase]['filter'] = $phrase->filter;
		}

		//var_dump($this->phrases);
		//die;
	}

	public function get_language()
	{
		return $this->language;
	}

	public function get_translation($phrase, $filter)
	{
		if (isset($this->phrases[$phrase]) && $this->phrases[$phrase]['filter'] == $filter && !empty($this->phrases[$phrase]['translation']))
			return $this->phrases[$phrase]['translation'];
		else
			return false;
	}

	public function phrase($phrase, $filter)
	{
		$tr = $this->get_translation($phrase, $filter);
		//var_dump($tr);
		//die;
		if ($tr)
		{
			return $tr;
		}
		else
		{
			//save for curr. language
			if ($this->language->id != Language::get_default()->id)
			{
				$newphr = Phrase::factory()->where('phrase', $phrase)->where_related_language($this->language)->limit(1)->get();
				$newphr->phrase = $phrase;
				$newphr->filter = $filter;
				$newphr->save($this->language);
			}

			//return default
			return Phrase::get_default($phrase, $filter)->translation;
		}


	/*	$phrase_obj = Phrase::factory()
			->where("phrase", $phrase)
			->where("filter", $filter)
			->where_related_language($this->language)
			->get();

		if ($phrase_obj->exists())
		{
			if (trim($phrase_obj->translation) != "")
				return $phrase_obj->translation;
			else
				return Phrase::get_default($phrase, $filter)->translation;
		}
		else
		{
			//save for curr. language
			if ($this->language->id != Language::get_default()->id)
			{
				$newphr = new Phrase();
				$newphr->phrase = $phrase;
				$newphr->filter = $filter;
				$newphr->save($this->language);
			}

			//return default
			return Phrase::get_default($phrase, $filter)->translation;
		}//*/

	}
}

?>