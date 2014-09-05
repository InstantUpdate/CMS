<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Image {

	public $uri, $path, $dir, $filename, $extension, $url;

    public function __construct($uri)
	{
		if (!is_file($uri))
			$uri = 'iu-resources/images/no-image.png';

    	$this->uri = $uri;
    	$this->init();
    }

	public static function factory($uri)
	{
		return new self($uri);
	}

	public static function none()
	{
		return self("iu-resources/images/no-image.png");
	}

	private function init()
	{
		if (empty($this->uri))
			return false;

		$this->path = realpath($this->uri);
		$this->url = base_url() . $this->uri;

		$this->filename = basename($this->uri);

		$this->base_url = str_replace($this->filename, '', $this->url);

		$this->dir = str_replace($this->filename, '', $this->uri);
		$this->extension = end(explode('.', $this->filename));

		return true;

	}

	public function thumb_path()
	{
		return $this->dir . 'thumbs/' . md5($this->filename). '/';
	}

	public function remove_thumbnails()
	{
		@rmdir_recursive($this->thumb_path());
		return !is_dir($this->thumb_path());
	}

	public function thumbnail($width, $height = 0)
	{
		$thumb_folder = $this->thumb_path();

		if (!is_dir($thumb_folder))
			@mkdir($thumb_folder, 0777, true);

		$thumb_file = $width . 'x' . $height . '.' . $this->extension;

		if (!is_file($thumb_folder . $thumb_file))
		{
			$img = image_create_from_file($this->path);
			$newimg = image_resize_crop($img, $width, $height);
			image_to_file($newimg, $thumb_folder . $thumb_file);
		}

		return is_file($thumb_folder . $thumb_file) ? new self($thumb_folder . $thumb_file) : false;
	}

}


?>