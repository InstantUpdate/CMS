<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CS_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
		$this->load->helper('string');
		$this->templatemanager->popup(true); //hide sidebar, header, footer and notifications

	//	$this->templatemanager->set_template_name(Setting::value('default_template', 'default'));

	}

	public function index()
	{
		redirect($this->loginmanager->login_url);
	}

	public function login()
	{
		if (!empty($this->user))
			redirect('administration/dashboard');

        $cap = generate_captcha();
        $this->templatemanager->assign('captcha', $cap['image']);

		$this->templatemanager->set_title(__("User authentication"));
		$this->templatemanager->assign('goto', $this->loginmanager->get_redirect());

		$msg = $this->session->flashdata('auth_msg');

		if (!empty($msg))
			$this->templatemanager->notify($msg['text'], $msg['type'], $msg['title']);

		$this->templatemanager->load_template('auth_login');
	}

	public function logout()
	{
		$this->loginmanager->process_logout();
		redirect(site_url());
	}

	public function verify()
	{
		$usr = new User();
		$usr->email = $this->input->post('email', true);
		$usr->password = $this->input->post('password', true);
		$remember = (boolean)$this->input->post('remember', true);

		//var_dump($_POST);
		//die;

		$ok = $this->loginmanager->process_login($usr);

		if ($ok !== false)
		{
			if ($remember)
				$this->session->set_userdata('remember', $remember);

			Log::write('logged in', LogSeverity::Notice, $this->loginmanager->user->id);

			redirect($this->loginmanager->get_redirect());
		}
		else
		{
			$u = User::factory()->get_by_email($usr->email);

			if ($u->exists())
				Log::write('failed to login', LogSeverity::Notice, $u->id);
			else
				Log::write('failed to login', LogSeverity::Notice);

			$this->templatemanager->notify_next(__("You have entered wrong e-mail or password."), "error", __("Login Failed!"));
			redirect($this->loginmanager->login_url);
		}
	}

	public function register()
	{
        $cap = generate_captcha();
        $this->templatemanager->assign('captcha', $cap['image']);

		$this->templatemanager->set_title(__("User registration"));
		$this->templatemanager->show_template('auth_register');
	}

	public function apply()
	{
		//turned off
		die;
		if (!check_captcha())
		{
			$this->templatemanager->notify_next(__("You have entered wrong security code."), "error", __("Error!"));
			redirect("administration/auth/register");
			die;
		}

		$this->load->helper('email');

		//get all
		$name = trim($this->input->post("name", true));
		$email = trim($this->input->post("email", true));
		$pass = trim($this->input->post("password", true));
		$pass2 = trim($this->input->post("password2", true));

		//check if all present
		if (empty($name) || empty($email) || empty($pass) || empty($pass))
		{
			$this->templatemanager->notify_next(__("Please fill all fields so you can continue."), "error", __("Error"));
			redirect("administration/auth/register");
		}

		if (strlen($name) < 5)
		{
			$this->templatemanager->notify_next(__("Name must be longer than 4 characters!"), "error", __("Error"));
			redirect("administration/auth/register");
		}

		//check e-mail validity
		if (!valid_email($email))
		{
			$this->templatemanager->notify_next(__("Entered e-mail address was not valid."), "error", __("Error"));
			redirect("administration/auth/register");
		}

		//check if passwords are the same
		if ($pass != $pass2)
		{
			$this->templatemanager->notify_next(__("Passwords differ."), "error", __("Error"));
			redirect("administration/auth/register");
		}

		//check user by email
		$user = User::factory()->get_by_email($email);
		if ($user->exists())
		{
			$this->templatemanager->notify_next(__("User with that e-mail address already exists."), "error", __("Error"));
			redirect("administration/auth/register");
		}


		//create user
		$newu = new User();
		$newu->name = $name;
		$newu->email = $email;
		$newu->password = $pass;
		$newu->key = random_string('unique');

		$role = Userrole::get_lowest();

		$newu->save($role);

		//set variables for template
		$vars = array(
			'name'=>$name
			,'email'=>$email
			,'password'=>$pass
			,'website_title'=>Setting::value('website_title', CS_PRODUCT_NAME)
			,'activation_link'=>site_url('administration/auth/activate/'.$newu->id.'/'.$newu->key)
			,'site_url'=>site_url()
		);

		//get email template
		$template = file_get_contents(APPPATH . "templates/register.html");
		$template = __($template, null, 'email');
		$template .= "<br />\n<br />\n<br />\n" . __(file_get_contents(APPPATH . "templates/signature.html"), null, 'email');
		$template = parse_template($template, $vars);

		//send email
		$this->email->to("$name <$email>");
		$this->email->subject(__("%s registration", Setting::value('website_title', CS_PRODUCT_NAME), 'email'));
		$this->email->message($template);
		$this->email->set_alt_message(strip_tags($template));

		$from = Setting::value("default_email", false);

		if (empty($from))
			$from = "noreply@".get_domain_name(true);

		$this->email->from($from);

		$sent = $this->email->send();

		if ($sent)
			$this->templatemanager->notify_next(__("Account created. Please check your e-mail."), "notice", __("Notice"));
		else
			$this->templatemanager->notify_next(__("Activation e-mail could not be sent!"), "error", __("Error"));

		redirect("administration/auth/login");
	}

	public function activate($id, $key)
	{
		$u = User::factory()->where('id', (int)$id)->where("key", $key)->get();

		if ($u->exists())
		{
			$u->key = '';
			$u->active = 1;
			$u->save();

			$this->templatemanager->notify_next(__("Your account is now activated. Please log in."), "success", __("Success"));
			redirect("administration/auth/login");
		}
		else
		{
			$this->templatemanager->notify_next(__("Invalid key or user already activated."), "error", __("Error"));
			redirect("administration/auth/login");
		}
	}

	public function forgot()
	{
        $cap = generate_captcha();
        $this->templatemanager->assign('captcha', $cap['image']);

		//$this->templatemanager->notify(__("Enter e-mail address to reset password."), "notice", __("Notice"));
		$this->templatemanager->set_title(__("Forgot password"));
		$this->templatemanager->show_template('auth_forgot_password');
	}

	public function remind()
	{
		/*if (!check_captcha())
		{
			$this->templatemanager->notify_next(__("You have entered wrong security code."), "error", __("Error!"));
			redirect("administration/auth/forgot");
			die;
		}//*/

		$email = trim($this->input->post("email", true));

		$u = User::factory()->get_by_email($email);

		if (!$u->exists())
		{
			$this->templatemanager->notify_next(__("User with that e-mail does not exists!"), "error", __("Error"));
			redirect("administration/auth/forgot");
		}

		$u->key = random_string('unique');
		$u->save();

		Log::write('requested password change', LogSeverity::Notice, $u->id);

		//set variables for template
		$vars = array(
			'name'=>$u->name
			,'email'=>$u->email

			,'website_title'=>Setting::value('website_title', CS_PRODUCT_NAME)
			,'reset_link'=>site_url('administration/auth/resetpass/'.$u->id.'/'.$u->key)
			,'site_url'=>site_url()
		);

		//get email template
		$template = file_get_contents(APPPATH . "templates/forgot_password.html");
		$template = __($template, null, 'email');
		$template .= "<br />\n<br />\n<br />\n" . __(file_get_contents(APPPATH . "templates/signature.html"), null, 'email');
		$template = parse_template($template, $vars);

		//send email
		$this->email->to($u->email);
		$this->email->subject(__("%s password reset", Setting::value('website_title', CS_PRODUCT_NAME), 'email'));
		$this->email->message($template);
		$this->email->set_alt_message(strip_tags($template));

		$from = Setting::value("default_email", false);

		if (empty($from))
			$from = "noreply@".get_domain_name(true);

		$this->email->from($from);

		$sent = $this->email->send();

		if ($sent)
			$this->templatemanager->notify_next(__("Please check your e-mail for further information."), "notice", __("Notice"));
		else
			$this->templatemanager->notify_next(__("Activation e-mail could not be sent!"), "error", __("Error"));

		redirect("administration/auth/login");
	}

	public function resetpass($id, $key)
	{
		$u = User::factory()->where('id', (int)$id)->where("key", $key)->get();

		if ($u->exists())
		{
			$u->key = '';
			$u->active = 1;
			$u->salt = '';
			$p = random_string();
			$u->password = $p;
			$u->save();

			//set variables for template
			$vars = array(
				'name'=>$u->name
				,'email'=>$u->email
				,'password'=>$p

				,'website_title'=>Setting::value('website_title', CS_PRODUCT_NAME)
				,'site_url'=>site_url()
			);

			//get email template
			$template = file_get_contents(APPPATH . "templates/new_password.html");
			$template = __($template, null, 'email');
			$template .= "<br />\n<br />\n<br />\n" . __(file_get_contents(APPPATH . "templates/signature.html"), null, 'email');
			$template = parse_template($template, $vars);

			//send email
			$this->email->to("{$u->name} <{$u->email}>");
			$this->email->subject(__("%s password reset", Setting::value('website_title', CS_PRODUCT_NAME), 'email'));
			$this->email->message($template);
			$this->email->set_alt_message(strip_tags($template));

			$from = Setting::value("default_email", false);

			if (empty($from))
				$from = "noreply@".get_domain_name(true);

			$this->email->from($from);

			$sent = $this->email->send();

			if ($sent)
				$this->templatemanager->notify_next(__("New password is set. Please check your e-mail."), "success", __("Success"));
			else
				$this->templatemanager->notify_next(__("E-mail could not be sent!"), "error", __("Error"));

		}
		else
			$this->templatemanager->notify_next(__("Invalid key or password already reset."), "error", __("Error"));

		redirect("administration/auth/login");

	}

}

?>
