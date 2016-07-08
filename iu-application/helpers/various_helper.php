<?php defined('BASEPATH') OR exit('No direct script access allowed');

if (!function_exists('error_404')) {
	function error_404($pageName)
	{
		hook('404');
		show_404($pageName);
	}
}

if (!function_exists('hook')) {
	function hook($hookName)
	{
		$_ci = &get_instance();
		$_ci->pluginmanager->triggerHook($hookName);
	}
}

if (!function_exists('filters')) {
	function filters($filterName, $value = null)
	{
		$_ci     = &get_instance();
		$filters = $_ci->pluginmanager->triggerFilter($filterName, $value);
		return $filters->stackFilters($value);
	}
}

if (!function_exists('flat_filters')) {
	function flat_filters($filterName, $value = null)
	{
		$_ci     = &get_instance();
		$filters = $_ci->pluginmanager->triggerFilter($filterName, $value);
		return $filters->flattenStackFilters($value);
	}
}

if (!function_exists('filter')) {
	function filter($filterName, $value = null)
	{
		$_ci     = &get_instance();
		$filters = $_ci->pluginmanager->triggerFilter($filterName, $value);
		return $filters->callFilters($value);
	}
}

if (!function_exists('plugin_page_url')) {
	function plugin_page_url($pageSlug)
	{
		return site_url(CS_ADMIN_CONTROLLER_FOLDER.'/extend/'.$pageSlug);
	}
}


if (!function_exists('rel2abs'))
{
 function rel2abs($rel, $base)
    {
        /* return if already absolute URL */
        if (parse_url($rel, PHP_URL_SCHEME) != '' || substr($rel, 0, 2) == '//') return $rel;

        /* queries and anchors */
        if ($rel[0]=='#' || $rel[0]=='?') return $base.$rel;

        /* parse base URL and convert to local variables:
         $scheme, $host, $path */
        extract(parse_url($base));

        /* remove non-directory element from path */
        $path = preg_replace('#/[^/]*$#', '', $path);

        /* destroy path if relative url points to root */
        if ($rel[0] == '/') $path = '';

        /* dirty absolute URL */
        $abs = "$host$path/$rel";

        /* replace '//' or '/./' or '/foo/../' with '/' */
        $re = array('#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#');
        for($n=1; $n>0; $abs=preg_replace($re, '/', $abs, -1, $n)) {}

        /* absolute URL is ready! */
        return $scheme.'://'.$abs;
    }
}

if (!function_exists('embed'))
{
	function embed($file_url, $type=NULL)
	{
		if (empty($type))
		{
			$ext = end(explode('.', $file_url));

			if ($ext != 'css')
				$type = 'js';
			else
				$type = 'css';
		}

		if ($type == 'css')
			echo '<link href="'.$file_url.'" type="text/css" rel="stylesheet" media="screen" />'."\n";
		else
			echo '<script src="'.$file_url.'" type="text/javascript"> </script>'."\n";
	}
}

if (!function_exists('decompress_gzfile'))
{
	function decompress_gzfile($srcName, $dstName) {
	    $sfp = gzopen($srcName, "rb");
	    if ($sfp === false)
	    	return false;

	    $fp = fopen($dstName, "w");
	    if ($fp === false)
	    	return false;

	    while ($string = gzread($sfp, 4096)) {
	        fwrite($fp, $string, strlen($string));
	    }
	    gzclose($sfp);
	    fclose($fp);

	    return file_exists($dstName);
	}
}

if (!function_exists('compatible_gzinflate'))
{
	function compatible_gzinflate($gzData) {

		if ( substr($gzData, 0, 3) == "\x1f\x8b\x08" ) {
			$i = 10;
			$flg = ord( substr($gzData, 3, 1) );
			if ( $flg > 0 ) {
				if ( $flg & 4 ) {
					list($xlen) = unpack('v', substr($gzData, $i, 2) );
					$i = $i + 2 + $xlen;
				}
				if ( $flg & 8 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 16 )
					$i = strpos($gzData, "\0", $i) + 1;
				if ( $flg & 2 )
					$i = $i + 2;
			}
			return @gzinflate( substr($gzData, $i, -8) );
		} else {
			return false;
		}
	}
}

