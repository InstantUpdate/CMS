<?php defined('BASEPATH') OR exit('No direct script access allowed');

final class LogSeverity {
	const Notice = 'notice';
	const Warning = 'warning';
	const Alert = 'alert';
	private function __construct() { }
}

class Log extends DataMapper {

	public $has_one = array('user');
	public $_IU = null;

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    	$this->_IU = &get_instance();
    }

    public static function factory($id = null)
    {
		$instance = new Log();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public static function write($message, $severity=NULL, $user_id=NULL, $ip_addr=NULL)
	{
		$log = self::factory();
		$log->message = $message;
		$log->severity = !empty($severity) ? $severity : LogSeverity::Notice;
		$log->user_id = !empty($user_id) ? (int)$user_id : 0;
		$log->ip_address = !empty($ip_addr) ? $ip_addr : $log->_IU->input->ip_address();

		$log->save();
	}

	public function message()
	{
		if (empty($this->user_id))
			return $this->ip_addr . ' ' . $this->message;
		else
			return User::factory($this->user_id)->name . ' ' . $this->message();
	}

	public function __toString()
	{
		return "[".date('d/m/Y @ H:i', $this->created)."] ".strtoupper($this->severity).": ".$this->message();
	}

}


?>