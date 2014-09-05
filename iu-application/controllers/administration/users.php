<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		$this->templatemanager->set_title(__("Manage users"));

	}

	public function index()
	{
		if (!$this->user->can('manage_users'))
		{
			$this->templatemanager->notify_next("You are not allowed to edit users!", 'failure');
			redirect('administration/dashboard');
		}

		$users = User::factory();

		$users->get();

		$this->templatemanager->assign('users', $users);

		$this->templatemanager->show_template("users_list");
	}

	public function add()
	{
		//require admin
		if (!$this->user->can('manage_users'))
		{
			$this->templatemanager->notify_next("You are not allowed to edit users!", 'failure');
			redirect('administration/dashboard');
		}

		$perms = Permission::factory()->get();
		$this->templatemanager->assign('permissions', $perms);

		$roles = UserRole::factory()->get();
		$this->templatemanager->assign('roles', $roles);

		$this->templatemanager->set_title(__("Add new user"));
		$this->templatemanager->show_template("users_edit");
	}

	public function edit($id)
	{
		$id = (int)$id;

		//require admin or self editing
		if (!$this->user->can('manage_users') && ($id !== $this->user->id))
		{
			$this->templatemanager->notify_next("You are not allowed to edit users!", 'failure');
			redirect('administration/dashboard');
		}

		$perms = Permission::factory()->get();
		$this->templatemanager->assign('permissions', $perms);

		$roles = UserRole::factory()->get();
		$this->templatemanager->assign('roles', $roles);

		$this->templatemanager->assign('userEdited', User::factory($id));
		$this->templatemanager->set_title(__("Edit user %s", User::factory($id)->name));
		$this->templatemanager->show_template("users_edit");
	}

	public function save($id = null)
	{
		$this->load->helper('email');

		if (!empty($id))
			$id = (int)$id;

		if (!$this->user->can('manage_users') && ($id !== $this->user->id))
		{
			$this->templatemanager->notify_next("You are not allowed to edit users!", 'failure');
			redirect('administration/dashboard');
		}

		//get user from db (or instantiate new user obj)
		if (empty($id))
			$user = new User();
		else
			$user = User::factory()->get_by_id($id);

		$user->name = $this->input->post('name');

		if ($this->user->can('manage_users'))
			$user->active = (bool)$this->input->post('active');

		//check name
		if (empty($user->name) || strlen($user->name) < 5)
		{
			$this->templatemanager->notify_next(__("Name can not be empty or shorter than 5 characters."), 'failure');
			redirect('administration/users/'. (empty($id) ? 'add' : 'edit/'.$id));
		}

		$role_id = $this->input->post('userrole_id');

		//get role
		if ($this->user->can('manage_users') && !empty($role_id))
			$role = UserRole::factory((int)$role_id);
		else
			$role = $this->user->userrole->get();

		//other data
		$email = trim($this->input->post('email'));

		//check e-mail
		if (!valid_email($email))
		{
			$this->templatemanager->notify_next(__("Entered e-mail address was not valid."), 'failure');
			redirect('administration/users/'. (empty($id) ? 'add' : 'edit/'.$id));
		}
		else
			$user->email = $email;

		//get, check and update password
		$password = trim($this->input->post('password'));
		$password2 = trim($this->input->post('password2'));

		if (empty($id) && empty($password) && empty($password2))
		{
			$this->templatemanager->notify_next(__("When creating new user you must specify his password!"), 'failure');
			redirect('administration/users/'. (empty($id) ? 'add' : 'edit/'.$id));
		}


		if (!empty($password))
		{
			if ($password != $password2)
			{
				$this->templatemanager->notify_next(__("Passwords differ!"), 'failure');
				redirect('administration/users/'. (empty($id) ? 'add' : 'edit/'.$id));
			}
			else
			{
				//if ($user->id != 1)
					$user->password = $password;
				//else
				//	$this->templatemanager->notify_next("Changing administrator password is disabled in the demo!", 'information');
			}

		}

		//prepare for upload
		$config['upload_path'] = './iu-resources/uploads/';
		$config['allowed_types'] = 'gif|jpg|png|jpeg';
		$config['max_size']	= '512';
		$config['max_width']  = '1024';
		$config['max_height']  = '1024';
		$config['encrypt_name'] = true;


		$this->load->library('upload', $config);

		//upload profile picture
		if (!empty($_FILES['picture']['name']))
		{
			if ( !$this->upload->do_upload('picture'))
			{
				show_error($this->upload->display_errors());
			}
			else
			{
				$data = $this->upload->data();

				$im = image_create_from_file($config['upload_path'].$data['file_name']);
				$im = image_resize($im, 150);
				image_to_file($im, $config['upload_path'].$data['file_name']);

				$user->picture = $data['file_name'];
			}
		}

		//save user
		$user->save(array($role)); //save user and role

		//permissions
		$perms_arr = $this->input->post('permissions');
		$perms = Permission::factory()->where_in('id', $perms_arr)->get();

		$user->delete(Permission::factory()->get()->all);
		$user->save($perms->all);

		//notify user
		if ($this->user->id != $id)
		{
			$this->templatemanager->notify_next(__("User is saved successfully."), "success");
		}
		else
		{
			$this->templatemanager->notify_next(__("Profile is updated successfully."), "success");
		}

		if ($this->loginmanager->is_editor())
			redirect('administration/users');

		//go back to previous page
		if (empty($_SERVER['HTTP_REFERER']))
			redirect('administration/users');
		else
			redirect($_SERVER['HTTP_REFERER']);
	}

	public function remove($id)
	{
		$id = (int)$id;
		$user = User::factory($id);

		if (!$this->user->can('manage_users'))
		{
			$this->templatemanager->notify_next("You are not allowed to edit users!", 'failure');
			redirect('administration/dashboard');
		}
		else if ($id != $this->user->id)
		{
			if (!empty($user->picture) && ($user->picture != 'user.jpg'))
				unlink('images/'.$user->picture);

			$user->delete();
			redirect('administration/users');
		}
		else
			$this->loginmanager->show_error("You can not delete yourself!", "Error", null);
	}

}

?>