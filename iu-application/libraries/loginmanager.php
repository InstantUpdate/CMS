<?php defined('BASEPATH') OR exit('No direct script access allowed');

class LoginManager {

	private $framework;
	private $session;
	public $user = null;
	public $default_redirect = 'administration/dashboard';
	public $login_url = 'administration/auth/login';

	public function __construct()
	{
		$this->framework = &get_instance();
		$this->session = &$this->framework->session;

		if (empty($this->framework->session))
		{
			$this->framework->load->library('session');
			$this->session = &$this->framework->session;
		}

		if ($this->session->userdata('logged_in_user') !== false)
		{
			$usr = unserialize($this->session->userdata('logged_in_user'));
			$this->user = User::factory($usr->id);
		}


		//log out if not remembered and is inactive for (more than) 1 hour
		$remember = $this->session->userdata('remember');
		$last_active = $this->session->userdata('last_activity');
		$diff = time() - (int)$last_active;
		$diff = $diff / 3600;

		if ($this->is_logged_in() && !$remember && ($diff > 1))
		{
			$this->process_logout();
			redirect($this->get_redirect());
		}

	}

	public function process_login($user)
    {
        // attempt the login
        $success = $user->log_in();
        if($success)
        {
            // store the userid if the login was successful
            //$this->session->set_userdata('logged_in_id', $user->id);

            $usr = new stdClass;

            foreach($user->stored as $var=>$value)
			{
            	$usr->{$var} = $value;
            }

            $usr->userrole = new stdClass;

            foreach($user->userrole->stored as $var=>$value)
			{
            	$usr->userrole->{$var} = $value;
            }

			$this->user = $usr;

            unset($this->user->salt);
            unset($this->user->password);
            $this->session->set_userdata('logged_in_user', serialize($this->user));
            // if a redirect is necessary, return it.
            $redirect = $this->session->userdata('login_redirect');
            if ($redirect !== false)
                $success = $redirect;

            //$this->unsetRedirect();
        }
        else
        {
			$this->process_logout(); //do some cleanup, just in case
		}
        return $success;
    }

    public function process_logout()
	{
    	$this->user = null;
    	$this->session->unset_userdata('logged_in_user');
    	$this->session->unset_userdata('remember');
    	return $this;
    }

    public function get_redirect()
    {
    	/*if ($this->session->userdata('login_redirect'))
    		return $this->session->userdata('login_redirect');
    	else//*/
    		return $this->default_redirect;
	}

	public function set_redirect($url)
	{
		$this->session->set_userdata('login_redirect', $url);
		return $this;
	}

	public function unset_redirect()
	{
		$this->session->unset_userdata('login_redirect');
		return $this;
	}

    public function matches_roles($roles = array('administrator'), $bring_user_back_after_login = true)
	{
		if ($bring_user_back_after_login)
			$this->set_redirect(current_url());

		if (!$this->is_logged_in())
			return false;

		if (!is_array($roles))
			return ($this->user->userrole->name == $roles);
		else
			return in_array($this->user->userrole->name, $roles);

    }

    public function is($role)
    {
		return $this->matches_roles($role, false);
	}

    public function is_logged_in()
	{
    	return (empty($this->user)) ? false : true;
    }

	public function is_administrator()
	{
    	if (!$this->is_logged_in())
    		return false;

		return true;
	}

    public function is_admin()
    {
		return $this->is_administrator();
	}

	public function is_editor()
	{
		return $this->is_administrator() || $this->has_level(UserRole::factory()->get_by_name('editor')->access_level);
	}

    public function show_error($msg, $title = 'Login error', $label = "Log in", $link = null)
    {
    	$msg = "<p>$msg</p>";
    	if (!empty($label))
		{
    		if (empty($link))
    			$link = anchor($this->login_url, $label);

    		$msg .= "\n<p>$link</p>";
    	}

		show_error($msg, 403, $title);
	}

	public function has_level($lvl)
	{
		if (!$this->is_logged_in())
    		return false;

		return ($this->user->userrole->access_level >= (int)$lvl);
	}
}

?>