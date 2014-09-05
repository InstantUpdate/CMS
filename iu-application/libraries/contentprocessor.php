<?php defined('BASEPATH') OR exit('No direct script access allowed');

abstract class ContentProcessor {

	protected $_IU = null;

	public function __construct($instance)
	{
		$this->_IU = $instance;
	}

	abstract public function process($div, $content, $page);

}