if (!function_exists('html_tidy'))
{
	function html_tidy($code, $tidy_config='')
	{

		if (!class_exists('Tidy'))
			return $code;

		$config = array(
		    'show-body-only' => false,
		    'clean' => false,
		    'char-encoding' => 'utf8',
		    'add-xml-decl' => false,
		    'add-xml-space' => false,
		    'output-html' => false,
		    'output-xml' => false,
		    'output-xhtml' => true,
		    'numeric-entities' => false,
		    'ascii-chars' => false,
		    'doctype' => 'omit',
		    'bare' => false,
		    'fix-uri' => true,
		    'indent' => true,
		    'indent-spaces' => 4,
		    'tab-size' => 4,
		    'wrap-attributes' => true,
		    'wrap' => 0,
		    'indent-attributes' => false,
		    'join-classes' => false,
		    'join-styles' => false,
		    'enclose-block-text' => true,
		    'fix-bad-comments' => true,
		    'fix-backslash' => true,
		    'replace-color' => false,
		    'wrap-asp' => false,
		    'wrap-jste' => false,
		    'wrap-php' => false,
		    'write-back' => true,
		    'drop-proprietary-attributes' => false,
		    'hide-comments' => false,
		    'hide-endtags' => false,
		    'literal-attributes' => false,
		    'drop-empty-paras' => false,
		    'enclose-text' => true,
		    'quote-ampersand' => true,
		    'quote-marks' => false,
		    'quote-nbsp' => true,
		    'vertical-space' => true,
		    'wrap-script-literals' => false,
		    'tidy-mark' => false,
		    'merge-divs' => false,
		    'repeated-attributes' => 'keep-last',
		    'break-before-br' => true,
		);

		if (empty($tidy_config))
			$tidy_config = &$config;

		$tidy = new Tidy();

		$code = html5tidy($code);
		$out = $tidy->repairString($code, $tidy_config, 'UTF8');
		$out = html5untidy($out);
		unset($tidy);
		unset($tidy_config);
		return "<!DOCTYPE html>\n".$out;
	}

}

if (!function_exists('html5tidy'))
{
	function html5tidy($code)
	{
		$html5tags = array('article', 'aside', 'bdi', 'command', 'details', 'dialog',
						'summary', 'figure', 'figcaption', 'footer', 'header',
						'hgroup', 'mark', 'meter', 'nav', 'progress', 'ruby',
						'rt', 'rp', 'section', 'time', 'wbr', 'audio', 'video',
						'source', 'embed', 'track', 'canvas', 'datalist', 'keygen',
						'output');

		foreach ($html5tags as $tag)
		{
			$code = str_ireplace('<'.$tag, '<div rel="'.$tag.'"', $code);
			$code = str_ireplace('</'.$tag.'>', '</div><br rel="'.$tag.'"/>', $code);
		}

		return $code;
	}
}

if (!function_exists('html5untidy'))
{
	function html5untidy($code)
	{
		$html5tags = array('article', 'aside', 'bdi', 'command', 'details', 'dialog',
						'summary', 'figure', 'figcaption', 'footer', 'header',
						'hgroup', 'mark', 'meter', 'nav', 'progress', 'ruby',
						'rt', 'rp', 'section', 'time', 'wbr', 'audio', 'video',
						'source', 'embed', 'track', 'canvas', 'datalist', 'keygen',
						'output');

		foreach ($html5tags as $tag)
		{
			$code = str_ireplace('<div rel="'.$tag.'"', '<'.$tag, $code);
			$code = preg_replace('%<p>[\s\r\n]*<br\srel="(\w+)"\s/>[\s\r\n]*</p>%six', '<br rel="$1" />', $code);
			$code = preg_replace('%</div>[\s\r\n]*<br\srel="(\w+)"\s/>%six', '</$1>', $code);
		}

		return $code;
	}
}

if (!function_exists('format_datepicker'))
{
	function format_datepicker($timestamp, $format=null)
	{
		if (empty($format))
			$format = Setting::value('datepicker_format', 'yy/mm/dd');

		$format = str_replace(array('dd','mm','yy'), array('d','m','Y'), $format);

		return date($format, $timestamp);
	}
}

if (!function_exists('parse_datepicker'))
{
	function parse_datepicker($date, $split=null, $format=null)
	{
		if (empty($format))
			$format = Setting::value('datepicker_format', 'yy/mm/dd');

		if (empty($split))
			$split = $format[2];

		$format_parts = explode($split, $format);
		$date_parts = explode($split, $date);

		$return = array();

		for ($i=0; $i<count($format_parts);$i++)
		{
			$part = trim($format_parts[$i]);

			if ($part == 'yy')
				$return['y'] = $date_parts[$i];
			else if ($part == 'dd')
				$return['d'] = $date_parts[$i];
			else if ($part == 'mm')
				$return['m'] = $date_parts[$i];
		}


		return $return;
	}
}

