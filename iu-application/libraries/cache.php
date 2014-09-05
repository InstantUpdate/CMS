<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Cache
{
	private $uri = null;
	private $cache_path = null;

	public function __construct()
	{
		//$this->set_uri($uri);
		$this->cache_path = 'iu-application/cache/';
	}

	public function set_uri($uri)
	{
		$this->uri = $uri;
	}

	public function get_uri()
	{
		return $this->uri;
	}

	public function get_cache_file()
	{
		return $this->cache_path . md5($this->uri);
	}

	public function cache_exists($age)
	{
		if ($age < 1)
			return false;

		$now = time();

		$file = $this->get_cache_file();

		if (!is_file($file))
			return false;

		$time = filemtime($file);

		if (($now - $time) > $age)
		{
			//if older - remove existing cache file and returh false
			$this->clear_cache();
			return false;
		}
		else
			return true;
	}

	public function load_cache($die=true)
	{
		$file = $this->get_cache_file();
		if (is_file($file))
		{
			$ts = filemtime($file);
			$html = file_get_contents($file);
			$html = str_ireplace('<html', "<!-- Cached content for {$this->get_uri()} from ".date('Y-m-d H:i', $ts)." -->\n<html", $html);
			if ($die)
				die($html);
				//die($this->gzip_output($html));
			else
				return $html;
		}

		return false;
	}

	public function save_cache($html)
	{
		$file = $this->get_cache_file();
		$dir = dirname($file);
		if (is_really_writable($file) || is_really_writable($dir))
		{
			file_put_contents($file, $html);
			@touch($file);
			return true;
		}
		else
			return false;
	}

	public function clear_cache()
	{
		$file = $this->get_cache_file();

		if (is_file($file))
			@unlink($file);

		return !is_file($file);
	}

	public function gzip_output($html)
	{
		$HTTP_ACCEPT_ENCODING = $_SERVER['HTTP_ACCEPT_ENCODING'];
		if( headers_sent() ){
			$encoding = false;
		}elseif( strpos($HTTP_ACCEPT_ENCODING, 'x-gzip') !== false ){
			$encoding = 'x-gzip';
		}elseif( strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false ){
			$encoding = 'gzip';
		}else{
			$encoding = false;
		}

		if( $encoding ){
			header('Content-Encoding: '.$encoding);
			print("\x1f\x8b\x08\x00\x00\x00\x00\x00");
			$size = strlen($html);
			$html = gzcompress($html, 9);
			$html = substr($html, 0, $size);
		}

		die($html);
	}

}
?>