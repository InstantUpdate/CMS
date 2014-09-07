<?php

class Format
{
	private $input = '';
	private $output = '';
	private $tabs = 0;
	private $in_tag = FALSE;
	private $in_comment = FALSE;
	private $in_content = FALSE;
	private $inline_tag = FALSE;
	private $input_index = 0;
	
	public function HTML($input)
	{
		$this->input = $input;
		$this->output = '';
		
		$starting_index = 0;
		
		if (preg_match('/<\!doctype/i', $this->input)) {
			$starting_index = strpos($this->input, '>') + 1;
			$this->output .= substr($this->input, 0, $starting_index);
		}
		
		for ($this->input_index = $starting_index; $this->input_index < strlen($this->input); $this->input_index++) {
			if ($this->in_comment) {
				$this->parse_comment();
			} elseif ($this->in_tag) {
				$this->parse_inner_tag();
			} elseif ($this->inline_tag) {
				$this->parse_inner_inline_tag();
			} else {
				if (preg_match('/[\r\n\t]/', $this->input[$this->input_index])) {
					continue;
				} elseif ($this->input[$this->input_index] == '<') {
					if ( ! $this->is_inline_tag()) {
						$this->in_content = FALSE;
					}
					$this->parse_tag();
				} elseif ( ! $this->in_content) {
					if ( ! $this->inline_tag) {
						$this->output .= "\n" . str_repeat("\t", $this->tabs);
					}
					$this->in_content = TRUE;
				}
				$this->output .= $this->input[$this->input_index];
			}
		}
		
		return $this->output;
	}
	
	private function parse_comment()
	{
		if ($this->is_end_comment()) {
			$this->in_comment = FALSE;
			$this->output .= '-->';
			$this->input_index += 3;
		} else {
			$this->output .= $this->input[$this->input_index];
		}
	}
	
	private function parse_inner_tag()
	{
		if ($this->input[$this->input_index] == '>') {
			$this->in_tag = FALSE;
			$this->output .= '>';
		} else {
			$this->output .= $this->input[$this->input_index];
		}
	}
	
	private function parse_inner_inline_tag()
	{
		if ($this->input[$this->input_index] == '>') {
			$this->inline_tag = FALSE;
			$this->decrement_tabs();
			$this->output .= '>';
		} else {
			$this->output .= $this->input[$this->input_index];
		}
	}
	
	private function parse_tag()
	{
		if ($this->is_comment()) {
			$this->output .= "\n" . str_repeat("\t", $this->tabs);
			$this->in_comment = TRUE;
		} elseif ($this->is_end_tag()) {
			$this->in_tag = TRUE;
			$this->inline_tag = FALSE;
			$this->decrement_tabs();
			if ( ! $this->is_inline_tag() AND ! $this->is_tag_empty()) {
				$this->output .= "\n" . str_repeat("\t", $this->tabs);
			}
		} else {
			$this->in_tag = TRUE;
			if ( ! $this->in_content AND ! $this->inline_tag) {
				$this->output .= "\n" . str_repeat("\t", $this->tabs);
			}
			if ( ! $this->is_closed_tag()) {
				$this->tabs++;
			}
			if ($this->is_inline_tag()) {
				$this->inline_tag = TRUE;
			}
		}
	}
	
	private function is_end_tag()
	{
		for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if ($this->input[$input_index] == '<' AND $this->input[$input_index + 1] == '/') {
				return true;
			} elseif ($this->input[$input_index] == '<' AND $this->input[$input_index + 1] == '!') {
				return true;
			} elseif ($this->input[$input_index] == '>') {
				return false;
			}
		}
		return false;
	}
	
	private function decrement_tabs()
	{
		$this->tabs--;
		if ($this->tabs < 0) {
			$this->tabs = 0;
		}
	}
	
	private function is_comment()
	{
		if ($this->input[$this->input_index] == '<'
		AND $this->input[$this->input_index + 1] == '!'
		AND $this->input[$this->input_index + 2] == '-'
		AND $this->input[$this->input_index + 3] == '-') {
			return true;
		} else {
			return false;
		}
	}
	
	private function is_end_comment()
	{
		if ($this->input[$this->input_index] == '-'
		AND $this->input[$this->input_index + 1] == '-'
		AND $this->input[$this->input_index + 2] == '>') {
			return TRUE;
		} else {
			return FALSE;
		}
	}
	
	private function is_tag_empty()
	{
		$current_tag = $this->get_current_tag($this->input_index + 2);
		$in_tag = FALSE;
		
		for ($input_index = $this->input_index - 1; $input_index >= 0; $input_index--) {
			if ( ! $in_tag) {
				if ($this->input[$input_index] == '>') {
					$in_tag = TRUE;
				} elseif ( ! preg_match('/\s/', $this->input[$input_index])) {
					return FALSE;
				}
			} else {
				if ($this->input[$input_index] == '<') {
					if ($current_tag == $this->get_current_tag($input_index + 1)) {
						return TRUE;
					} else {
						return FALSE;
					}
				}
			}
		}
		return TRUE;
	}
	
	private function get_current_tag($input_index)
	{
		$current_tag = '';
		
		for ($input_index; $input_index < strlen($this->input); $input_index++) {
			if ($this->input[$input_index] == '<') {
				continue;
			} elseif ($this->input[$input_index] == '>' OR preg_match('/\s/', $this->input[$input_index])) {
				return $current_tag;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}
		
		return $current_tag;
	}
	
	private function is_closed_tag()
	{
		$closed_tags = array(
			'meta', 'link', 'img', 'hr', 'br', 'input',
		);
		
		$current_tag = '';
		
		for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if ($this->input[$input_index] == '<') {
				continue;
			} elseif (preg_match('/\s/', $this->input[$input_index])) {
				break;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}
		
		if (in_array($current_tag, $closed_tags)) {
			return true;
		} else {
			return false;
		}
	}
	
	private function is_inline_tag()
	{
		$inline_tags = array(
			'title', 'a', 'span', 'abbr', 'acronym', 'b', 'basefont', 'bdo', 'big', 'cite', 'code', 'dfn', 'em', 'font', 'i', 'kbd', 'q', 's', 'samp', 'small', 'strike', 'strong', 'sub', 'sup', 'textarea', 'tt', 'u', 'var', 'del', 'pre',
		);
		
		$current_tag = '';
		
		for ($input_index = $this->input_index; $input_index < strlen($this->input); $input_index++) {
			if ($this->input[$input_index] == '<' OR $this->input[$input_index] == '/') {
				continue;
			} elseif (preg_match('/\s/', $this->input[$input_index]) OR $this->input[$input_index] == '>') {
				break;
			} else {
				$current_tag .= $this->input[$input_index];
			}
		}
		
		if (in_array($current_tag, $inline_tags)) {
			return true;
		} else {
			return false;
		}
	}
}
