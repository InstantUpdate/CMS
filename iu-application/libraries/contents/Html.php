<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Html extends ContentProcessor {

	public function process($div, $content, $page)
	{
		//get classes
		if (empty($div->class))
			$classes = array();
		else
			$classes = explode(' ', $div->class);

		//add editable class
		if (!in_array("iu-content-html", $classes))
			$classes[] = "iu-content-html";

		//apply class to div
		$div->class = implode(' ', $classes);

		//add content
		if ($content->exists() && !empty($content->contents) && ($content->is_related_to($page) || !empty($content->is_global)))
		{
			$div->innertext = $content->contents;
		}


		return $div;
	}

}