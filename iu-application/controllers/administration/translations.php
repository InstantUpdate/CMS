<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Translations extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

	}

	private function update_languages_setting()
	{
		//get language setting
		$lngsett = Setting::factory("default_language");

		//get all languages
		$languages = Language::factory()->get();

		$lngsettarr = array();

		foreach($languages as $lng)
			$lngsettarr[] = $lng->name;

		$lngsett->set_options($lngsettarr)->save();
	}

	public function languages($saved = false)
	{
		//get all languages
		$languages = Language::factory()->get();

		$this->templatemanager->assign("languages", $languages);
		$this->templatemanager->show_template("translations_languages");
	}

	public function index($saved = false)
	{
		$this->languages($saved);
	}

	public function edit_language($id)
	{
		$id = (int)$id;
		$lng = Language::factory()->get_by_id($id);

		if (!$lng->exists())
			show_error("That language does not exist!");

		$this->templatemanager->assign('lang', $lng);
		$this->templatemanager->show_template('translations_edit_lang');

	}

	public function save_language($id)
	{
		$id = (int)$id;
		$lng = Language::factory()->get_by_id($id);

		if (!$lng->exists())
			show_error("That language does not exist!");

		$name = $this->input->post('name');
		$slug = $this->input->post('slug');
		$acti = $this->input->post('active');

		if (!empty($name))
			$lng->name = trim($name);

		if (!empty($slug))
			$lng->slug = trim($slug);


		$lng->active = ($acti !== false);

		$lng->save();

		redirect('administration/translations');
	}

	public function add_language()
	{
		$lng = trim($this->input->post('name'));
		$slg = $this->input->post('slug');
		if (empty($slg))
		{
			$slg = cyr_url_title($lng);
			$slg = substr($slg, 0, 2);
		}

		$slg = trim($slg);

		$lang = Language::factory($lng);
		$lang->name = $lng;
		$lang->slug = $slg;
		$lang->active = 1;
		$lang->save();

		//get all phrases for default language
		$phrases = Language::get_default()->phrase->get();

		//copy all phrases for language to new language and clear translations
		foreach ($phrases as $phrase)
		{
			$newphr = $phrase->get_copy();
			$newphr->translation = null;
			$newphr->save(array($lang));
		}

		$this->update_languages_setting();

		redirect("administration/translations");
	} //*/

	public function phrases($lng, $all=false)
	{
		$lng = (int)$lng;
		$lang = Language::factory()->get_by_id($lng);

		$groups = array();
		$filters = Phrase::factory()->distinct()->select('filter')->get();

		$i = 0;
		foreach ($filters as $filter)
		{
			$groups[$i]['name'] = $filter->filter;
			$groups[$i]['phrases'] = Phrase::factory()->where('filter', $filter->filter)
										->where_related_language($lang)->order_by('translation')->get();
			$i++;
		}

		$this->templatemanager->assign("groups", $groups);
		//$this->templatemanager->assign("total", count($phrases->all));
		$this->templatemanager->assign("language", $lang);
		$this->templatemanager->assign("filters", $filters);
		$this->templatemanager->show_template("translations_phrases");
	}

	public function save()
	{
		$phrases = $this->input->post("phrase");
		$translations = $this->input->post("translation");
		$lng = (int)$this->input->post("language");
		$lang = Language::factory()->get_by_id($lng);

		for ($i = 0; $i < count($phrases); $i++)
		{
			$pid = $phrases[$i]; $pid = (int)$pid;
			$translation = $translations[$i];
			if (!empty($translation))
			{
				$phrase = new Phrase($pid);
				$phrase->translation = $translations[$i];
				$phrase->save($lang);
			}

		}

		$this->update_languages_setting();

		redirect("administration/translations/phrases/".$lang->id);
	}

	public function remove($id)
	{
		$lang = Language::factory()->get_by_id((int)$id);
		$lang->delete();

		$this->update_languages_setting();

		redirect("administration/translations");
	}

}

?>