if (!function_exists('explode_tree'))
{
	function explode_tree($array, $delimiter = '_', $baseval = false)
	{
		if(!is_array($array)) return false;
		$splitRE   = '/' . preg_quote($delimiter, '/') . '/';
		$returnArr = array();
		foreach ($array as $key => $val) {
			// Get parent parts and the current leaf
			$parts  = preg_split($splitRE, $key, -1, PREG_SPLIT_NO_EMPTY);
			$leafPart = array_pop($parts);

			// Build parent structure
			// Might be slow for really deep and large structures
			$parentArr = &$returnArr;
			foreach ($parts as $part) {
				if (!isset($parentArr[$part])) {
					$parentArr[$part] = array();
				} elseif (!is_array($parentArr[$part])) {
					if ($baseval) {
						$parentArr[$part] = array('__base_val' => $parentArr[$part]);
					} else {
						$parentArr[$part] = array();
					}
				}
				$parentArr = &$parentArr[$part];
			}

			// Add the final part to the structure
			if (empty($parentArr[$leafPart])) {
				$parentArr[$leafPart] = $val;
			} elseif ($baseval && is_array($parentArr[$leafPart])) {
				$parentArr[$leafPart]['__base_val'] = $val;
			}
		}
		return $returnArr;
	}
}

if (!function_exists('dm_column'))
{
	function dm_column($set, $column_name = 'name')
	{
		$arr = array();
		foreach ($set as $obj)
		$arr[] = $obj->{$column_name};

		return $arr;
	}
}

if (!function_exists('percent'))
{
	function percent($num_amount, $num_total)
	{
		if (($num_amount == 0) || ($num_total == 0))
			return 0;

		$count1 = $num_amount / $num_total;
		$count2 = $count1 * 100;
		$count = number_format($count2, 0);
		return $count;
	}
}

if (!function_exists('yes_no'))
{
	function yes_no($cond)
	{
		return ($cond) ? "yes" : "no";
	}
}

if (!function_exists('true_false'))
{
	function true_false($cond)
	{
		return ($cond) ? "true" : "false";
	}

	function t_f($cond)
	{
		return true_false($cond);
	}
}

if (!function_exists('nice_file_size'))
{
	function nice_file_size($size)
	{
		//$size = ($file && @is_file($file)) ? filesize($file) : NULL;
		$FS = array("B","kB","MB","GB","TB","PB","EB","ZB","YB");
		$i=floor(log($size, 1024));
		return number_format($size/pow(1024, $i), ($i >= 1) ? 2 : 0) . ' ' . $FS[$i];
	}
}

if (!function_exists('generate_captcha') && function_exists('create_captcha'))
{
    function generate_captcha($config = array())
    {
    	clear_captcha_cache();

        $defaults = array(
            'word' => random_string('alnum', 5),
            'img_path' => './iu-resources/captcha/',
            'img_url' => base_url() . 'iu-resources/captcha/',
            'font_path' => './iu-resources/fonts/vera.ttf',
            'img_width' => 100,
            'img_height' => 40,
            'expiration' => 7200
        );

        $opts = array_merge($defaults, $config);

        $cap = create_captcha($opts);
    	$_ci = &get_instance();
    	$_ci->session->set_userdata('captcha', $cap['word']);
    	return $cap;
    }
}

if (!function_exists('clear_captcha_cache'))
{
	function clear_captcha_cache()
	{
		$diff = 3600; //delete captcha images older than one hour
		$path = FCPATH . "iu-resources/captcha/";
		$files = scandir($path);

		foreach ($files as $file)
		{
			//split file name by dot [timestamp].[msec].jpg
			$parts = explode('.', $file);

			//skip non-images
			if (end($parts) != 'jpg')
				continue;

			//delete files
			if (time() - ((int)reset($parts)) > $diff)
				@unlink($path . $file);
		}
	}
}


if (!function_exists('check_captcha'))
{
    function check_captcha($case_sensitive = false)
    {
        $_ci = &get_instance();
        $captcha = ($case_sensitive) ? $_ci->session->userdata('captcha') : strtoupper($_ci->session->userdata('captcha'));
        $entered_captcha = ($case_sensitive) ? $_ci->input->post('captcha') : strtoupper($_ci->input->post('captcha'));

        if (empty($captcha) || empty($entered_captcha))
            return false;
        else
            return trim($captcha) == trim($entered_captcha);
    }
}

