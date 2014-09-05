<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Hit extends DataMapper {

	public $has_one = array('page');

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new Hit();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public function fetch($from, $to=null)
	{
		if (empty($to))
			$to = time();

		return $this->where('created >=', $from)
			->where('created <=', $to);
	}

	public function unique($fields='ip_address')
	{
		if (is_array($fields))
			$fields = implode(', ', $fields);

		return $this->group_by($fields);
	}

	public static function timeflow($from, $to=null, $steps=20, $unique=true, $page_id=null, $returning=false)
	{
		$to = empty($to) ? time() : $to;

		$steps = ($steps > 31) ? 31 : $steps;

		$timespan = $to-$from;

		$zzz = $timespan/$steps;

		$hits = array();

		for ($i=0; $i<(int)$steps; $i++)
		{
			$step_from = $to-$zzz*($steps-$i);
			$step_to = $from+$zzz*($i+1);

			$h = Hit::factory()->fetch($step_from, $step_to);

			if (!empty($page_id))
				$h->where_related_page('id', $page_id);

			if ($unique)
				$h->unique();

			if ($returning)
				$h->where('returning', true);

			$h->get();

			$hits[] = $h->result_count();
		}

		return $hits;
	}

	function cnt()
	{
		if ($this->exists())
			return $this->result_count();
		else
			return $this->get()->result_count();
	}

}


?>