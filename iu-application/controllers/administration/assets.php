<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Assets extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);


		//if can edit everything
		//if ($this->user->can('edit_templates'))
		//	redirect('administration/templates');

	}

	public function index()
	{
		$this->templatemanager->set_title("Manage Assets");

		$mini = $this->input->get('mini', true);
		if ($mini)
		{
			$this->templatemanager->set_header(false);
			$this->templatemanager->set_sidebar(false);
			$this->templatemanager->set_footer(false);
		}


		$this->templatemanager->show_template("asset_manager");
	}


	public function quick_upload()
	{
		$assetdir = Setting::value('assets_folder', 'iu-assets').'/'.$this->user->id;
		@mkdir($assetdir, 0777, true);

		$assetpath = realpath(trim($assetdir, '/')). '/';
		$assetbase =  rel2abs($assetdir, trim(base_url(), '/').'/');

		// Optional: instance name (might be used to adjust the server folders for example)
		$CKEditor = $_GET['CKEditor'] ;
		// Required: Function number as indicated by CKEditor.
		$funcNum = $_GET['CKEditorFuncNum'] ;
		// Optional: To provide localized messages
		$langCode = $_GET['langCode'] ;

		// The returned url of the uploaded file
		$url = '' ;
		// Optional message to show to the user (file renamed, invalid file, not authenticated...)
		$message = '';
		// in CKEditor the file is sent as 'upload'
		if (isset($_FILES['upload']))
		{
			$config = array();
			$config['upload_path'] = $assetdir;
			$config['allowed_types'] = '*';

			$this->load->library('upload', $config);

			if (!$this->upload->do_upload('upload'))
			{
				$message = $this->upload->display_errors();
			}
			else
			{
				$data = $this->upload->data();

				if ((stripos(current_url(), 'instant-update.com')) && empty($data['is_image']))
					die(json_encode($result));

				$url = $assetbase . '/' . $data['file_name'] ;
				//$result['url'] = base_url() . 'iu-assets/'.$this->user->id.'/'.$data['file_name'];

				$message = 'The file has been uploaded';
			}

		}
		else
		{
			$message = 'No file has been sent';
		}
		// ------------------------
		// Write output
		// ------------------------
		// We are in an iframe, so we must talk to the object in window.parent
		echo "<script type='text/javascript'> window.parent.CKEDITOR.tools.callFunction({$funcNum}, '{$url}', '{$message}')</script>";

	}

}

?>