if (!function_exists('modify_url'))
{
	function modify_uri($base, $arr)
	{
		//return $base;
		$_ci = &get_instance();
		$parts = $parts2 = $_ci->uri->ruri_to_assoc();

		foreach ($parts2 as $part=>$val)
		{
			if ($val == false)
				unset($parts[$part]);
		}

		$parts = array_merge($parts, $arr);
		return site_url($base . '/' . $_ci->uri->assoc_to_uri($parts));
	}
}


if (!function_exists('month_by_number'))
{
	function month_by_number($i)
	{
		if (empty($i))
			show_error(__FUNCTION__.": First parameter expects to be number between 1 and 12.");

		$i = (int)$i;

		if ($i < 1 || $i > 12)
			show_error(__FUNCTION__.": First parameter expects to be number between 1 and 12.");

		$months = array(
			1=>"January"
			,"February"
			,"March"
			,"April"
			,"May"
			,"June"
			,"July"
			,"August"
			,"September"
			,"October"
			,"November"
			,"December"
		);

		return $months[$i];
	}
}

if (!function_exists('__'))
{
	function __($phrase, $args=null, $filter=null)
	{
		$_ci = &get_instance();

		if ($filter == null)
			$filter = $_ci->in_admin() ? "administration" : "frontend";

		$translated = $_ci->translate->phrase($phrase, $filter);

		if ($args === null)
			return $translated;
		else
		{
			if (is_array($args))
				return vsprintf($translated, $args);
			else
				return sprintf($translated, $args);
		}
	}
}

if (!function_exists('_e'))
{
	function _e($phrase, $args=null, $filter=null)
	{
		echo __($phrase, $args, $filter);
	}
}

if (!function_exists('translit'))
{
	function translit($string, $inverse = false)
	{
		$arr = array(
			'а'=>'a',
			'б'=>'b',
			'в'=>'v',
			'г'=>'g',
			'д'=>'d',
			'ђ'=>'đ',
			'е'=>'e',
			'ж'=>'ž',
			'з'=>'z',
			'и'=>'i',
			'ј'=>'j',
			'к'=>'k',
			'л'=>'l',
			'љ'=>'lj',
			'м'=>'m',
			'н'=>'n',
			'њ'=>'nj',
			'о'=>'o',
			'п'=>'p',
			'р'=>'r',
			'с'=>'s',
			'т'=>'t',
			'у'=>'u',
			'ф'=>'f',
			'х'=>'h',
			'ц'=>'c',
			'ћ'=>'ć',
			'ч'=>'č',
			'џ'=>'dž',
			'ш'=>'š',

			'А'=>'A',
			'Б'=>'B',
			'В'=>'V',
			'Г'=>'G',
			'Д'=>'D',
			'Ђ'=>'Đ',
			'Е'=>'E',
			'Ж'=>'Ž',
			'З'=>'Z',
			'И'=>'I',
			'Ј'=>'J',
			'К'=>'K',
			'Л'=>'L',
			'Љ'=>'Lj',
			'М'=>'M',
			'Н'=>'N',
			'Њ'=>'Nj',
			'О'=>'O',
			'П'=>'P',
			'Р'=>'R',
			'С'=>'S',
			'Т'=>'T',
			'У'=>'U',
			'Ф'=>'F',
			'Х'=>'H',
			'Ц'=>'C',
			'Ч'=>'Č',
			'Ћ'=>'Ć',
			'Џ'=>'Dž',
			'Ш'=>'Š'
		);

		if ($inverse)
			$arr = array_flip($arr);

		return strtr($string, $arr);
	}
}

if (!function_exists('translit_no_accents'))
{
	function translit_no_accents($string)
	{
		$arr = array(
			'đ'=>'dj',
			'č'=>'c',
			'ć'=>'c',
			'ž'=>'z',
			'š'=>'s',
			'Đ'=>'Dj',
			'Č'=>'C',
			'Ć'=>'C',
			'Ž'=>'Z',
			'Š'=>'S'
		);

		return strtr(translit($string), $arr);
	}
}

if (!function_exists('cyr_url_title'))
{
	function cyr_url_title($string)
	{
		return url_title(strtolower(translit_no_accents(trim($string))));
	}
}


if (!function_exists('root_url'))
{
	function root_url($url)
	{
		$parts = explode('/', $url);
		array_shift($parts); array_shift($parts); array_shift($parts);
		return '/' . implode('/', $parts);
	}
}

