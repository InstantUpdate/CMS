<?php

class Uploadimage extends CS_Controller {

	public function __construct()
	{
		parent::__construct();
		if (empty($this->user))
			die('Not allowed!');
	}

	public function index()
	{
		$result = array();
		$result['status_code'] = 403;
		$result['message'] = "Invalid file namez";

		if (!$this->is_ajax_request())
			header('Content-type: text/plain');


		if (!empty($_FILES))
		{
			$config = array();
			$config['upload_path'] = './iu-assets/'.$this->user->id.'/';
			$config['allowed_types'] = 'gif|jpg|png';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('file'))
			{
				$result['message'] = $this->upload->display_errors();
			}
			else
			{
				$data = $this->upload->data();

				if (empty($data['is_image']))
					die(json_encode($result));

				$content_width = $_POST['max_width'];
				$img_width = $data['image_width'];

				if ($img_width > $content_width)
				{
					$img = image_create_from_file('iu-assets/'.$this->user->id.'/'.$data['file_name']);
					$img = image_resize($img, $content_width);
					image_to_file($img, 'iu-assets/'.$this->user->id.'/'.$content_width.'_'.$data['file_name']);
					$result['url'] = base_url() . 'iu-assets/'.$this->user->id.'/'.$content_width.'_'.$data['file_name'];
				}
				else
					$result['url'] = base_url() . 'iu-assets/'.$this->user->id.'/'.$data['file_name'];


				$result['message'] = 'Success';
				$result['status_code'] = 200;
			}

		}


		die(json_encode($result));
	}

}

?>