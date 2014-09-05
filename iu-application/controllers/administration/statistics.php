<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Statistics extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		$this->templatemanager->set_title(__("Statistics"));
	}

	public function index()
	{
		$this->days(30);
	}

	public function days($days=30)
	{
		//hits
		$hits = Hit::factory()->
			fetch(time()-3600*24*$days)
			->cnt();

		$hitsflow = Hit::timeflow(time()-3600*24*$days, null, 30, false);

		$unique = Hit::factory()->
			fetch(time()-3600*24*$days)
			->unique()
			->cnt();

		$uniqueflow = Hit::timeflow(time()-3600*24*$days, null, 30, true);

		$this->templatemanager->assign('hits', $hits);
		$this->templatemanager->assign('hitsflow', $hitsflow);
		$this->templatemanager->assign('unique', $unique);
		$this->templatemanager->assign('uniqueflow', $uniqueflow);

		$this->templatemanager->assign('days', $days);

		//pages

		$pagehits = Hit::factory()
			->select('*')
			->select_func('COUNT', '@id', 'cnt')
			->include_related('page', null, TRUE, TRUE)
			->where('page_id >', 0)
			->fetch(time()-3600*24*$days)
			->unique('page_id')
			->limit(100)
			->get();

		for ($i=0; $i<count($pagehits->all);$i++)
		{
			$pagehits->all[$i]->timeflow = Hit::timeflow(time()-3600*24*$days, null, 30, false, $pagehits->all[$i]->page_id);
		}

		$this->templatemanager->assign('pagehits', $pagehits);

		//sasa
		$returning = Hit::factory()->
			fetch(time()-3600*24*$days)
			->unique()
			->where('returning', true)
			->cnt();

		$returningflow = Hit::timeflow(time()-3600*24*$days, null, 30, true, null, true);

		$this->templatemanager->assign('returning', $returning);
		$this->templatemanager->assign('returningflow', $returningflow);

		//contents edited
		$cnt_edits = ContentRevision::factory()
			->where('created >=', time()-3600*24*$days)
			->get()->result_count();

		$new_users = User::factory()
			->where('created >=', time()-3600*24*$days)
			->get();

		$new_users = $new_users->result_count();

		$new_pages = Page::factory()
			->where('created >=', time()-3600*24*$days)
			->get()->result_count();

		$repeats = RepeatableItem::factory()
			->where('timestamp >=', time()-3600*24*$days)
			->get()->result_count();

		$this->templatemanager->assign('cnt_edits', $cnt_edits);
		$this->templatemanager->assign('new_users', $new_users);
		$this->templatemanager->assign('new_pages', $new_pages);
		$this->templatemanager->assign('repeatables', $repeats);


		//get stats for browsers
		$browsers = Hit::factory()
			->select('browser')
			->select_func('COUNT', '@id', 'cnt')
			->fetch(time()-3600*24*$days)
			->unique('browser')
			->order_by('cnt DESC')
			->get();

		$browsersarr = array();

		$browsers->iu_total = 0;
		foreach ($browsers as $bro)
			$browsers->iu_total += $bro->cnt;

		$limit = ($browsers->result_count() > 10) ? 10 : $browsers->result_count();

		for ($i=0; $i<$limit;$i++)
		{
			$br = $browsers->all[$i];
			$obj = new stdClass();
			$obj->label = str_replace("'", "\'", $br->browser) . ' (' . percent($br->cnt, $browsers->iu_total) . '%)';
			$obj->data = (int)$br->cnt;
			$browsersarr[] = $obj;
		}

		//usort($browsersarr, array($this, 'compare_series'));

		$this->templatemanager->assign('browsers', $browsers);
		$this->templatemanager->assign('browsers_series', $browsersarr);

		//get stats for operating systems
		$oses = Hit::factory()
			->select('os')
			->select_func('COUNT', '@id', 'cnt')
			->fetch(time()-3600*24*$days)
			->unique('os')
			->order_by('cnt DESC')
			->get();

		$osarr = array();

		$oses->iu_total = 0;
		foreach ($oses as $osi)
			$oses->iu_total += $osi->cnt;

		$limit = ($oses->result_count() > 10) ? 10 : $oses->result_count();

		for ($i=0; $i<$limit;$i++)
		{
			$os = $oses->all[$i];
			$obj = new stdClass();
			$obj->label = str_replace("'", "\'", $os->os) . ' (' . percent($os->cnt, $oses->iu_total) . '%)';
			$obj->data = (int)$os->cnt;
			$osarr[] = $obj;
		}

		//usort($osarr, array($this, 'compare_series'));

		$this->templatemanager->assign('oses', $oses);
		$this->templatemanager->assign('oses_series', $osarr);

		//get stats for countries
		$geoip_db_filename = './iu-resources/geoip/GeoIP.dat';

		if (is_file($geoip_db_filename))
		{
			$countries = Hit::factory()
				->select('country')
				->select_func('COUNT', '@id', 'cnt')
				->fetch(time()-3600*24*$days)
				->unique('country')
				->order_by('cnt DESC')
				->get();

			$conarr = array();

			$countries->iu_total = 0;
			foreach ($countries as $cou)
				$countries->iu_total += $cou->cnt;

			//var_dump(percent(20,100));

			$limit = ($countries->result_count() > 10) ? 10 : $countries->result_count();

			for ($i=0; $i<$limit;$i++)
			{
				$c = $countries->all[$i];
				$obj = new stdClass();
				$obj->label = str_replace("'", "\'", empty($c->country) ? __("(unknown)") : $c->country) . ' (' . percent($c->cnt, $countries->iu_total) . '%)';
				$obj->data = (int)$c->cnt;
				$conarr[] = $obj;
			}

			//usort($conarr, array($this, 'compare_series'));

			$this->templatemanager->assign('countries', $countries);
			$this->templatemanager->assign('countries_series', $conarr);
		}


		$this->templatemanager->show_template('statistics');

	}

	public function compare_series($s1, $s2)
	{
		if ($s1 == $s2)
			return 0;

		return ($s1->data > $s2->data) ? -1 : 1;
	}

}

?>