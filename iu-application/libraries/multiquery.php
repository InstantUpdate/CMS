<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MultiQuery {

	private $vars = array();
	public $noofqueries, $errors;
	public $separator = '%';


	public function assign($varname, $varvalue)
	{
		$this->vars[$varname] = $varvalue;
	}

	private function _fetch_file($template_file)
	{
		$template = file_get_contents($template_file);

		foreach ($this->vars as $key=>$value)
			$template = str_replace($this->separator.$key.$this->separator, $value, $template);

		return $template;
	}

	private function _parse_sql($string)
	{
		$sql = explode("\n",$string);
		$out = array();
		$p = 0;
		$size = count($sql);
		$Terms = array('`',"'",'"');

		while ($p<$size)
		{
			$line=$sql[$p];
			$tline=trim($line);

			if ((!$tline) or ($tline[0]=='-'))
			{
				$p++;
				continue;
			}

			$instr= '';
			$InString= false;

			do
			{
				$instr .= $line;
				for($x=0;$x<strlen($tline);$x++)
				{
					if (($InString) and ($tline[$x]==$Term))
					{
						$InString=false;
						continue;
					}
					if ((!$InString) and (in_array($tline[$x], $Terms)))
					{
						$Term=$tline[$x];
						$InString=true;
						continue;
					}
				}

				$FoundSemicolon = (!$InString) && (substr($tline,-1)==';');
				$p++;

				if($p>=$size)
					break;

				$line= $sql[$p];
				$tline= trim($line);

			} while (($InString) || (!$FoundSemicolon));

			$out[]= $instr;
		}
		return $out;
	}

	public function execute_file($sql_file)
	{
		$return = true;
		$_ci = &get_instance();
		$arr = $this->_parse_sql($this->_fetch_file($sql_file));

		$this->noofqueries = 0;
		$this->errors = array();

		for ($j=0; $j<count($arr); $j++)
		{
			$query = $arr[$j];

			foreach ($this->vars as $var=>$value)
				$query = str_replace($this->separator . $var . $this->separator, $value, $query);

			$sql = $_ci->db->query(trim($query));
			$i = 0;

			if (($_ci->db->_error_number() != 0) || ($sql === false))
			{
				$this->errors[$i]['number'] = $j+1;
				$this->errors[$i]['query'] = $query;
				$this->errors[$i]['message'] = $_ci->db->_error_message();
				$this->errors[$i]['error_number'] = $_ci->db->_error_number();

				$return = false;
				$i++;
			}


			$this->noofqueries += 1;
		}

		return $return;
	}

}

?>