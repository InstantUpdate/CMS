<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Repeatables extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

	}

	public function ajax_save()
	{
		$id = (int)$this->input->post('id');
		$pid = (int)$this->input->post('page_id');
		$div = $this->input->post('div');
		$title = $this->input->post('title');
		$text = $this->input->post('text');
		$img = $this->input->post('image');

		//file_put_contents('post', json_encode($_POST));die;

		$page = Page::factory($pid);

		$content = Content::factory()
			->where('div', $div)
			->where_related_page('id', $pid)
			->limit(1)
			->get();

		if (!$content->exists())
		{
			$content = new Content();
			$content->div = $div;

			$ctype = ContentType::factory()
				->where('classname', 'Repeatable')
				->limit(1)
				->get();

			$content->editor_id = $this->user->id;
			$content->save(array($page, $ctype));
		}
		else
		{
			$content->editor_id = $this->user->id;
			$content->save();
		}

		if (empty($id))
		{
			$item = new RepeatableItem();
			$item->timestamp = time();
		}
		else
			$item = RepeatableItem::factory($id);

		$item->title = $title;
		$item->text = $text;
		$item->image = trim($img, '/');

		if (empty($id))
			$item->save(array($content, $this->user));
		else
			$item->save();


		if (empty($id))
			$msg = __("New item published: \"%s\"", $title);
		else
			$msg = __("Changes saved in \"%s\"", $title);

		echo json_encode(array('status'=>'OK', 'message'=>$msg,'id'=>$item->id));
	}

	public function ajax_remove()
	{
		$id = (int)$this->input->post('id');
		$title = $this->input->post('title');

		$item = RepeatableItem::factory($id);
		if (!empty($id))
			$title = $item->title;

		$msg = __("Item \"%s\" removed!", $title);

		$item->remove();

		echo json_encode(array('status'=>'OK', 'message'=>$msg));
	}

	public function ajax_removeimage($rid)
	{
		$r = RepeatableItem::factory((int)$rid);

		if ($r->exists() && (!empty($r->image)))
		{


			if (trim($r->image) != 'iu-resources/images/no-image.png')
			{
				$img = new Image($r->image);
				$img->remove_thumbnails();
				@unlink($r->image);
			}

			$r->image = null;
			$r->save();
			$msg = 'Image is deleted successfully';
		}
		else
			$msg = 'Repeatable item doesn\'t exist!';

		echo json_encode(array('status'=>'OK', 'message'=>$msg));

	}

	public function index()
	{
		$reps = Content::factory()
			->where_related_contenttype('classname', 'Repeatable')
			->order_by('updated DESC, created DESC')
			->get();

		$this->templatemanager->assign("repeatables", $reps);

		$this->templatemanager->set_title("Manage News/Blog");
		$this->templatemanager->show_template("repeatables_list");
	}

	public function add($cid)
	{

		$content = Content::factory((int)$cid);

		if (!$this->user->can_edit_content($content))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit this content!", 'failure');
			redirect('administration/dashboard');
		}

		$content->page->get();
		$users = User::factory()->get();

		$this->templatemanager->assign('content', $content);
		$this->templatemanager->assign('users', $users);

		$this->templatemanager->set_title("Edit News/Blog");
		$this->templatemanager->show_template('repeatableitem_edit');
	}

	public function edit($id)
	{
		$item = RepeatableItem::factory((int)$id);

		$item->user->get();
		$item->content->get();
		$item->content->page->get();

		if (!$this->user->can_edit_content($item->content))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit this item's content!", 'failure');
			redirect('administration/dashboard');
		}

		if ($item->timestamp > time())
			$this->templatemanager->notify("This item is scheduled to be published in future!", 'information');

		$users = User::factory()->get();

		$this->templatemanager->assign('item', $item);
		$this->templatemanager->assign('users', $users);

		$this->templatemanager->set_title("Edit News/Blog");
		$this->templatemanager->show_template('repeatableitem_edit');
	}

	public function save($id = null)
	{
		if (empty($id))
			$item = new RepeatableItem();
		else
			$item = RepeatableItem::factory((int)$id);

		if ($item->exists() && !$this->user->can_edit_content($item->content->get()))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit this item's content!", 'failure');
			redirect('administration/dashboard');
		}

		$item->title = $this->input->post('title');
		$item->text = $this->input->post('text');

		$uid = (int)$this->input->post('user_id');

		if ($this->user->is_admin())
			$item->user_id = empty($uid) ? $this->user->id : $uid;
		else
			$item->user_id = empty($id) ? $this->user->id : $item->user_id;


		$date = $this->input->post('date');
		$time = $this->input->post('time');
		$cid = (int)$this->input->post('cid');

		$dateparts = parse_datepicker($date);
		$timeparts = explode(':', $time);

		$ts = mktime($timeparts[0], $timeparts[1], 0, $dateparts['m'], $dateparts['d'], $dateparts['y']);
		$item->timestamp = $ts;

		//prepare for upload
		$config['upload_path'] = $this->user->assets_path();
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']	= '512';
		$config['max_width']  = '1024';
		$config['max_height']  = '1024';
		$config['encrypt_name'] = true;


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
				$item->image = $config['upload_path'].$data['file_name'];
			}
		}


		if ($item->exists())
			$item->save();
		else
		{
			$content = Content::factory($cid);
			$item->save(array($content));
		}


		redirect('administration/repeatables/edit/'.$item->id);
	}

	public function remove($id)
	{
		$item = RepeatableItem::factory((int)$id);
		$title = $item->title;
		$content = $item->content->get();

		/*if ($item->exists() && (!empty($item->image)) && (trim($item->image) != 'iu-resources/images/no-image.png'))
		{
			$img = new Image($item->image);
			$img->remove_thumbnails();
			@unlink($item->image);
		}//*/

		$item->remove();

		redirect('administration/contents/edit/'.$content->id.'/'.$content->div);
	}

}

?>