if (!function_exists('get_domain'))
{
	function get_domain($url)
	{
		$nowww = str_replace('www.','',$url);
		$domain = parse_url($nowww);
		if(!empty($domain["host"]))
			return $domain["host"];
		else
			return $domain["path"];
	}
}

if (!function_exists('parse_template'))
{
	function parse_template($template, $data, $delim = '%')
	{
		if (is_object($data))
		{
			if (!empty($data->stored))
				$data = get_object_vars($data->stored);
			else
				$data = get_object_vars($data);
		}

		$format = $template;

		foreach ($data as $var=>$value)
		{
			if (!is_array($value))
				$format = str_replace($delim . $var . $delim, $value, $format);
		}

		return $format;
	}
}

if (!function_exists('image_resize_crop'))
{
	function image_resize_crop($image, $thumb_width, $thumb_height = 0)
	{
		//if thumb height is 0, auto-calculate it!
		if ($thumb_height == 0)
		{
			$xy = image_calculate_size($image, $thumb_width);
			$thumb_height = $xy[1];
		}

		$width = imagesx($image);
		$height = imagesy($image);

		$original_aspect = $width / $height;
		$thumb_aspect = $thumb_width / $thumb_height;

		if ($original_aspect >= $thumb_aspect)
		{
			// If image is wider than thumbnail (in aspect ratio sense)
			$new_height = $thumb_height;
			$new_width = $width / ($height / $thumb_height);
		}
		else
		{
			// If the thumbnail is wider than the image
			$new_width = $thumb_width;
			$new_height = $height / ($width / $thumb_width);
		}

		$thumb = imagecreatetruecolor($thumb_width, $thumb_height);

		// Resize and crop
		imagecopyresampled($thumb,
		                   $image,
		                   0 - ($new_width - $thumb_width) / 2, // Center the image horizontally
		                   0 - ($new_height - $thumb_height) / 2, // Center the image vertically
		                   0, 0,
		                   $new_width, $new_height,
		                   $width, $height);
		return $thumb;

	}
}

if (!function_exists('image_resize'))
{
	function image_resize($srcImage, $width, $height = 0)
	{
	   $srcWidth = imagesx($srcImage);
	   $srcHeight = imagesy($srcImage);

	   list($dstWidth, $dstHeight) = image_calculate_size($srcImage, $width, $height);

	   //resize
	   $dstImage = imagecreatetruecolor($dstWidth, $dstHeight);
	   imagecopyresampled($dstImage, $srcImage, 0, 0, 0, 0, $dstWidth, $dstHeight, $srcWidth, $srcHeight);
	   return $dstImage;
	}
}

if (!function_exists('image_calculate_size'))
{
	function image_calculate_size($img, $dst_w, $dst_h = 0)
	{
		if (is_resource($img))
		{
			$w = imagesx($img);
			$h = imagesy($img);
		}
		else
		{
			list($w, $h) = getimagesize($img);
		}

		if ($w == 0 and $h == 0) return array(0,0);
		if ($dst_w == 0 and $dst_h == 0) return array($w, $h);

		if ($dst_w == 0)
		{
			//autocalculate width
			$retH = $dst_h;
			$proportion = $h / $dst_h;
			$retW = round($w / $proportion);
		}
		else if ($dst_h == 0)
		{
			//autocalculate height
			$retW = $dst_w;
			$proportion = $w / $dst_w;
			$retH = round($h / $proportion);
		}
		else
		{
			$retW = $dst_w;
			$retH = $dst_h;
		}

		return array($retW, $retH);
	}
}

if (!function_exists('image_create_from_file'))
{
	function image_create_from_file($file)
	{
		$info = pathinfo($file);
		$ext = strtolower($info['extension']);
		if (!file_exists($file)) { $ext = false; } //function returns false
		switch ($ext) {
			case 'jpg' :
			 $image = imagecreatefromjpeg($file);
			 break;
			case 'jpeg' :
			 $image = imagecreatefromjpeg($file);
			 break;
			case 'gif' :
			 $image = imagecreatefromgif($file);
			 break;
			case 'png' :
			 $image = imagecreatefrompng($file);
			 break;
			case 'xmp' :
			 $image = imagecreatefromxpm($file);
			 break;
			case 'xbm' :
			 $image = imagecreatefromxbm($file);
			 break;
			case 'wbmp' :
			 $image = imagecreatefromwbmp($file);
			 break;
			default :
			 $image = false;
		}
		return $image;
	}
}


