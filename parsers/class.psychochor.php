<?php
class Psychochor{
	private static $base_url = 'http://psycho-chor.de/';
	private static $event_list_page = 'de/konzerte/aktuell.html';

	public static function read_events(){
		$xml = load_xml(self::$base_url . self::$event_list_page);
		$event_div = $xml->getElementById('c1074');
		$divs = $event_div->getElementsByTagName('div');
		
		foreach ($divs as $event){
			$class = $event->getAttribute('class');
			if (strpos($class, 'wrapper')!==false) continue;
			$tables = $event->getElementsByTagName('table');
			if ($tables->length <1) continue;
			self::read_event($event);
		}
	}

	public static function read_event($xml){
		$title = self::read_title($xml);
		$table_rows = $xml->getElementsByTagName('tr');
		foreach ($table_rows as $row){
			$cols = $row->getElementsByTagName('td');
			$first = null;
			$second = null;
			foreach ($cols as $col){
				if ($first == null){
					$first = trim($col->nodeValue);
				} elseif ($second == null){
					$second = trim($col->nodeValue);
					break;
				}
			}
			print $first.NL;
			print $second.NL; die();
		}
		
		
		
		
		
		
		$description = self::read_description($xml);
		die($description);
		$start = self::date(self::read_start($xml));
		$location = 'Café Wagner, Wagnergasse 26, 07743 Jena';

		$coords = '50.931251, 11.580310';

		$tags = self::read_tags($xml);
		$links = self::read_links($xml,$source_url);		
		$attachments = self::read_images($xml);
		//print $title . NL . $description . NL . $start . NL . $location . NL . $coords . NL . 'Tags: '. print_r($tags,true) . NL . 'Links: '.print_r($links,true) . NL .'Attachments: '.print_r($attachments,true).NL;
		$event = Event::get_imported($source_url);
		if ($event == null){
			//print 'creating new event for '.$source_url.NL;
			$event = Event::create($title, $description, $start, null, $location, $coords,$tags,$links,$attachments,false);
			$event->mark_imported($source_url);
		} else {
			//print 'updating event for '.$source_url.NL;
			$event->set_title($title);
			$event->set_description($description);
			$event->set_start($start);
			$event->set_location($location);
			$event->set_coords($coords);
			foreach ($tags as $tag) $event->add_tag($tag);
			foreach ($links as $link) $event->add_link($link);
			foreach ($attachments as $attachment) $event->add_attachment($attachment);
			$event->save();
		}
	}

	private static function read_title($xml){
		$headings = $xml->getElementsByTagName('h1');
		foreach ($headings as $heading){
			return trim($heading->nodeValue);
		}
		return null;
	}

	private static function read_description($xml){
		$articles = $xml->getElementsByTagName('article');
		$description = '';
		foreach ($articles as $article){
			$paragraphs = $article->getElementsByTagName('p');
			$first=true;
			foreach ($paragraphs as $paragraph){
				if ($first){
					$first = false;
					continue;
				}
				$text = trim($paragraph->textContent);
				if (!empty($text)) {
					if ($text == 'Sorry, the comment form is closed at this time.') continue;
					$description .= str_replace('€Kategorie', "€\nKategorie", $text) . NL;
				}
			}
		}
		return $description;
	}

	private static function read_start($xml){
		global $db_time_format;
		$articles = $xml->getElementsByTagName('article');
		$description = '';
		foreach ($articles as $article){
			$paragraphs = $article->getElementsByTagName('p');
			foreach ($paragraphs as $paragraph){
				$text = trim($paragraph->textContent);
				if (preg_match('/\d\d.\d\d.\d\d:\d\d/',$text)){
					return $text;
				}
				if (preg_match('/\d\d.\d\d.\d\d\d\d/',$text)){
					return $text;
				}
			}
		}
		return null;
	}

	private static function read_tags($xml){
		global $db_time_format;
		$articles = $xml->getElementsByTagName('article');
		$description = '';
		foreach ($articles as $article){
			$paragraphs = $article->getElementsByTagName('p');
			foreach ($paragraphs as $paragraph){
				$text = trim($paragraph->textContent);
				$pos = strpos($text, 'Kategorie:');
				if ($pos!==false) {
					$tags = explode(' ',substr($text, $pos+11));
					$tags[] = 'CafeWagner';
					$tags[] = 'Jena';
					return $tags;
				}
			}
		}
		return array('CafeWagner','Jena');
	}

	private static function read_links($xml,$source_url){
		$articles = $xml->getElementsByTagName('article');
		$url = url::create($source_url,loc('event page'));	
		$links = array($url,);
		foreach ($articles as $article){			
			$anchors = $article->getElementsByTagName('a');
			foreach ($anchors as $anchor){
				if ($anchor->hasAttribute('href')){
					$address = $anchor->getAttribute('href');
					if (strpos(guess_mime_type($address),'image')===false){
						$links[] = url::create($address,trim($anchor->nodeValue));
					}
				}
			}
		}
		return $links;
	}

	private static function read_images($xml){
		$articles = $xml->getElementsByTagName('article');
		$attachments = array();
		foreach ($articles as $article){
			$images = $article->getElementsByTagName('img');
			foreach ($images as $image){
				$address = $image->getAttribute('src');
				$mime = guess_mime_type($address);
				$attachments[] = url::create($address,$mime);
			}
		}
		return $attachments;
	}



	private static function date($text){
		global $db_time_format;
		$date=extract_date($text);
		$time=extract_time($text);
		$datestring=date_parse($date.' '.$time);
		$secs=parseDateTime($datestring);
		return date($db_time_format,$secs);
	}
}