<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Pages extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

	}

	public function index()
	{
		if ($this->user->can('edit_all_pages'))
			$pages = Page::factory()->get();
		else
		{
			$pages = Page::factory()
				->where_related_user('id', $this->user->id)
				->or_where_related_editor('id', $this->user->id)
				->get();
		}

		$this->templatemanager->assign("pages", $pages);

		$this->templatemanager->set_title("Manage Pages");
		$this->templatemanager->show_template("pages_list");
	}

	public function edit()
	{
		$arr = func_get_args();
		$uri = implode('/', $arr);

		$page = Page::factory()->get_by_uri($uri);

		if (!$page->exists())
		{
			$this->templatemanager->notify_next("Page {$uri} doesn't exist!", 'failure');
			redirect('administration/pages');
		}

		if (!$this->user->can_edit_page($page))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to edit this page!", 'failure');
			redirect('administration/dashboard');
		}

		$divs = $page->get_div_ids();
		//$contents = $page->content->get();
		$users = User::factory()->get();
		$roles = UserRole::factory()->get();

		$this->templatemanager->assign("page", $page);
		$this->templatemanager->assign("users", $users);
		$this->templatemanager->assign("roles", $roles);
		$this->templatemanager->assign("files", File::get_templates());
		$this->templatemanager->assign("divs", $divs);

		$this->templatemanager->set_title("Edit Page");
		$this->templatemanager->show_template("pages_edit");
	}

	public function add($based_on_page_id = null)
	{
		if (!empty($based_on_page_id))
			$oldpage = Page::factory()->get_by_id($based_on_page_id);
		else
			$oldpage = null;

		if (!$this->user->can('add_pages'))
		{
			$this->templatemanager->notify_next("You don't have enough permissions to add new page!", 'failure');
			redirect('administration/dashboard');
		}

		//$divs = $page->get_div_ids();
		//$contents = $page->content->get();
		$users = User::factory()->get();
		$roles = UserRole::factory()->get();


		$this->templatemanager->assign("oldPage", $oldpage);
		$this->templatemanager->assign("users", $users);
		$this->templatemanager->assign("roles", $roles);
		$this->templatemanager->assign("files", File::get_templates());
		//$this->templatemanager->assign("divs", $divs);

		$this->templatemanager->set_title("Add New Page");
		$this->templatemanager->show_template("pages_edit");
	}

	public function save($id = NULL)
	{
		if (!empty($id))
			$page = Page::factory((int)$id);
		else
		{
			$page = new Page();
			$page->uri = $this->input->post('uri');
		}

		$page->title = $this->input->post('title');
		$page->keywords = $this->input->post('keywords');
		$page->description = $this->input->post('description');
		$page->editor_id = $this->user->id;

		//caching?
		$page->custom_caching = ($this->input->post('custom_caching') == 'yes');
		if ($page->custom_caching)
			$page->custom_caching_duration = (int) str_replace(array(',','.'), '', $this->input->post('cache_duration'));

		//save editors
		$editors = $this->input->post('editors');
		$editors_users = User::factory()->where_in('id', $editors)->get();
		$not_editors_users = User::factory()->where_not_in('id', $editors)->get();

		//file
		$fid = $this->input->post('template');

		$save2 = array();

		if (!empty($fid))
		{
			if (is_numeric($fid))
			{
				$file = File::factory()->get_by_id((int)$fid);
			}
			else
			{
				$file = File::factory()->get_by_path($fid);

				if (!$file->exists())
				{
					$file->path = $fid;
					$file->checksum = md5_file($fid);
					$file->editor_id = $this->user->id;
					$file->save();
				}
			}



			$tpl = $file->path;

			if (empty($tpl) && !empty($id))
				$tpl = $page->file->path;

			if (!$file->exists())
			{
				if (is_file($tpl))
				{
					$file->path = $tpl;
					$file->save();
				}
				else
					show_error("Template file does not exist!");
			}

			$save2[] = $file;
		}


		if ($this->user->can_edit_page($page))
		{
			$owner_id = (int)$this->input->post('user');
			if (!empty($owner_id))
				$save2[] = User::factory($owner_id);
			else
				$save2[] = $this->user;
		}

		$page->save($save2);

		if ($this->user->can_edit_page($page))
		{
			//remove non editors, save editors
			$page->delete_editor($not_editors_users->all);
			$page->save_editor($editors_users->all);
		}


		$basename = basename($page->uri);

		$this->templatemanager->notify_next("Page $basename is successfully saved!", 'success');

		redirect('administration/pages/edit/'.$page->uri);

	}

	public function remove($id)
	{
		if (!is_numeric($id))
			show_error("ID must be numeric!");

		$page = Page::factory($id);

		if ($page->exists())
		{
			$contents = $page->contents->where('is_global', false)->get();

			//loop through all non global contents and remove revisions
			foreach ($contents as $c)
			{
				$revisions = $c->contentrevision->get();
				$revisions->delete_all();
			}

			//remove all non global contents
			$contents->delete_all();

			$uri = $page->uri;

			//remove page
			$page->delete();

			$this->templatemanager->notify_next("Page \"$uri\" is successfully removed!", 'success');
		}

		redirect('administration/pages');
	}

	public function linkdialog_choose()
	{
		$pages = Page::factory()->get();

		$this->templatemanager->assign('pages', $pages);
		$this->templatemanager->load_template('pages_linkdialog');
	}


}

?>