if (!function_exists('image_to_file'))
{
	function image_to_file($im, $file)
	{
		$ext = strtolower(end(explode('.', $file)));
		$return = true;
		switch ($ext) {
			case 'jpg' :
			 imagejpeg($im, $file);
			 break;
			case 'jpeg' :
			 imagejpeg($im, $file);
			 break;
			case 'gif' :
			 imagegif($im, $file);
			 break;
			case 'png' :
			 imagepng($im, $file);
			 break;
			case 'wbmp' :
			 imagewbmp($im, $file);
			 break;
			default :
			 $return = false;
		}
		return $return;
	}
}

if (!function_exists('image_to_stream'))
{
	function image_to_stream($im, $type='jpg')
	{
		switch ($type) {
			case 'jpg' :
			 imagejpeg($im);
			 break;
			case 'jpeg' :
			 imagejpeg($im);
			 break;
			case 'gif' :
			 imagegif($im);
			 break;
			case 'png' :
			 imagepng($im);
			 break;
			case 'wbmp' :
			 imagewbmp($im);
			 break;
			default :
			 $return = false;
		}
	}
}

if (!function_exists('rmdir_recursive'))
{
	function rmdir_recursive($dir)
	{
	    $files = scandir($dir);
	    array_shift($files);    // remove '.' from array
	    array_shift($files);    // remove '..' from array

	    foreach ($files as $file) {
	        $file = $dir . '/' . $file;
	        if (is_dir($file)) {
	            rmdir_recursive($file);
	            rmdir($file);
	        } else {
	            unlink($file);
	        }
	    }
	    rmdir($dir);
	}
}

if (!function_exists('get_domain_name'))
{
	function get_domain_name($strip_www = false, $url=null)
	{
		if (empty($url))
			$url = site_url();

		$parts = explode('/', $url);

		if ($strip_www)
			return str_replace('www.', '', $parts[2]);
		else
			return $parts[2];
	}
}

if (!function_exists('format_version'))
{
	function format_version($version, $as_integer = false)
	{
		//$ver = trim(file_get_contents(realpath(FCPATH."installed.txt")));
		$ver = str_replace('.', '', $version);
		$ver = str_pad($ver, 2, "0");

		if (!$as_integer)
		{
			$verarr = str_split($ver);
			return implode('.', $verarr);
		}
		else
			return (int)$ver;
	}
}

if (!function_exists('get_app_version'))
{
	function get_app_version($as_integer = false)
	{
		if (!is_installed())
			return false;

		$ver = trim(file_get_contents(realpath(FCPATH."installed.txt")));
		return format_version($ver, $as_integer);
	}
}

if (!function_exists('is_db_conf_empty'))
{
	function is_db_conf_empty()
	{
		require_once(realpath(FCPATH."iu-application/config/database.php"));

		if (!isset($active_group))
			$active_group = 'default';

		return empty($db[$active_group]['username']);
	}

}

if (!function_exists('is_installed'))
{
	function is_installed()
	{
		$installed = trim(file_get_contents(realpath(FCPATH."installed.txt")));
		if (empty($installed))
			return false;
		else
			return true;
	}
}

if (!function_exists('select_html'))
{
	function select_html($set, $selected = 0, $template = '%name%', $name = null, $class = "select")
	{

		if (is_object($selected))
			$selected = $selected->id;

		if (empty($name))
			$name = strtolower(get_class($set)) . '_id';

		$ret = "<select name='$name' class='$class'>";
		foreach ($set as $obj)
		{
			if ($selected == $obj->id)
				$selstr = " selected='selected'";
			else
				$selstr = "";

			$format = parse_template($template, $obj->stored);

			$ret .= "<option value='{$obj->id}' class='user_select'$selstr>".$format."</option>\n";

		}
		$ret .= "</select>";

		return $ret;
	}
}

if (!function_exists('format_price'))
{
	function format_price($amount, $currency)
	{
		$rules = array(
			'USD'=>array(
				'prefix'=>'$'
			)
			,'EUR'=>array(
				'prefix'=>'&euro;'
			)
			,'GBP'=>array(
				'prefix'=>'&pound;'
			)
			,'JPY'=>array(
				'prefix'=>'&yen;'
			)
			,'CNY'=>array(
				'prefix'=>'&yen;'
			)
		);

		$ret = $amount;

		if (isset($rules[$currency]['prefix']))
			$ret = $rules[$currency]['prefix'] . $ret;
		else if (isset($rules[$currency]['suffix']))
			$ret = $ret . $rules[$currency]['suffix'];
		else
			$ret .= $currency;

		return $ret;
	}
}

//eof