<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Template DataMapper Model
 *
 * Use this basic model as a template for creating new models.
 * It is not recommended that you include this file with your application,
 * especially if you use a Template library (as the classes may collide).
 *
 * To use:
 * 1) Copy this file to the lowercase name of your new model.
 * 2) Find-and-replace (case-sensitive) 'Template' with 'Your_model'
 * 3) Find-and-replace (case-sensitive) 'template' with 'your_model'
 * 4) Find-and-replace (case-sensitive) 'templates' with 'your_models'
 * 5) Edit the file as desired.
 *
 * @license		MIT License
 * @category	Models
 * @author		Phil DeJarnett
 * @link		http://www.overzealous.com
 */
class Setting extends DataMapper {

	public $table = 'settings';
	public $validation = array(
	    'value' => array(
	        'rules' => array('serialize_value')
	    ),
	    'options' => array(
	        'rules' => array('serialize_value')
	    )
	);

	public $separator = "<br />";

    public function __construct($id=null)
	{
		parent::__construct($id);
    }

    public static function factory($name=null)
    {
		$instance = new Setting();
		if (!empty($name))
			$instance->where('name', $name)->get();
		return $instance;
	}

    public function _serialize_value($field)
    {
		if (is_array($this->{$field}))
		{
			$this->{$field} = implode('|',$this->{$field});
		}

		return true;
	}

	// This function takes care of settings value(s). For checkboxes it will
	// return array of values (even if even just one option is checked) and
	// string for all other types.
	public function get_value()
	{
		if ($this->type == "checkbox")
			return explode('|', $this->value);
		else
			return $this->value;
	}

	// Alias funciton for get_value()
	public function get_values()
	{
		return $this->get_value();
	}

	public function set_value($value)
	{
		if ($this->type == "checkbox")
			$this->value = implode('|', $value);
		else
			$this->value = $value;

		return $this;
	}

	public function set_values($values)
	{
		return $this->set_value($values);
	}

	// Same as above function, except this one returns options as an array for
	// all types including text, but option is ignored on text fields anyway
	public function get_options()
	{
		return explode('|',$this->options);
	}

	public function set_options($options = array())
	{
		$this->options = implode('|', $options);
		return $this;
	}

	public function add_option($option)
	{
		$options = $this->get_options();
		$options[] = $option;
		$this->set_options($options);
		return $this;
	}

	public function delete_option($option)
	{
		$options = $this->get_options();
		$remove = false;
		for ($i=0; $i<count($options); $i++)
		{
			if ($options[$i] == $option)
				$remove = $i;
		}
		if ($remove !== false)
			unset($options[$remove]);

		$this->set_options($options);
		return $this;
	}

	// This function returns html code for setting based on it's type db field.
	public function get_html()
	{
		$html = "";
		if ($this->type == "radio")
		{
			$options = $this->get_options();
			$value = $this->get_value();
			foreach ($options as $option)
			{
				if ($value == $option)
				{
					$html .= "<label><input class='settings_radio' type='radio' name='{$this->name}' value='$option' checked='checked' /> $option</label>{$this->separator}\n";
				}
				else
				{
					$html .= "<label><input class='settings_radio' type='radio' name='{$this->name}' value='$option' /> $option</label>{$this->separator}\n";
				}
			}
		}
		else if ($this->type == "checkbox")
		{
			$options = $this->get_options();
			$values = $this->get_values(); // checkboxes can have multiple selected values so this will return array

			foreach ($options as $option)
			{
				if (in_array($option, $values))
				{
					$html .= "<label><input class='settings_checkbox' type='checkbox' name='{$this->name}[]' value='$option' checked='checked' /> $option</label>{$this->separator}\n";
				}
				else
				{
					$html .= "<label><input class='settings_checkbox' type='checkbox' name='{$this->name}[]' value='$option' /> $option</label>{$this->separator}\n";
				}
			}
		}
		else if ($this->type == "select")
		{
			$options = $this->get_options();
			$value = $this->get_value();
			$html .= "<select name='{$this->name}' class='chzn-select' data-placeholder='$value' style='width:350px;' tabindex='2'>\n";
			foreach ($options as $option)
			{
				if ($value == $option)
				{
					$html .= "<option value='$option' selected='selected'>$option</option>\n";
				}
				else
				{
					$html .= "<option value='$option'>$option</option>\n";
				}
			}
			$html .= "</select>";
		}
		else // text
		{
			$value = $this->get_value();
			$html .= "<input type='text' name='{$this->name}' value='{$value}'  />";
		}

		return $html;
	}

	// Static function for views - using like Setting::HTML('option_name'); it will
	// automatically print out html code unless second argument is set to true,
	// in which case it'll return html code
	public static function html($name, $return = false)
	{
		$setting = new Setting();
		$setting->where('name', $name)->limit(1)->get();
		if (!$setting->exists())
			show_error('No such setting: '.$name . '<br />File: ' . __FILE__ . '<br />Line: ' . __LINE__);
		else
		{
			if ($return)
				return $setting->get_html();
			else
				echo $setting->get_html();
		}
	}

	public static function value($name, $default = false, $return = true)
	{
		$setting = new Setting();
		$setting->where('name', $name)->limit(1)->get();
		if (!$setting->exists())
			return $default;
		else
		{
			$val = $setting->get_value();

			if (empty($val))
				return $default;
			else
			{
				if ($return)
					return $setting->get_value();
				else
					echo $setting->get_value();
			}

		}
	}
}

/* End of file setting.php */
/* Location: ./application/models/setting.php */
