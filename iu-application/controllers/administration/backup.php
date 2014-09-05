<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Backup extends CS_Controller {

    public function __construct()
    {
        parent::__construct();

    	//require login
    	if (!$this->loginmanager->is_logged_in())
    		redirect($this->loginmanager->login_url);

        $this->load->library('zip');
        $this->load->dbutil();
    }

    public function index()
    {
        $this->templatemanager->set_title(__("Backup Data"));
        $this->templatemanager->show_template("backup");
    }

    public function export()
    {
        $export_data = $this->input->post("export_data");
        $export_images = $this->input->post("export_images");
        $export_assets = $this->input->post("export_assets");

        if ($export_data)
        {
            $sql = $this->dbutil->backup(array('format'=>'txt'));
            $this->zip->add_data('database.sql', $sql);
        }

        if ($export_images)
        {
            $path = str_replace("\\", "/", FCPATH . "images\\");
            $this->zip->read_dir($path, false);
        }

        if ($export_assets)
        {
            $path = str_replace("\\", "/", FCPATH . "assets\\");
            $this->zip->read_dir($path, false);
        }

        $this->zip->download(cyr_url_title(Setting::value("website_title", CS_PRODUCT_NAME)).".zip");
    }
}