<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserRoles extends CS_Controller {

	public function __construct()
	{
		parent::__construct();

		//require login
		if (!$this->loginmanager->is_logged_in())
			redirect($this->loginmanager->login_url);

		if (!$this->user->can('manage_user_roles'))
		{
			$this->templatemanager->notify_next("You are not allowed to edit user roles!", 'failure');
			redirect('administration/dashboard');
		}

		$this->templatemanager->set_title(__("Manage roles"));
	}

	public function index()
	{
		$roles = UserRole::factory();

		$roles->get();

		$this->templatemanager->assign('roles', $roles);

		$this->templatemanager->show_template("userroles_list");
	}

	public function add()
	{
		$perms = Permission::factory()->get();
		$this->templatemanager->assign('permissions', $perms);

		$this->templatemanager->set_title(__("Add new role"));
		$this->templatemanager->show_template("userroles_edit");
	}

	public function edit($id)
	{
		$id = (int)$id;

		$perms = Permission::factory()->get();
		$role = UserRole::factory($id);

		if ($role->name == 'Administrator')
			$this->templatemanager->notify(__("This is a special user group. All users in this group will have ALL permissions allowed, regardless of their own permissions settings."), 'information');

		$this->templatemanager->assign('permissions', $perms);
		$this->templatemanager->assign('role', $role);
		$this->templatemanager->set_title(__("Edit role %s", UserRole::factory($id)->name));
		$this->templatemanager->show_template("userroles_edit");
	}

	public function save($id = null)
	{
		if (!empty($id))
			$id = (int)$id;

		$role = UserRole::factory($id);

		$role->name = $this->input->post('name', true);
		if (!$role->exists())
			$role->save(); //save role under new name

		$perms_arr = $this->input->post('permissions');
		$perms = Permission::factory()->where_in('id', $perms_arr)->get();

		$role->delete(Permission::factory()->get()->all);
		$role->save($perms->all);


		$this->templatemanager->notify_next(__("User role is saved successfully."), "success");

		redirect('administration/userroles');

	}

	public function remove($id)
	{
		$id = (int)$id;

		$role = UserRole::factory($id);

		if ($role->name == 'Administrator')
		{
			$this->templatemanager->notify_next(__("You can't remove \"Administrator\" role."), "failure");
			redirect('administration/userroles');
		}
		else if ($this->user->is_related_to($role))
		{
			$this->templatemanager->notify_next(__("You can't remove the role you're in."), "failure");
			redirect('administration/userroles');
		}
		else
		{
			$role->delete();

			$this->templatemanager->notify_next(__("User role is removed successfully."), "success");

			redirect('administration/userroles');
		}
	}

}

?>