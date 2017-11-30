<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User extends DataMapper {

	public $table = 'users';
	public $has_many = array('page', 'permission', 'contentrevision', 'filerevision'

		,'assigned_pages'=>array(
			'class'=>'page'
			,'other_field'=>'editor'
			,'join_self_as'=>'user'
			,'join_other_as'=>'page'
			,'join_table'=>'pages_users'
		)
		,'assigned_contents'=>array(
			'class'=>'content'
			,'other_field'=>'editor'
			,'join_self_as'=>'user'
			,'join_other_as'=>'content'
			,'join_table'=>'contents_users'
		)

		,'repeatableitem', 'log'

	);

	public $has_one = array('userrole');
	public $validation = array(
	    'password' => array(
	        'rules' => array('encrypt')
	    )
	);

	public $auto_populate_has_one = true;


    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new User();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

    public function _encrypt($field, $param = '')
	{
	    if (!empty($this->{$field}))
	    {
	        if (empty($this->salt))
	        {
	            $this->salt = md5(uniqid(rand(), true));
	        }

	        $this->{$field} = sha1($this->salt . $this->{$field});
	    }

	    return true;
	}

  	public function log_in()
    	{
        // backup username for invalid logins
        $uname = $this->email;
		
	if (empty($uname)) {
		return false;
	}

        // Create a temporary user object
        $u = new User();

        // Get this users stored record via their email
        $u->where('email', $uname)
        	->where('active', 1)
			->get();

  		if (!$u->exists())
  			return false;

        // Give this user their stored salt
        $this->salt = $u->salt;

        // Validate and get this user by their property values,
        // this will see the 'encrypt' validation run, encrypting the password with the salt
        $this->validate()->get();

        // If the username and encrypted password matched a record in the database,
        // this user object would be fully populated, complete with their ID.

        // If there was no matching record, this user would be completely cleared so their id would be empty.
        if ($this->exists())
        {
            // Login succeeded
            return TRUE;
        }
        else
        {
            // Login failed, so set a custom error message
            //$this->error_message('login', 'Username or password invalid');

            // restore username for login field
            //$this->username = $uname;

            return FALSE;
        }
    }

	public static function select_html($selected = 0, $name = "user_id", $userrole = null, $class = "select", $template = '%name% (%userrole%)')
	{
		$users = new User();
		if (empty($userrole))
			$users->order_by('name')->get_iterated();
		else
			$users->where_related_userrole('name', $userrole)->order_by('access_level desc')->get_iterated();

		echo "<select name='$name' class='$class'>";
		foreach ($users as $usr)
		{
			if ($selected == $usr->id)
				$selstr = " selected='selected'";
			else
				$selstr = "";

			$format = parse_template($template, $usr->to_array());

			echo "<option value='{$usr->id}' class='user_select'$selstr>".$format."</option>\n";

		}
		echo "</select>";

	}

	public function is_admin()
	{
		return ($this->userrole->get()->name == 'Administrator');
	}

	public function is_administrator()
	{
		return $this->is_admin();
	}

	public function to_array()
	{
		$data = get_object_vars($this->stored);
		$role = $this->userrole->get();
		$data['userrole'] = $role->stored->name;
		$data['access_level'] = $role->stored->access_level;

		return $data;
	}

	public function get_profile_picture_url($template = "iu-resources/uploads/%picture%")
	{
		return base_url() . parse_template($template, $this->stored) . '?rand=' . rand(1, 99999);
	}

	public function get_profile_picture_thumb($width, $height = 0, $template = "iu-resources/uploads/%picture%")
	{
		$path = parse_template($template, $this->stored);


		if ($height > 0 && is_file(str_replace($this->picture, 'thumbs/'.$width.'x'.$height.'_'.$this->picture, $path)))
		{
			return base_url() . str_replace($this->picture, 'thumbs/'.$width.'x'.$height.'_'.$this->picture, $path) . '?rand=' . rand(1, 99999);
		}
		else
		{
			$im = image_create_from_file($path);

			$thumb = image_resize_crop($im, $width, $height);
			if ($height == 0)
				$height = imagesy($thumb);

			$th_path = str_replace($this->picture, 'thumbs/'.$width.'x'.$height.'_'.$this->picture, $path);

			$ok = image_to_file($thumb, $th_path);
			if ($ok)
				return base_url() . $th_path . '?rand=' . rand(1, 99999);
			else
				return base_url() . $path . '?rand=' . rand(1, 99999);
		}

	}

	public function can($permission_key)
	{
		//admin can edit all
		if ($this->is_admin())
			return true;

		$perm = Permission::factory()->get_by_key($permission_key);
		if (!$perm->exists())
			return false;

		return $this->is_related_to($perm);
	}

	public function can_edit_page($page)
	{
		//if page doesn't exist - assume we can't edit it
		if (empty($page))
			return false;

		//admin can edit all
		if ($this->is_admin())
			return true;

		if (is_numeric($page))
			$page = Page::factory($page);

		//if page doesn't exist, return false
		if (!$page->exists())
			return false;

		$page->user->get();

		//if user is owner, return true
		if ($page->user->id == $this->id)
			return true;

		//if user can edit all pages, return true
		if ($this->can('edit_all_pages'))
			return true;

		//if user is editor of a page, return true
		if ($this->is_related_to('assigned_pages', $page->id))
			return true;

		//by default, return false
		return false;
	}

	public function can_edit_content($content)
	{
		//admin can edit all
		if ($this->is_admin())
			return true;

		if (is_numeric($content))
			$content = Content::factory($content);

		//if content does not exist, assume we can edit it
		if (empty($content) || !$content->exists())
			return true;

		//if user can edit page, he can edit all contents
		if ($this->can_edit_page($content->page_id))
			return true;

		//if user is editor of a content, return true
		if ($this->is_related_to('assigned_contents', $content->id))
			return true;

		//by default, return false
		return false;
	}

	public function owns_page($page)
	{
		if (empty($page))
			return false;

		if (is_numeric($page))
			$page = Page::factory($page);

		//if page doesn't exist, return false
		if (!$page->exists())
			return false;

		return ($page->user_id == $this->id);

	}

	public function assets_path()
	{
		$assets_folder = Setting::value('assets_folder', false);
		if (empty($assets_folder))
			return false;

		if ($this->can('edit_all_assets'))
			$folder =  $assets_folder;

		if ($this->can('edit_assets'))
			$folder = $assets_folder.'/'.$this->id;
		else
			return false;

		if (!is_dir($folder))
			@mkdir($folder, 0777, true);

		return is_dir($folder) ? $folder . '/' : false;

	}

	public function last_updated_content()
	{
		$cr = ContentRevision::factory()
			->where_related_user('id', $this->id)
			->order_by('id DESC')
			->limit(1)
			->get();

		//$cr->check_last_query();

		return $cr->content->get();
	}

}


?>
