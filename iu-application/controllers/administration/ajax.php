<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Ajax extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

	}

	public function permissions_for($id)
	{
		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		$role = UserRole::factory((int)$id);

		$arr = array();

		$perms = $role->permission->get();

		foreach ($perms as $p)
			$arr[] = $p->key;

		echo json_encode($arr);
	}

	public function gallery_upload($cid, $uid)
	{
		//if (!$this->loginmanager->is_logged_in())
			//show_error('You are not logged in!', 403);

		$content = Content::factory((int)$cid);
		$u = User::factory((int)$uid);

		/*if (!$u->can_edit_content($content))
		{
			show_error('You can\'t upload images to this gallery!', 403);
		}
		else//*/
		{
			$config['upload_path'] = "iu-assets/galleries/";
			$config['allowed_types'] = 'png|jpg|jpeg|jpe|gif';
			$this->load->library('upload', $config);

			//file_put_contents('uploadify', json_encode($_FILES));

			if ( ! $this->upload->do_upload("Filedata"))
			{
				$error = $this->upload->display_errors();
				// do stuff
				show_error('Couldn\'t upload!', 500);
			}
			else
			{
				$data = $this->upload->data();

				$gi = new GalleryItem();

				$gi->image = $config['upload_path'] . $data['file_name'];
				$gi->order = 0;
				$gi->save(array($content, $content->page->get()->user->get()));

				$content->updated = time();
				$content->save();
			}

			echo 'OK!';
		}


	}

	public function gallery_save_order()
	{
		if (!$this->loginmanager->is_logged_in())
			die(json_encode(array('message'=>'You are not logged in!', 'status'=>'Error!')));

		$im = GalleryItem::factory((int)$id);
		$content = $im->content->get();

		if (!$this->user->can_edit_content($content))
			die(json_encode(array('message'=>'You are not allowed to manage images in this gallery!', 'status'=>'Error!')));

		$imgsS = $this->input->post('order');
		$imgs = explode(',', $imgsS);

		foreach ($imgs as $order=>$img)
		{
			$id = end(explode('_', $img));

			$im = GalleryItem::factory((int)$id);
			$im->order = $order+1;
			$im->save();
		}

		die(json_encode(array('status'=>'OK')));
	}

	public function gallery_remove_image($id)
	{
		if (!$this->loginmanager->is_logged_in())
			die(json_encode(array('message'=>'You are not logged in!', 'status'=>'Error!')));

		$im = GalleryItem::factory((int)$id);
		$content = $im->content->get();

		if (!$this->user->can_edit_content($content))
			die(json_encode(array('message'=>'You are not allowed to manage images in this gallery!', 'status'=>'Error!')));

		$img = Image::factory($im->filename);
		$img->remove_thumbnails();
		@unlink($im->filename);
		$im->delete();

		$content->updated = time();
		$content->save();

		die(json_encode(array('message'=>'Image is successfully removed.','status'=>'OK')));
	}

	public function gallery_save_image_data($id)
	{
		if (!$this->loginmanager->is_logged_in())
			die(json_encode(array('message'=>'You are not logged in!', 'status'=>'Error!')));

		$im = GalleryItem::factory((int)$id);
		$content = $im->content->get();

		if (!$this->user->can_edit_content($content))
			die(json_encode(array('message'=>'You are not allowed to manage images in this gallery!', 'status'=>'Error!')));

		$title = strip_tags($this->input->post('title'));
		$desc = $this->input->post('desc');

		$im->title = $title;
		$im->text = $desc;
		$im->save();

		die(json_encode(array('message'=>'Image data is successfully saved.','status'=>'OK')));
	}

	public function remove_cache($id)
	{
		if (!$this->loginmanager->is_logged_in())
			die(json_encode(array('message'=>'You are not logged in!', 'status'=>'Error!')));

		$uri = Page::factory((int)$id)->uri;
		$this->load->library('cache');
		$this->cache->set_uri($uri);
		$ok = $this->cache->clear_cache();

		$results = array();

		if ($ok)
		{
			$results['status'] = 'OK';
			$results['message'] = 'Cache status: successfully removed cache file';
		}
		else
		{
			$results['status'] = 'error';
			$results['message'] = 'Cache status: failed to remove cache file';
		}

		die(json_encode($results));
	}


	public function repeatable_page($pid, $cid, $ppage, $pagenr)
	{
		$template = $this->input->post('template');
		$page = Page::factory((int)$pid);
		$content = Content::factory((int)$cid);

		$tempdom = new htmldom();
		$tempdom->load(
				'<html><body>'.$template.'</body></html>');
		$domitem = $tempdom->find('.iu-item', 0);

		$items = RepeatableItem::factory()
			->where_related_content('id', (int)$cid)
			->where('timestamp <=', time())
			->order_by('timestamp DESC')
			->get_paged_iterated($pagenr, $ppage);

		require_once("./iu-application/libraries/contentprocessor.php");
		require_once("./iu-application/libraries/contents/Repeatable.php");
		$instance = &get_instance();
		$cp = new Repeatable($instance);

		$response = '';

		foreach ($items as $i)
		{
			$newdomitem = clone $domitem;
			//add new item to placeholder
			$response .= $cp->process_template($newdomitem, $i, $page);
		}

		echo json_encode(array('content'=>$content->div,'html'=>$response));
	}

	public function gallery_page($pid, $cid, $ppage, $pagenr)
	{
		$template = $this->input->post('template');
		$page = Page::factory((int)$pid);
		$content = Content::factory((int)$cid);
		$ppage = (int)$ppage;

		$tempdom = new htmldom();
		$tempdom->load(
				'<html><body>'.$template.'</body></html>');
		$domitem = $tempdom->find('.iu-gallery-item', 0);

		$content = Content::factory((int)$cid);

		$items = GalleryItem::factory()
			->where_related_content('id', (int)$cid);

		if (empty($ppage))
			$items->get();
		else
			$items->get_paged_iterated($pagenr, $ppage);

		require_once("./iu-application/libraries/contentprocessor.php");
		require_once("./iu-application/libraries/contents/Gallery.php");
		$instance = &get_instance();
		$cp = new Gallery($instance);

		$response = '';

		foreach ($items as $i)
		{
			$newdomitem = clone $domitem;
			//add new item to placeholder
			$response .= $cp->process_template($newdomitem, $i, $page, $content);
		}

		echo json_encode(array('content'=>$content->div,'html'=>$response));
	}

}


?>