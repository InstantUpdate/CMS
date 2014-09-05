<?php

class JS extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		while(ob_get_level())
			ob_end_clean();


	}

	public function init()
	{
		header("Content-Type: application/x-javascript");

		echo "var IU_SITE_URL = '".str_replace("'", "\\'", rtrim(site_url(), '/'))."';\n";
		echo "var IU_BASE_URL = '".str_replace("'", "\\'", base_url())."';\n";
		echo "var IU_GLOBALS = {}; //throw anything in here\n";

		$pages = Page::factory()->get();

		$pages_arr = array();

		foreach ($pages as $p)
		{
			$page = new stdClass();;
			$page->uri = $p->uri;
			$page->label = $p->title . ' ' . $p->uri;
			$page->url = site_url($p->uri);
			$page->title = character_limiter($p->title, 50);
			$page->id = $p->id;

			$pages_arr[] = $page;
		}

		echo "var IU_PAGES = " . json_encode($pages_arr) . ";\n";

		$settings = Setting::factory()->where('group !=', 'hidden')
			->where('group !=', 'branding')->get();

		$setts = array();

		foreach ($settings as $s)
			$setts[$s->name] = $s->value;

		echo "var IU_SETTINGS = " . json_encode($setts) . ";\n";

		if (!empty($this->user))
		{
			$juser = $this->user->stored;
			unset($juser->salt);
			unset($juser->password);
			unset($juser->key);
			unset($juser->active);
			echo "var IU_USER = " . json_encode($juser) . ";\n";

			$perms = Permission::factory()->where_related_user('id', $this->user->id)->get();

			$permarr = array();

			foreach ($perms as $p)
				$permarr[] = $p->key;

			echo "var IU_USER_PERMISSIONS = " . json_encode($permarr) . ";\n";
		}



	}


} // class

?>