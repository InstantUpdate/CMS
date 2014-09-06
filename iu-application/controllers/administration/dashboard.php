<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		$this->templatemanager->set_title(__("Dashboard"));
	}

	public function index()
	{
		//get stats
		$last15 = Hit::factory()
			->fetch(time()-60*15)
			->unique()->cnt();

		$last15bymin = Hit::timeflow(time()-60*15, null, 15);

		$last4hrs = Hit::factory()
			->fetch(time()-3600*4)
			->unique()->cnt();

		$last4hrsbymin = Hit::timeflow(time()-3600*4, null, 16);


		$today = Hit::factory()
			->fetch(mktime(0, 0, 0))
			->unique()->cnt();

		$todaybymin = Hit::timeflow(mktime(0, 0, 0), null, 24);

		$yesterday = Hit::factory()
			->fetch(mktime(0, 0, 0)-(3600*24), mktime(0, 0, 0))
			->unique()->cnt();

		$yesterdaybymin = Hit::timeflow(mktime(0, 0, 0)-(3600*24), mktime(0, 0, 0), 24);


		$lastweek = Hit::factory()
			->fetch(mktime(0, 0, 0)-(3600*24*7))
			->unique()->cnt();

		$lastweekbymin = Hit::timeflow(mktime(0, 0, 0)-(3600*24*7), null, 14);

		$lastmonth = Hit::factory()
			->fetch(mktime(0, 0, 0)-(3600*24*30))
			->unique()->cnt();

		$lastmonthbymin = Hit::timeflow(mktime(0, 0, 0)-(3600*24*7), null, 30);

		$returning = Hit::factory()
			->fetch(time()-3600*24*30)
			->unique()
			->where('returning', true)
			->cnt();

		$new_pages = Page::factory()
			->where('created >=', time()-3600*24*30)
			->get()->result_count();

		$countries = Hit::factory()
			->fetch(time()-3600*24*30)
			->group_by('country')
			->cnt();

		$pagehits = Hit::factory()
			->select('*,count(page_id) as cnt')
			->include_related('page', null, TRUE, TRUE)
			->where('page_id >', 0)
			->fetch(time()-3600*24*7)
			->group_by('page_id')
			->limit(1)
			->get();


		$this->templatemanager->assign('pagehits', $pagehits);

		$ping = false;

		$pingset = Setting::factory('last_ping');
		$lastping = (int)$pingset->value;
		if (time()-$lastping > (3600*24*30))
			$ping = true;
		
		$pingset->value = time();
		$pingset->save();

		$this->templatemanager->assign('last15', $last15);
		$this->templatemanager->assign('last15bymin', $last15bymin);
		$this->templatemanager->assign('last4hrs', $last4hrs);
		$this->templatemanager->assign('last4hrsbymin', $last4hrsbymin);
		$this->templatemanager->assign('today', $today);
		$this->templatemanager->assign('todaybymin', $todaybymin);
		$this->templatemanager->assign('yesterday', $yesterday);
		$this->templatemanager->assign('yesterdaybymin', $yesterdaybymin);
		$this->templatemanager->assign('lastweek', $lastweek);
		$this->templatemanager->assign('lastweekbymin', $lastweekbymin);
		$this->templatemanager->assign('lastmonth', $lastmonth);
		$this->templatemanager->assign('lastmonthbymin', $lastmonthbymin);
		$this->templatemanager->assign('returning', $returning);
		$this->templatemanager->assign('new_pages', $new_pages);
		$this->templatemanager->assign('countries', $countries);
		$this->templatemanager->assign('ping', $ping);

		//latest repeatables
		$last_repeatables = RepeatableItem::factory()
			->order_by('timestamp DESC')->limit(5)->get();

		$this->templatemanager->assign('last_repeatables', $last_repeatables);

		//latest contents updated
		$last_contents = Content::factory()
			->where_related_contenttype('classname', 'Html')
			->order_by('updated DESC, created DESC')
			->limit(10)->get();

		$this->templatemanager->assign('last_contents', $last_contents);

		//count content updates (revisions)
		$revs = ContentRevision::factory()->count();
		$this->templatemanager->assign('revisions', $revs);


		//if geoip is old, notify
		$geoip_db_filename = './iu-resources/geoip/GeoIP.dat';

		if (is_file($geoip_db_filename))
		{
			$month_earlier = time() - (3600*24*30);
			$filemtime = filemtime($geoip_db_filename);

			if ($this->user->can('edit_settings') && ($filemtime <= $month_earlier))
			{
				$lnk = site_url('administration/maintenance');
				$this->templatemanager->notify(__("Your GeoIP database is now older than one month! Consider <a href='$lnk'>updating it</a>!"), 'information');
			}
		}

		//get latest users
		$users = User::factory()->order_by('created DESC')->limit(5)->get();
		$this->templatemanager->assign('users', $users);

		$this->templatemanager->show_template("dashboard");
	}

	public function news()
	{
		echo $this->http_get("http://my.cubescripts.com/support/rss/index.php?/News/Feed/Index/0");
	}

}

?>