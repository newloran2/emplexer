<?php 

/**
* 
*/
class EmplexerSeasonList extends AbstractPreloadedRegularScreen implements UserInputHandler
{
	const ID = "emplexer_season_list"; 

	// private $archive;

	function __construct()
	{
		hd_print(__METHOD__);
		parent::__construct(self::ID, $this->get_folder_views());	
		UserInputHandlerRegistry::get_instance()->register_handler($this);

	}

	public static function get_media_url_str($key, $filter_name=null)
	{
		hd_print(__METHOD__);
		return MediaURL::encode(
			array
			(
				'screen_id'   	=> self::ID,
				'key'  			=> $key,
				'filter_name'   => $filter_name
				)
			);
	}


	public function get_handler_id(){
		hd_print(__METHOD__);
		return self::ID;
	}

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		hd_print(__METHOD__);
		$enter_action = ActionFactory::open_folder();
		$pop_up_action = UserInputHandlerRegistry::create_action($this, 'pop_up', null,'Filtos');


		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY => $enter_action,
			GUI_EVENT_KEY_POPUP_MENU => $pop_up_action,
			);
	}


	public function handle_user_input(&$user_input, &$plugin_cookies){
		hd_print(__METHOD__);
		hd_print(print_r($user_input, true));		
		$media_url = MediaURL::decode($user_input->selected_media_url);
		
		if ($user_input->control_id == 'pop_up'){

		}
	}

	public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
		hd_print(__METHOD__);
		// hd_print (__METHOD__ . ':' . print_r($media_url, true));
		if (!isset($media_url->filter_name)){
			$doc = HD::http_get_document(EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . $media_url->key );			
		} else {
			$doc = HD::http_get_document(EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/' .  $media_url->key . '/' . $$media_url->filter_name );			
		}

		// $doc = HD::http_get_document(EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . $media_url->key );

		$xml = simplexml_load_string($doc);

		
		$items = array();
		


		/*

			TODO adicionar backgound com o art vindo do plex
		$bg = array(
			ViewParams::background_order =>'before_all',
			ViewParams::background_path  => $bgImage
		);
		
		$viewParams = PluginRegularFolderItem::view_params;
		array_push($view_params, $bg );
		PluginRegularFolderItem::view_params => $viewParams;
		*/

		foreach ($xml->Directory as $c)
		{
			hd_print(__METHOD__);
			// hd_print(__METHOD__ . ':' .  print_r($xml, true));
			$thumb = (string)$c->attributes()->thumb ? (string)$c->attributes()->thumb : (string)$xml->attributes()->thumb;
			$url =  EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) .'/photo/:/transcode?width=340&height=480&url=' . urlencode( EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . $thumb);
			if (EmplexerConfig::$USE_CACHE === 'false'){
				$url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . $thumb;
			}
			// $url =  EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) .'/photo/:/transcode?width=150&height=222&url=' . EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . (string)$c->attributes()->thumb;
			$urlb = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . (string)$c->attributes()->thumb;
			$bgImage = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) .  $c->attributes()->art;			
			$ratingKey = $c->attributes()->ratingKey ? (string) $c->attributes()->ratingKey : (string)$xml->attributes()->key;
			$caption = $ratingKey . '.jpg';
			
			if ($thumb){
				EmplexerArchive::getInstance()->setFileToArchive($caption, $url );			
			}
				

			$remainingEpisodes = (((int)$c->attributes()->leafCount) - ((int)$c->attributes()->viewedLeafCount ));
			$items[] = array
			(
				ViewParams::background_order => 'before_all',
				ViewParams::background_path  => $bgImage,
				PluginRegularFolderItem::media_url => EmplexerVideoList::get_media_url_str((string)$c->attributes()->key),
				PluginRegularFolderItem::caption => (string) $c->attributes()->title . ' (' .  (string) $remainingEpisodes . ')' ,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					// ViewItemParams::icon_path => $url,
					ViewItemParams::icon_path =>  EmplexerArchive::getInstance()->getFileFromArchive($caption, $url),
					// ViewItemParams::item_detailed_icon_path => $url,
					ViewItemParams::item_detailed_icon_path => EmplexerArchive::getInstance()->getFileFromArchive($caption, $url),
					ViewItemParams::item_caption_wrap_enabled => false,
				)
			);
		}

		// hd_print(print_r($items, true));
		return $items;

	}

	public function get_archive(MediaURL $media_url){
		hd_print(__METHOD__);
		// return $this->archive;
		return null;
	}
	protected function setBg(){
		
	}


	public function get_folder_views()
	{
		hd_print(__METHOD__);
		// return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
		return EmplexerConfig::GET_VIDEOS_LIST_VIEW();
	}



}

?>