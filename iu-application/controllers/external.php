<?php defined('BASEPATH') OR exit('No direct script access allowed');

class External extends CI_Controller {

    public function __construct()
    {
        parent::__construct();

    	$this->load->database();
    	$this->load->library('datamapper');
    	$this->load->library('loginmanager');

    	//hack for datamapper prefix
    	DataMapper::$config['prefix'] = $this->db->dbprefix;

    	if ($this->loginmanager->is_logged_in())
    		$this->user = User::factory($this->loginmanager->user->id);
    }

    public function index()
    {
        //nothing here :)
    }

    public function in_admin()
    {
        return false;
    }

    public function recover_get_array($query_string = '')
    {
        $query_string = urldecode($query_string);

        $_SERVER['QUERY_STRING'] = $query_string;
        $get_array = array();
        parse_str($query_string,$get_array);
        foreach($get_array as $key => $val) {
            $_GET[$key] = $this->input->xss_clean($val);
            $_REQUEST[$key] = $this->input->xss_clean($val);
        }
    }
}
?>