<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Images extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		/*$allowed = $this->loginmanager->is_administrator();
		if (!$allowed)
		{
			$login_link = anchor($this->loginmanager->login_url, "Log in");
			$this->loginmanager->showError("Only administrators are allowed in here, not " . $this->loginmanager->user->role ."s");
		}*/
	}

	public function replace()
	{

		$this->templatemanager->set_title("Replace image");
		$this->templatemanager->show_template("image_replace");
	}

	public function new_content($cname, $pid)
	{
		$page = Page::factory((int)$pid);
		$ctype = ContentType::factory()->where('classname', "Gallery")->limit(1)->get();

		$content = Content::factory();
		$content->div = $cname;
		$content->editor_id = $this->user->id;
		$content->page_id = $page->id;
		$content->contenttype_id = $ctype->id;

		$content->save();

		$redir = site_url('administration/images/gallery_add_new/'.$content->id.'/'.$cname.'/'.$pid) . '?iu-popup';

		redirect($redir);
	}

	public function gallery_add_new($id, $cname, $pid)
	{
		$id = (int)$id;
		$pid = (int)$pid;

		$content = Content::factory()->get_by_id($id);


		$this->templatemanager->set_title("Add images");
		$this->templatemanager->assign("content", $content);
		$this->templatemanager->show_template("gallery_images_upload");
	}

	public function upload()
	{

		if (empty($_FILES['image']['name']))
			show_error('Please upload a file!');

		//prepare for upload
		$config['upload_path'] = $this->user->assets_path();
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']	= '4096';
		$config['max_width']  = '4096';
		$config['max_height']  = '4096';
		$config['encrypt_name'] = false;
		$config['file_name'] = $_FILES['image']['name'];

		$this->load->library('upload', $config);

		//upload picture
		if (!empty($_FILES['image']['name']))
		{
			if ( !$this->upload->do_upload('image'))
			{
				show_error($this->upload->display_errors());
			}
			else
			{
				$data = $this->upload->data();

				$this->templatemanager->assign('image', $config['upload_path'].$data['file_name']);
				$this->templatemanager->load_template('image_replace_uploaded');
			}
		}

	}

	public function make_thumb($width, $height = 0)
	{
		$img = $this->input->post('image');
		//die($img);

		if ($img == false)
			die('FALSE');

		$im = new Image($img);
		echo $im->thumbnail($width, $height)->url;
	}

	private function do_save_from_pixlr($path = null)
	{
		$remote_img = $_GET['image'];
		$title = $_GET['title'];

		if (empty($path))
			$path = $this->user->assets_path();

		if (empty($path))
			show_error("You can't save images on this website!");

		if (!is_dir($path))
			@mkdir($path, 0777, true);

		$fname = $check = cyr_url_title($title);

		$i = 1;
		while (is_file(FCPATH . $path . $check . '.jpg'))
		{
			$check = $fname . $i;
			$i++;
		}

		$fname = $check . '.jpg';

		//create file
		$f = new File();
		$f->path = ltrim($path . $fname, '/');
		$f->title = $title;

		$code = $this->http_get($remote_img);
		$ok = file_put_contents(FCPATH . $path . $fname, $code);

		$f->editor_id = $this->user->id;

		//if not written to a file, write to db
		if ($ok === FALSE)
		{
			$f->data = base64_encode($code);
			$f->checksum = md5($code);
			$f->save();
		}

		$ret = array();
		$ret['url'] = site_url($path.$fname);
		$ret['path'] = ltrim($path.$fname, '/');
		$ret['title'] = $title;

		return $ret;

	}

	public function save()
	{
		$image = $this->do_save_from_pixlr();
		$this->templatemanager->assign('image', $image);
		$this->templatemanager->load_template('image_saved_from_pixlr');
	}

	public function save_reload()
	{
		$image = $this->do_save_from_pixlr();
		$this->templatemanager->assign('image', $image);
		$this->templatemanager->load_template('image_saved_from_pixlr_reload_admin');
	}
}

/* End of file admin.php */
/* Location: ./application/controllers/admin.php */


?>