<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		$allowed = $this->loginmanager->is_logged_in();
		if (!$allowed)
			redirect($this->loginmanager->login_url);

		if (!$this->user->can('edit_settings'))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to access maintenance page!", 'failure');
			redirect('administration/dashboard');
		}
	}

	public function index()
	{
		$this->templatemanager->set_title("Website Maintenance");
		$this->templatemanager->show_template("maintenance");
	}

	public function exportsql($zipped=false)
	{

        $this->load->dbutil();

		$sql = $this->dbutil->backup(array('format'=>'txt'));

		if (!empty($zipped))
		{
			$this->load->library('zip');
			$this->zip->add_data(Setting::value("website_title", CS_PRODUCT_NAME).'.sql', $sql);
			$this->zip->download(Setting::value("website_title", CS_PRODUCT_NAME).'.sql.zip');
		}
		else
		{
			$this->load->helper('download');
			force_download(Setting::value("website_title", CS_PRODUCT_NAME).'.sql', $sql);
		}
	}

	public function getgeoip()
	{
		$remote = 'http://geolite.maxmind.com/download/geoip/database/GeoLiteCountry/GeoIP.dat.gz';
		$fname = 'iu-resources/geoip/GeoIP.dat';

		if (is_file($fname))
		{
			$month_earlier = time() - (3600*24*10);
			$filemtime = filemtime($fname);

			if ($filemtime >= $month_earlier)
			{
				$this->templatemanager->notify_next("Your GeoIP database was updated recently, so there's no need to do it again. You can try again in a couple of days, though.", 'information');
				redirect('administration/maintenance');
			}
		}

		$writable = false;
		if (is_file($fname))
			$writable = is_really_writable($fname);
		else
			$writable = is_really_writable(dirname($fname));

		if (!$writable)
		{
			$this->templatemanager->notify_next("The file <strong>$fname</strong> or folder <strong>".dirname($fname)."</strong> is not writable! Either make it writable or manually <a href='$remote'>download GeoIP database</a> manually...", 'failure');
			redirect('administration/maintenance');
		}
		else
		{
			$dbgz = $this->http_get($remote);

			$tmp_name = 'iu-resources/geoip/GeoIP.dat.gz';
			$tmp_written = file_put_contents($tmp_name, $dbgz);

			if (empty($dbgz) or ($tmp_written === false))
			{
				$this->templatemanager->notify_next("Could not download GeoIP database! Try again tomorrow or <a href='$remote'>download GeoIP database</a> manually.", 'failure');
				redirect('administration/maintenance');
			}

			$raw_written = decompress_gzfile($tmp_name, $fname);

			if (!$raw_written)
			{
				$this->templatemanager->notify_next("Could not (g)unzip to <strong>$fname</strong>! <a href='$remote'>Download GeoIP database</a> manually, please.", 'failure');
				redirect('administration/maintenance');
			}
			else
			{
				$this->templatemanager->notify_next("GeoIP database updated successfully!", 'success');
				//file_put_contents($fname, $raw);
				@touch($fname);
				redirect('administration/maintenance');
			}
		}
	}

	public function prunerevs($months = 0)
	{
		error_reporting(E_ALL);
		if (empty($months))
			$months = (int)$this->input->post('age');

		$seconds = 3600*24*30*$months;
		$ago = time() - $seconds;

		$contents = ContentRevision::factory()
			->where('created <', $ago)
			->get();

		$contentsnum = $contents->result_count();

		$contents->delete_all();

		$files = FileRevision::factory()
			->where('created <', $ago)
			->get();

		$filesnum = $files->result_count();

		$files->delete_all();

		if ($filesnum > 0 || $contentsnum > 0)
			$this->templatemanager->notify_next("$filesnum file revision(s) and $contentsnum content revision(s) were removed successfully!", 'success');
		else
			$this->templatemanager->notify_next("Nothing to remove older than $months month(s)!", 'information');

		redirect('administration/maintenance');
	}

}

?>