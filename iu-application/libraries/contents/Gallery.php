<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Gallery extends ContentProcessor {

	public $page_slug = 'page';

	public function process($div, $content, $page)
	{
		//get first (and hopefully only one) item
		$domitem = $div->find('.iu-gallery-item', 0);

		//if no domitem, provide one
		if (empty($domitem))
		{
			$tempdom = new htmldom();
			$tempdom->load(
				'<html><body><div class="iu-gallery-item" style="float:left;width:150px;">'."\n"
				.'<img class="iu-gallery-image" width="150px" src="'.base_url() . '/iu-resources/images/no-image.png" />'."\n"
				.'</div></body></html>');

			$domitem = $tempdom->find('.iu-gallery-item', 0);
		}

		if ($content->exists())
		{
			//$single = $this->_IU->input->get($this->read_slug);
			$logged_in = !empty($this->_IU->user);

			//read limit
			$limit = empty($div->{'data-per-page'}) ? 0 : (int)$div->{'data-per-page'};
			$order = empty($div->{'data-order-by'}) ? 'order ASC, id DESC' : $div->{'data-order-by'};

			//var_dump($limit); die;

			$pagenr = (int)$this->_IU->input->get($this->page_slug);
			$pagenr = empty($pagenr) ? 1 : $pagenr;


			//get images for current content
			$items = GalleryItem::factory()
				->where_related_content('id', $content->id)
				->order_by($order);

			if (empty($limit))
				$items->get();
			else
				$items->get_paged_iterated($pagenr, $limit);

			//get all images for this gallery
			$all_images = GalleryItem::factory()
				->where_related_content('id', $content->id)
				//->where_not_in('id', dm_column($items, 'id'))
				->order_by($order)
				->get();

		}

		$div->innertext = '';

		if (!empty($domitem) && $content->exists() && !empty($items) && ($items->result_count() > 0))
		{
			//if there are items, loop over them, create new repeatable item
			//and add it to the $div
			foreach ($items as $item)
			{
				$newdomitem = clone $domitem;
				//add new item to placeholder
				$div->innertext .= $this->process_template($newdomitem, $item, $page, $content);
			}

			$hide_pagination = empty($div->{'data-hide-pagination'}) ? false : true;

			//add pagination and hidden images (from other pages)
			if (!$hide_pagination && (isset($items->paged) && ($items->paged->total_rows > $limit)))
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

			/*else
			{
				$newdomitem = clone $domitem;

				$placeholder = new GalleryItem();
				$placeholder->image = 'iu-resources/images/sample.png';
				$placeholder->title = __('Sample Image');
				$placeholder->text = __('This is a sample image. It will disappear once you upload first image!');


				$div->innertext .= $this->process_template($newdomitem, $placeholder, $page, $content);
			} //*/

			//hidden images and descriptions
			$jackbox = empty($newdomitem->{'data-no-lightbox'}) ? true : false;

			if ($jackbox && $content->exists() && isset($all_images) && ($all_images->result_count() > 0))
			{
				$div->innertext .= '<ul rel="'.$content->div.'" class="iu-invisible">';

				foreach ($all_images as $im)
				{
					$thumb = new Image($im->image);
					$thumb_url = $thumb->thumbnail(100)->url;
					$div->innertext .= '<li id="iu_gallery_el_'.$im->id.'" class="iu-gallery-member" data-group="'.$content->div.'" data-title="'.$im->title.'" data-description="#iu_gallery_desc_'.$im->id.'" data-href="'.$im->image.'" data-thumbnail="'.$thumb_url.'"></li>';
					$div->innertext .= '<li id="iu_gallery_desc_'.$im->id.'"><h3>'.$im->title.'</h3> '.$im->text.'</li>';
				}

				$div->innertext .= '</ul>';
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
				$div->innertext .= __("Please define div with class \"iu-gallery-item\"");

		}

		//apply iu-content-repeatable class if doesn't exist
		if (empty($div->class))
			$classes3 = array();
		else
			$classes3 = explode(' ', $div->class);

		if (!in_array("iu-content-gallery", $classes3))
			$classes3[] = "iu-content-gallery";

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

		return $div;
	}









	public function process_template($newdomitem, $i, $page, $content)
	{
		//$single = $this->_IU->input->get('read');
		$logged_in = !empty($this->_IU->user);

		//set title, and link it if it's <a> tag
		$titlefield = $newdomitem->find('.iu-gallery-title');
		foreach ($titlefield as $field)
		{
			$field->innertext = $i->title;
			if (strtolower(trim($field->tag)) == 'a')
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

		//set link for <a> element; usable for "read more" links
		$itemlnks = $newdomitem->find('.iu-gallery-url');
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
		$authorfield = $newdomitem->find('.iu-gallery-author');
		foreach ($authorfield as $field)
			$field->innertext = $i->user->get()->name;

		//fill out text field
		$textfield = $newdomitem->find('.iu-gallery-text');
		foreach ($textfield as $field)
		{
			$limit = empty($field->{'data-limit'}) ? 0 : (int)$field->{'data-limit'};
			$field->innertext = $i->text;

		}

		//set images (and resize them)
		$images = $newdomitem->find('.iu-gallery-image');
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

			$jackbox = empty($newdomitem->{'data-no-lightbox'}) ? true : false;

			$im = new Image($i->image);

			$img->src = $im->thumbnail($width, $height)->url;
			$img->setAttribute('data-fullimg', $im->uri);
			$img->alt = $img->title = $i->title;
			if ($jackbox)
				$img->onclick = 'return iu_popup_gallery_image($(this));';
			$img->id = 'iu_image_'.$i->id;

			$img->setAttribute('data-group', $content->div);
			$img->setAttribute('data-title', $i->title);
			$img->setAttribute('data-href', $i->image);
			$img->setAttribute('data-description', '#iu_gallery_desc_'.$i->id);

			/*$classesarr = empty($img->class) ? array() : explode(' ', $img->class);
			$classesarr[] = 'iu-gallery-member';
			$img->class = implode(' ', $classesarr); //*/

		}

		//set image links for <a> elements
		$imagelnks = $newdomitem->find('.iu-gallery-image-url');
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
		$datefield = $newdomitem->find('.iu-gallery-date');
		foreach ($datefield as $field)
		{
			$format = empty($field->{'data-format'}) ? Setting::value('datetime_format', 'F j, Y @ H:i') : $field->{'data-format'};
			$field->innertext = date($format, $i->timestamp);
		}

		/*//add id
		$idfield = $newdomitem->find('.iu-gallery-item-id', 0);
		if (!empty($idfield))
			$idfield->value = $i->id;
		else
			$newdomitem->innertext .= '<input type="hidden" class="iu-gallery-item-id" value="'.$i->id.'" />';

		//add comments on single pages
		/*$comments = Setting::value('comments_enabled', 'no');
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
		}//*/

		//add new item to placeholder
		return $newdomitem->outertext . "\n\n";
	}

}