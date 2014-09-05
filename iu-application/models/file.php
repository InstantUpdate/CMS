<?php defined('BASEPATH') OR exit('No direct script access allowed');

class File extends DataMapper {

	public $has_many = array('page', 'filerevision');
	public $html = NULL;

    public function __construct($id = NULL)
	{
		parent::__construct($id);
    }

    public static function factory($id = null)
    {
		$instance = new File();
		if (!empty($id))
			$instance->where('id', $id)->get();
		return $instance;
	}

	public function mime_type()
	{
		$ext = end(explode('.', $this->path));

		if (in_array($ext, array('htm', 'html', 'php', 'phtml', 'php3', 'php4', 'php5', 'php5', 'php6') ))
			return 'text/html';
		else if (in_array($ext, array('js', 'jscript') ))
			return 'text/javascript';
		else if (in_array($ext, array('css', 'css3') ))
			return 'text/css';
		else if (in_array($ext, array('sql') ))
			return 'text/plain';
		else if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif') ))
			return 'image/'.str_replace('jpg', 'jpeg', $ext);
		else
			return get_mime_by_extension($this->path);
	}

	public function html()
	{
		if (empty($this->html))
		{
			if ($this->is_processable())
				$this->html = $this->process();
			else
				$this->html = $this->contents();
		}

		return $this->html;
	}

	public function is_downloadable()
	{
		switch ($this->mime_type())
		{
			case 'text/html': case 'text/javascript': case 'application/x-javascript': case 'text/css': case 'text/plain': case 'text/xml':
				return false;
				break;
			default:
				return true;
		} // switch
	}

	public function is_image()
	{
		switch($this->mime_type()){
			case 'image/jpeg': case 'image/png': case 'image/gif':
				return true;
				break;
			default:
				return false;
		} // switch
	}

	public function push($expires_relative_in_seconds=30)
	{

		if (!$this->is_downloadable() || $this->is_image())
			$disposition = "inline";
		else
			$disposition = "attachment";

		header('Content-type: '.$this->mime_type());
		header('Content-Disposition: '.$disposition.'; filename="'.basename($this->path).'"');
		header('Expires: ' . gmdate('D, d M Y H:i:s T', time()+$expires_relative_in_seconds));

		if (is_file($this->path))
			header('Content-Length: '.filesize($this->path));
		else if (!empty($this->data))
			header('Content-Length: '.strlen($this->data));
		else
			die('No content found for ' . $this->path);

		die($this->contents());
	}

	public function directory()
	{
		$parts = explode('/', $this->path);
		array_pop($parts);

		return implode('/', $parts) . '/';
	}

	public function contents()
	{
		if (empty($this->data))
		{
			if (is_file($this->path))
				return file_get_contents($this->path);
			else
				return false;
		}
		else
			return $this->data;
	}

	public function is_processable()
	{
		if ($this->contents() === false || $this->mime_type() != "text/html")
			return false;

		return strpos($this->contents(), '<'.'?') !== false;
	}

	public function process()
	{
		$cs_iu4_cwd = getcwd();

		if (empty($cs_iu4_cwd) || !$this->is_processable())
			return $this->contents();

		$cs_iu4_data = $this->contents();
		$cs_iu4_nwd = realpath(dirname($this->path));
		$cs_iu4_path = realpath($this->path);

		if (!chdir($cs_iu4_nwd))
			return $this->contents();

		while (ob_get_level())
			ob_end_clean();

		ob_start(); ob_start();
		eval('?'.'>'.preg_replace("/;*\s*\?>/", "; ?".">", str_replace('<'.'?=', '<'.'?php echo ', $cs_iu4_data)));

		$cs_iu4_output = '';
		while (ob_get_level())
			$cs_iu4_output .= ob_get_clean();

		chdir($cs_iu4_cwd);

		return $cs_iu4_output;
	}

	public function get_title()
	{
		if ($this->mime_type() != 'text/html')
			return NULL;

		$html = $this->html();

		$dom = new htmldom();
		$dom->load($html);
		$titleD = $dom->find('title', 0);
		if (!empty($titleD))
			$title = $titleD->innertext;

		if (empty($title))
			return "";

		//$this->title = $title;
		return $title;

	}

	public function get_meta($name)
	{
		if ($this->mime_type() != 'text/html')
			return NULL;

		$html = $this->html();

		$dom = new htmldom();
		$dom->load($html);
		$titleD = $dom->find('head meta[name='.$name.']', 0);
		if (!empty($titleD))
			return $titleD->content;
		else
			return '';

	}

	public function size($nice=false)
	{
		if (!$nice)
			return strlen($this->contents());
		else
			return nice_file_size(strlen($this->contents()));
	}

	public static function get_templates()
	{
		$files = File::factory()->get();

		$files_arr = array();

		foreach ($files as $f)
		{
			$ext = end(explode('.', $f->path));
			if (preg_match('/php[1-9]?|p?html?/si', $ext))
				$files_arr[] = $f;
		}

		return $files_arr;
	}

}


?>