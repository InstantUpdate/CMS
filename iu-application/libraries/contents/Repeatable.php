<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Repeatable extends ContentProcessor {

	protected $read_slug = 'read';
	protected $page_slug = 'page';

	public function __($i)
	{
		parent::__construct($i);

		$this->_IU->load->helper('text');

	}

	public function process($div, $content, $page)
	{
		//get first (and hopefully only one) item
		$domitem = $div->find('.iu-item', 0);

		//if no domitem, provide one
		if (empty($domitem))
		{
			$tempdom = new htmldom();
			$tempdom->load(
				'<html><body><div class="iu-item" style="float:left;width:100%;">'."\n"
				.'<a class="iu-item-title" style="text-align: left;">Title</a><br />'."\n"
				.'<img class="iu-item-image" width="150px" src="'.base_url() . '/iu-resources/images/no-image.png" /><br />'."\n"
				.'<div style="width:100%; text-align: justify;" class="iu-item-text" data-limit="350">Text</div>'."\n"
				.'<p style="text-align: left;">Posted by <span class="iu-item-author">'.(empty($this->_IU->user) ? 'author' : $this->_IU->user->name).'</span>'
				.' on <span class="iu-item-date">'.date('Y-m-d H:i').'</span></p>'."\n"
				.'<hr />'."\n"
				.'</div></body></html>');

			$domitem = $tempdom->find('.iu-item', 0);
		}

		if ($content->exists())
		{
			$single = $this->_IU->input->get($this->read_slug);
			$logged_in = !empty($this->_IU->user);

			if (!empty($single))
			{
				//show only one
				$parts = explode('-', $single);
				$id = (int)$parts[0];
				$items = RepeatableItem::factory($id);
				//$page->set_title($items->title);
			}
			else
			{
				//read limit
				$limit = empty($div->{'data-per-page'}) ? 10 : (int)$div->{'data-per-page'};

				//var_dump($limit); die;

				$pagenr = (int)$this->_IU->input->get($this->page_slug);
				$pagenr = empty($pagenr) ? 1 : $pagenr;


				//find all items
				$items = RepeatableItem::factory()
					->where_related_content('id', $content->id)
					->where('timestamp <=', time())
					->order_by('timestamp DESC')
					->get_paged_iterated($pagenr, $limit);

			}
		}

		$div->innertext = '';

		if (!empty($domitem) && $content->exists() && !empty($items) && ($items->result_count() > 0))
		{
			//if there are items, loop over them, create new repeatable item
			//and add it to the $div
			foreach ($items as $i)
			{
				$newdomitem = clone $domitem;
				//add new item to placeholder
				$div->innertext .= $this->process_template($newdomitem, $i, $page);
			}

			$hide_pagination = empty($div->{'data-hide-pagination'}) ? false : true;

			//add pagination
			if (empty($single) && !$hide_pagination && ($items->paged->total_rows > $limit))
			{
				$paged = json_encode($items->paged);
				$div->innertext .= "<div class='iu-pagination' data-slug='".$this->page_slug."' data-paged='".$paged."'></div><img id='iu-pagination-loader' style='display: none; padding-left: 15px; padding-top: 5px' src='".site_url('iu-resources/images/ajax-load.png')."' alt='...' /><noscript><p>";
				$base_lnk = site_url($page->uri) . '?' . $this->page_slug . '=';

				//prev
				if ($items->paged->has_previous)
					$div->innertext .= '<span class="iu-paged-btn"><a href="'.$base_lnk.$items->paged->previous_page.'">' . __("&laquo; Prev").'</a></span> ';

				//pages
				for ($i=1; $i<=$items->paged->total_pages; $i++)
				{
					if ($i == $pagenr)
						$div->innertext .= '<span class="iu-paged-btn-disabled">' . $i . '</span> ';
					else
						$div->innertext .= '<span class="iu-paged-btn"><a href="'.$base_lnk.$i.'">' . $i . '</a></span> ';
				}

				//next
				if ($items->paged->has_next)
					$div->innertext .= '<span class="iu-paged-btn"><a href="'.$base_lnk.$items->paged->next_page.'">' . __("Next &raquo;").'</a></span> ';

				$div->innertext .= '</p></noscript>';
			}

		}
		else
		{
			//if there are no news items, apply other css to div
			if (empty($div->class))
				$classes2 = array();
			else
				$classes2 = explode(' ', $div->class);

			if (!in_array("iu-empty", $classes2) && !empty($this->_IU->user))
				$classes2[] = "iu-empty";

			$div->class = implode(' ', $classes2);

			if (empty($domitem) && !empty($this->_IU->user))
				$div->innertext .= __("Please define div with class \"iu-item\"");

		}

		//apply iu-content-repeatable class if doesn't exist
		if (empty($div->class))
			$classes3 = array();
		else
			$classes3 = explode(' ', $div->class);

		if (!in_array("iu-content-repeatable", $classes3))
			$classes3[] = "iu-content-repeatable";

		$div->class = implode(' ', $classes3);


		//hide original news item
		if (!empty($domitem))
		{
			if (empty($domitem->class))
				$classes = array();
			else
				$classes = explode(' ', $domitem->class);

			if (!in_array("iu-invisible", $classes))
				$classes[] = "iu-invisible";

			$domitem->class = implode(' ', $classes);

			$div->innertext .= "\n\n" . $domitem->outertext;

		}

		if (!empty($single))
		{
			$parts = explode('-', $single);
			$id = (int)$parts[0];
			$item = RepeatableItem::factory($id);

			/*$page->set_title($item->title);
			$page->set_meta('description', character_limiter(strip_tags($item->text), 350));
			$page->set_meta('keywords', ''); //todo*/
		}

		return $div;
	}

	public function process_template($newdomitem, $i, $page)
	{
		$single = $this->_IU->input->get('read');
		$logged_in = !empty($this->_IU->user);

		//set title, and link it if it's <a> tag
		$titlefield = $newdomitem->find('.iu-item-title');
		foreach ($titlefield as $field)
		{
			$field->innertext = $i->title;
			if (strtolower(trim($field->tag)) == 'a')
			{
				if (!empty($single))
					$field->href='javascript:;';
				else
				{
					$format = empty($field->{'data-format'}) ? '%page_url%?%read_slug%=%seo_title%' : (string)$field->{'data-format'};

					$seo_title = $i->id . '-' . cyr_url_title($i->title);

					$url = str_replace('%page_url%', site_url($page->uri), $format);
					$url = str_replace('%read_slug%', $this->read_slug, $url);
					$url = str_replace('%seo_title%', $seo_title, $url);
					$url = str_replace('%base_url%', base_url(), $url);
					$url = str_replace('%site_url%', site_url(), $url);

					$field->href = $url;
				}
			}
		}

		//set link for <a> element; usable for "read more" links
		$itemlnks = $newdomitem->find('.iu-item-url');
		foreach ($itemlnks as $lnk)
		{
			if (strtolower(trim($lnk->tag)) != 'a')
				continue;

			$format = empty($lnk->{'data-format'}) ? '%page_url%?%read_slug%=%seo_title%' : (string)$lnk->{'data-format'};

			$seo_title = $i->id . '-' . cyr_url_title($i->title);

			$url = str_replace('%page_url%', site_url($page->uri), $format);
			$url = str_replace('%read_slug%', $this->read_slug, $url);
			$url = str_replace('%seo_title%', $seo_title, $url);
			$url = str_replace('%base_url%', base_url(), $url);
			$url = str_replace('%site_url%', site_url(), $url);

			$lnk->href = $url;
		}

		//fill out author name
		$authorfield = $newdomitem->find('.iu-item-author');
		foreach ($authorfield as $field)
			$field->innertext = $i->user->get()->name;

		//fill out text field
		$textfield = $newdomitem->find('.iu-item-text');
		foreach ($textfield as $field)
		{
			$limit = empty($field->{'data-limit'}) ? 0 : (int)$field->{'data-limit'};

			//never show excerpt to a logged in user
			if ($logged_in)
				$shortened = false;
			else
				$shortened = (($single == false) && ($limit > 0));

			if ($shortened)
			{
				$format = empty($field->{'data-format'}) ? '%page_url%?%read_slug%=%seo_title%' : (string)$field->{'data-format'};
				$readmore = empty($field->{'data-readmore'}) ? 'read more &raquo;' : (string)$field->{'data-readmore'};

				$seo_title = $i->id . '-' . cyr_url_title($i->title);

				$url = str_replace('%page_url%', site_url($page->uri), $format);
				$url = str_replace('%read_slug%', $this->read_slug, $url);
				$url = str_replace('%seo_title%', $seo_title, $url);
				$url = str_replace('%base_url%', base_url(), $url);
				$url = str_replace('%site_url%', site_url(), $url);

				$field->href = $url;

				$field->innertext = character_limiter($i->text, $limit);
				$field->innertext .= ' <a href="'.$url.'" class="iu-read-more">'.$readmore.'</a>';
			}
			else
			{
				$field->innertext = $i->text;
			}


		}

		//set images (and resize them)
		$images = $newdomitem->find('.iu-item-image');
		foreach ($images as $img)
		{
			if (strtolower(trim($img->tag)) != 'img')
				continue;

			$width = preg_replace('/[^0-9]+/', '', $img->width);
			if (empty($width))
				$width = 300;

			$height = preg_replace('/[^0-9]+/', '', $img->height);
			if (empty($height))
				$height = 0;

			$im = new Image($i->image);

			$img->src = $im->thumbnail($width, $height)->url;
			$img->setAttribute('data-fullimg', $im->uri);
			$img->alt = $img->title = $i->title;

			if (!empty($single))
				$img->onclick = 'return iu_popup_image(this, \''.$i->title.'\');';

		}

		//set image links for <a> elements
		$imagelnks = $newdomitem->find('.iu-item-image-url');
		foreach ($imagelnks as $lnk)
		{
			if (strtolower(trim($lnk->tag)) != 'a')
				continue;

			if (!empty($i->image))
			{
				$im = new Image($i->image);
				$lnk->href = $im->url;
			}


		}

		//set date
		$datefield = $newdomitem->find('.iu-item-date');
		foreach ($datefield as $field)
		{
			$format = empty($field->{'data-format'}) ? Setting::value('datetime_format', 'F j, Y @ H:i') : $field->{'data-format'};
			$field->innertext = date($format, $i->timestamp);
		}

		//add id
		$idfield = $newdomitem->find('.iu-item-id', 0);
		if (!empty($idfield))
			$idfield->value = $i->id;
		else
			$newdomitem->innertext .= '<input type="hidden" class="iu-item-id" value="'.$i->id.'" />';

		//add comments on single pages
		$comments = Setting::value('comments_enabled', 'no');
		if (($single !== false) && ($comments != 'no'))
		{
			$comments_engine = Setting::value('comments_engine_id', 'no');
			if ($comments == 'Disqus')
			{
				$html = '<div id="disqus_thread"></div>';
			}
			else
			{
				$html = '<div style="text-align: center;" id="facebook_thread" class="fb-comments" data-href="'.site_url($page->uri).'?read='.$i->id.'-'.cyr_url_title($i->title).'" data-num-posts="2"></div>';
			}

			$newdomitem->innertext .= $html;
		}

		//add new item to placeholder
		return $newdomitem->outertext . "\n\n";
	}

}