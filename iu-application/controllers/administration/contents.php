<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Contents extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);
	}

	public function add($pid, $name)
	{
		$page = Page::factory($pid);

		if (!$this->user->can_edit_page($page))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to add contents to this page!", 'failure');
			redirect('administration/dashboard');
		}

		$url_suffix = (isset($_GET['iu-popup'])) ? '?iu-popup' : '' ;

		$c = $page->content->where('div', $name)->get();

		if (!$c->exists())
		{
			$html = $page->body()->find('div[id='.trim($name).']', 0)->innertext;

			$c = new Content();
			$c->div = $name;
			$c->contents = $html;
			$c->type = 'static';
			$c->editor_id = $this->user->id;
			$c->save(array($page));
		}

		redirect('administration/contents/edit/'.$c->id.'/'.$c->div.$url_suffix);
	}

	public function edit($cid, $title)
	{
		$cid = (int)$cid;
		$content = Content::factory($cid);

		if (!$this->user->can_edit_content($content))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit this content!", 'failure');
			redirect('administration/dashboard');
		}

		$page = $content->page->limit(1)->get();
		$roles = UserRole::factory()->get();
		$ctypes = ContentType::factory()->get();

		if (!$page->exists())
			show_error("No page exists!");

		/*if (!$content->exists())
		{
			$html = $page->body()->find('div[id='.trim($title).']', 0)->innertext;
			//var_dump($html); die;
			$content->div = $title;
			$content->contents = $html;
			$content->editor_id = $this->user->id;
			$content->save(array($page));
		}//*/

		$divs = $page->get_div_ids();

		//$this->templatemanager->assign("css_file", $css_file);
		$this->templatemanager->assign("content", $content);
		$this->templatemanager->assign("divs", $divs);
		$this->templatemanager->assign("page", $page);
		$this->templatemanager->assign("roles", $roles);
		$this->templatemanager->assign("types", $ctypes);

		$suffix = strtolower($content->contenttype->get()->classname);

		$this->templatemanager->set_title("Edit Content");
		$this->templatemanager->show_template("contents_edit_".$suffix);
	}

	public function save_instant()
	{
		$relations = array();

		$uri = $this->input->post('page_uri');
		$div_id = $this->input->post('div');
		$contents = $this->input->post('contents');
		$id = (int)$this->input->post('id');

		if ($uri !== false)
		{
			$page = Page::factory()->get_by_uri($uri);
			$page->save();
			$relations[] = $page;
		}

		if ($id > 0)
		{
			$div = Content::factory()->get_by_id($id);
		}
		else
		{
			$div = Content::factory()->where('div', $div_id);

			if ($uri !== false)
				$div->where_related_page('uri', $uri);

			$div->limit(1)->get();
		}


		$revision = true;

		if (!$div->exists())
		{
			$div->div = $div_id;
			$div->editor_id = $this->user->id;
			$revision = false;

			$ctype = ContentType::factory()->get_by_classname('Html');
			$relations[] = $ctype;
		}
		else if (!$this->user->can_edit_content($div))
		{
			echo json_encode(array('status'=>'Error', 'message'=>__("You don't have enough permissions to edit this content!")));
			die;
		}


		$old_html = $div->contents;
		$old_editor = $div->editor_id;
		$old_ts = !empty($div->updated) ? $div->updated : $div->created;

		$div->editor_id = $this->user->id;
		$page->editor_id = $this->user->id;

		$div->contents = $contents;

		$page->save();
		$div->save($relations);

		//create revision
		if ($revision)
		{
			$rev = new ContentRevision();
			$rev->contents = $old_html;
			$rev->user_id = $old_editor;
			$rev->created = $old_ts;
			$rev->save(array($div, $this->user));
		}


		echo json_encode(array('status'=>'OK', 'message'=>__("Content \"%s\" is saved!", $div_id), 'id'=>$div->id));

	} //*/

	public function save($id)
	{
		$relations = array();

		//file_put_contents('post', json_encode($_POST));

		$pid = $this->input->post('pid');
		//$div_id = $this->input->post('div');
		$html = $this->input->post('html');

		if (!empty($pid))
		{
			$page = Page::factory()->get_by_id((int)$pid);
			$relations[] = $page;
		}

		$content = Content::factory()->get_by_id($id);

		if (!$this->user->can_edit_content($content))
		{
			if ($this->is_ajax_request())
				die(json_encode(array('status'=>'Error', 'message'=>__("You don't have enough permissions to edit this content!"))));
			else
			{
				$this->templatemanager->notify_next("You don't have enough permissions to edit this content!", 'failure');
				redirect('administration/dashboard');
			}
		}

		$old_html = $content->contents;
		$old_editor = $content->editor_id;
		$old_ts = !empty($content->updated) ? $content->updated : $content->created;

		//set/unset editors
		$editors = $this->input->post('editors');
		if (!empty($editors))
		{
			$editors_users = User::factory()->where_in('id', $editors)->get();
			$not_editors_users = User::factory()->where_not_in('id', $editors)->get();
		}

		//set page and content editor
		$page->editor_id = $this->user->id;
		$content->editor_id = $this->user->id;

		//set contents
		$content->contents = empty($html) ? '' : $html;

		//save page and contents
		$page->save();

		//set content type
		$ctype_id = (int)$this->input->post('type');
		if ($ctype_id > 0 && $this->user->owns_page($page))
			$relations[] = ContentType::factory($ctype_id);

		if ($this->user->owns_page($page))
			$content->is_global = ($this->input->post('global') == 'yes');

		//remove non and save editors
		if (!empty($editors) && $this->user->owns_page($page))
		{
			$content->delete_editor($not_editors_users->all);
			$content->save_editor($editors_users->all);
		}

		$content->save($relations);


		//create revision
		$rev = new ContentRevision();
		$rev->contents = $old_html;
		$rev->user_id = $old_editor;
		$rev->created = $old_ts;
		$rev->save(array($content, $this->user));

		$this->templatemanager->notify_next(__("Content \"%s\" is saved!", $content->div), 'success');

		if ($this->is_ajax_request())
			echo json_encode(array('status'=>'OK', 'message'=>__("Content \"%s\" is saved!", $div_id)));
		else
			redirect('administration/contents/edit/'.$content->id.'/'.$content->div);

	} //*/

	public function diff($cid, $rid)
	{
		//load diff engine
		require_once dirname(APPPATH).'/iu-application/libraries/diff/Diff.php';
		require_once dirname(APPPATH).'/iu-application/libraries/diff/Diff/Renderer/Html/SideBySide.php';

		$cont = Content::factory()->get_by_id((int)$cid);
		$revision = ContentRevision::factory()->get_by_id((int)$rid);

		$cont_html = explode("\n", $cont->contents);
		$revision_html = explode("\n", $revision->contents);

		$options = array(
			'ignoreWhitespace' => true,
			'ignoreNewLines' => true
		);

		$diff = new Diff($revision_html, $cont_html, $options);

		$renderer = new Diff_Renderer_Html_SideBySide;
		$differences = $diff->Render($renderer);

		$this->templatemanager->assign("content", $cont);
		$this->templatemanager->assign("revision", $revision);
		$this->templatemanager->assign("differences", $differences);
		$this->templatemanager->set_title("View Differences");
		$this->templatemanager->show_template("contents_diff");
	}

	public function revert($cid, $rid)
	{
		$content = Content::factory()->get_by_id((int)$cid);
		$revision = ContentRevision::factory()->get_by_id((int)$rid);

		$old_html = $content->contents;
		$contents = $revision->contents;
		$old_editor = $content->editor_id;
		$old_ts = !empty($content->updated) ? $content->updated : $content->created;

		$content->contents = $contents;
		$content->editor_id = $this->user->id;

		$content->save();

		//create revision
		$rev = new ContentRevision();
		$rev->contents = $old_html;
		$rev->user_id = $old_editor;
		$rev->created = $old_ts;
		$rev->save(array($content));

		//$revision->delete();

		$this->templatemanager->notify_next("Revision #{$revision->id} successfully reverted.", 'success');

		redirect('administration/contents/edit/'.$content->id.'/'.$content->div);
	}

	public function remove($cid, $back_to = 'pages')
	{
		$c = Content::factory((int)$cid);

		if ($c->exists())
		{
			$page = $c->page->get();
			$name = $c->div;

			$revs = $c->contentrevision->get();
			$revs->delete_all();
			$repeatables = $c->repeatableitem->get();

			$repswimg = $c->repeatableitem->where('image !=', null);
			foreach ($repswimg as $r)
			{
				$im = new Image($r->image);
				$im->remove_thumbnails();
				@unlink($r->image);
			}

			$repeatables->delete_all();

			$c->delete();

			$this->templatemanager->notify_next("Content \"$name\" is successfully removed!", 'success');

			if (empty($back_to))
				redirect('administration/pages/edit/'.$page->uri);
			else
				redirect('administration/'.str_replace('-', '/', $back_to));
		}
		else
		{
			$this->templatemanager->notify_next("Content is already deleted!", 'failure');

			redirect('administration/'.str_replace('-', '/', $back_to));
		}
	}
}

?>