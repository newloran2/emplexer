<?php 



/**
* classe generica para uso de channels do Plex.
*/
class EmplexerBaseChannel extends AbstractPreloadedRegularScreen
{
	const ID ='emplexer_base_channel';

	private $channel_type;
	function __construct(String $channel_type)
	{
		hd_print(__METHOD__);
		parent::__construct(self::ID, $this->get_folder_views());
		$channel_type = $this->channel_type;
	}


	public static function get_media_url_str($key, $url=null)
	{

		return MediaURL::encode(
			array
			(
				'screen_id' => self::ID,
				'key'       => $key,
				'url'       => $url
			)
		);
	}

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		$enter_action = ActionFactory::open_folder();

		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY  => $enter_action,
		);
	}


	public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
	{
		// hd_print(__METHOD__ . ':' . print_r( $media_url, true  ));
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);	
		hd_print("\n\n\n\nbase_url=$base_url\n\n\n\n");	
		$doc_url =  $base_url . '/' . $media_url->key;
		//TODO arrumar essa gambiarra. o script precisa saber se começa com // ou não.
		$doc_url = str_replace('32400//', '32400/', $doc_url);
		$doc      = HD::http_get_document( $doc_url );	
		$xml      = HD::parse_xml_document($doc);
		
		$items    = array();

		hd_print(__METHOD__ . ':' . print_r( $xml, true ));
		
		foreach ($xml->Directory as $c) {
			$thumb           = (string)$c->attributes()->thumb;
			//caso no xml corrente não tenha contenttype significa que é raiz e ai o key é somente o nome do channel EX: youtube.
			//dessa forma é preciso concatenar o key corrente com o anterior.
			if (!(string)$xml->attributes()->contenttype){
				$key         = $media_url->key   . '/' . (string)$c->attributes()->key;	
			} else {
				$key         = (string)$c->attributes()->key;	
			}
			
			$title           = (string)$c->attributes()->title;
			$summary	     = (string)$c->attributes()->summary;
			$url             = $base_url . $key;
			$cache_file_name = "channel_$title.jpg";


			hd_print("base_url=$base_url, title=$title, summary=$summary, url=$url, cache_file_name=$cache_file_name, key=$key");

			if ($thumb){
				//cacheia o thum do channel
				// EmplexerArchive::getInstance()->setFileToArchive($cache_file_name, $base_url . $thumb );	
			}

			$items[] = array
			(
				PluginRegularFolderItem::media_url => $this->get_media_url_str($key),
				PluginRegularFolderItem::caption => $title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path => $base_url . $thumb, // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url),
					ViewItemParams::item_detailed_icon_path =>$base_url . $thumb // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url)
				)
			);
		}


		foreach ($xml->Video as $c) {	
			$thumb           = (string)$c->attributes()->thumb;
			$key         	 = (string)$c->attributes()->key;	
			
			$title           = (string)$c->attributes()->title;
			$summary	     = (string)$c->attributes()->summary;
			$url             = $base_url . $key;
			$cache_file_name = "channel_$title.jpg";


			hd_print("base_url=$base_url, title=$title, summary=$summary, url=$url, cache_file_name=$cache_file_name, key=$key");

			if ($thumb){
				//cacheia o thum do channel
				// EmplexerArchive::getInstance()->setFileToArchive($cache_file_name, $base_url . $thumb );	
			}

			$items[] = array
			(
				PluginRegularFolderItem::media_url => $this->get_media_url_str($key),
				PluginRegularFolderItem::caption => $title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path => $base_url . $thumb, // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url),
					ViewItemParams::item_detailed_icon_path =>$base_url . $thumb // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url)
				)
			);
		}



		hd_print(__METHOD__ . ':' . print_r( $items, true  ));
		return $items;
	}

	public function get_folder_views()
	{
		// return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
		return EmplexerConfig::GET_VIDEOS_LIST_VIEW();
	}
}


?>