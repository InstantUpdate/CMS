<?php defined('BASEPATH') OR exit('No direct script access allowed');

class UserRole extends DataMapper {

	public $table = 'userroles';
	public $has_many = array('user', 'permission');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new UserRole();
		if (!empty($id))
		{
			if (is_numeric($id))
				$instance->where('id', $id)->get();
			else
				$instance->where('name', $id)->get();
		}

		return $instance;
	}

	public static function select_html($selected = 0, $name = "userrole_id", $class = "select", $template = '%name%')
	{
		$roles = new UserRole();
		$roles->get_iterated();

		echo "<select name='$name' class='$class'>";
		foreach ($roles as $role)
		{
			if ($selected == $role->id)
				$selstr = " selected='selected'";
			else
				$selstr = "";

			$format = parse_template($template, $role->stored);

			echo "<option value='{$role->id}' class='user_select'$selstr>".$format."</option>\n";

		}
		echo "</select>";

	}

}


?>