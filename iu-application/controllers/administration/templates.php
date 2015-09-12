<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Templates extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		if (!$this->user->can('edit_templates'))
		{
			if ($this->user->can('edit_all_assets') || $this->user->can('edit_assets'))
				redirect('administration/assets');
			else
				show_error('You are not allowed to edit templates.');
		}

		$this->templatemanager->set_title("Manage Templates");

	}

	public function index()
	{
		if (defined('DEMO'))
			show_error(__("This function is disabled in online demo!"));

		$tpls = File::get_templates();
		$this->templatemanager->assign("templatez", $tpls);
		//$this->templatemanager->assign("uri", '');
		$this->templatemanager->show_template("templates_list");
	}

	public function choose_popup($id='template')
	{
		$this->templatemanager->assign("dom_id", $id);
		$this->templatemanager->show_template("templates_choose_popup");
	}

	public function choose()
	{
		$uri = implode('/', func_get_args());

		$this->templatemanager->assign("uri", $uri);
		$this->templatemanager->show_template("templates_choose");
	}

	public function duplicate($id, $newname)
	{
		$id = (int)$id;

		$old = File::factory()->get_by_id($id);

		if (!$old->exists())
			show_error("File #{$id} does not exist!");


		$dir = $old->directory();
		$new = $old->get_copy();
		$new->path = ltrim($dir.urldecode($newname), '/');

		if (is_file($new->path))
		{
			//already have a file
			$newbase = basename($new->path);
			$this->templatemanager->notify_next("File <a href='".site_url('administration/templates/edit/'.$new->path)."'>$newbase</a> already exists! Choose another name.", 'failure');
			redirect('administration/templates/edit/'.$old->path);
			die;
		}

		$succ = @file_put_contents($new->path, $old->contents());

		if ($succ === FALSE)
		{
			$new->data = $old->contents();
			$new->hash = md5($new->data);
		}
		else
			$new->hash = md5_file($old->path);

		$new->save();

		$this->templatemanager->notify_next("Template {$new->path} successfully created.", 'success');

		redirect('administration/templates/edit/'.$new->path);
	}

	public function save($id)
	{
		if (defined('DEMO'))
			show_error(__("This function is disabled in online demo!"));

		$id = (int)$id;

		$file = File::factory()->get_by_id($id);

		if (!$file->exists())
			show_error("File #{$id} does not exist!");

		$contents = $this->input->post('editarea');;

		$contents = str_replace(array('[/textarea]','[/form]'), array('</textarea>', '</form>'), $contents);

		$old_html = $file->contents();
		$old_editor = $file->editor_id;
		$old_ts = !empty($file->updated) ? $file->updated : $file->created;

		$succ = @file_put_contents($file->path, $contents);
		$file->editor_id = $this->user->id;

		if ($succ === FALSE)
		{
			$file->hash = md5($contents);
			$file->data = $contents;
		}
		else
			$file->hash = md5_file($file->path);

		$file->save();

		//create revision
		$rev = new FileRevision();
		$rev->contents = $old_html;
		$rev->user_id = $old_editor;
		$rev->created = $old_ts;
		$rev->save(array($file));

		$this->templatemanager->notify_next("Template {$file->path} successfully saved.", 'success');

		redirect('administration/templates/edit/'.$file->path);
	}

	public function edit()
	{
		$arr = func_get_args();
		$path = implode('/', $arr);
		$basename = basename($path);

		$file = File::factory()->get_by_path($path);

		if (($basename == '.htaccess') || (strpos($path, 'iu-application') !== FALSE))
			show_error("This file can not be edited!");

		if ($path == 'index.php')
			show_error("This file can not be edited!");

		if (is_dir($path))
			redirect('administration/templates');

		if (!$file->exists())
			show_error("File does not exist!");

		if (!file_exists($path) && empty($file->data))
			show_error("File does not exist!");

		if (!is_really_writable($path))
		{
			$this->templatemanager->notify("This file is read-only! Any changes made will be stored in the database instead.", "warning");
		}


		if (!$file->exists())
		{
			$file->path = $path;
			$file->checksum = md5_file($path);
			$file->editor_id = $this->user->id;
			$file->save();
		}

		$pages = $file->page->get();

		$this->templatemanager->assign("pages", $pages);

		$this->templatemanager->set_title("Edit Template");
		$this->templatemanager->assign("file", $file);
		$this->templatemanager->show_template("templates_edit");
	}

	public function remove($id)
	{
		if (!is_numeric($id))
			show_error("ID must be numeric!");

		File::factory((int)$id)->delete();

		redirect('administration/templates');
	}

	public function diff($fid, $rid)
	{
		//load diff engine
		require_once dirname(APPPATH).'/iu-application/libraries/diff/Diff.php';
		require_once dirname(APPPATH).'/iu-application/libraries/diff/Diff/Renderer/Html/SideBySide.php';

		$file = File::factory()->get_by_id((int)$fid);
		$revision = FileRevision::factory()->get_by_id((int)$rid);

		$file_html = explode("\n", $file->contents());
		$revision_html = explode("\n", $revision->contents);

		$options = array(
			'ignoreWhitespace' => true
		);

		$diff = new Diff($revision_html, $file_html, $options);

		$renderer = new Diff_Renderer_Html_SideBySide;
		$differences = $diff->Render($renderer);

		$this->templatemanager->assign("file", $file);
		$this->templatemanager->assign("revision", $revision);
		$this->templatemanager->assign("differences", $differences);
		$this->templatemanager->set_title("View Differences");
		$this->templatemanager->show_template("templates_diff");
	}

	public function revert($fid, $rid)
	{
		$file = File::factory()->get_by_id((int)$fid);
		$revision = FileRevision::factory()->get_by_id((int)$rid);

		$old_html = $file->contents();
		$contents = $revision->contents;
		$old_editor = $file->editor_id;
		$old_ts = !empty($file->updated) ? $file->updated : $file->created;

		$succ = @file_put_contents($file->path, $contents);
		$file->editor_id = $this->user->id;

		if ($succ === FALSE)
		{
			$file->hash = md5($contents);
			$file->data = $contents;
		}
		else
			$file->hash = md5_file($file->path);

		$file->save();

		//create revision
		$rev = new FileRevision();
		$rev->contents = $old_html;
		$rev->user_id = $old_editor;
		$rev->created = $old_ts;
		$rev->save(array($file));

		//$revision->delete();

		$this->templatemanager->notify_next("Revision #{$revision->id} successfully reverted.", 'success');

		redirect('administration/templates/edit/'.$file->path);
	}
}

?>