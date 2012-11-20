<?php 

/**
* 
*/
class EmplexerVideoList extends AbstractPreloadedRegularScreen implements UserInputHandler
{
	const ID = "emplexer_video_list";
	static $type;

	function __construct($id=null,$folder_views=null)
	{
		if (!is_null($id) && !is_null($folder_views) ){
			hd_print('parent' . print_r($folder_views, true));
			parent::__construct($id, $folder_views);
		}else {
			hd_print('local');
			parent::__construct(self::ID, $this->get_folder_views());
		}
		
	}


	public function get_handler_id(){
		return self::ID;
	}

    public function handle_user_input(&$user_input, &$plugin_cookies){
    	if ($user_input->control_id == 'play'){
    		hd_print($user_input);
    		$media_url = MediaURL::decode($user_input);
    		return ActionFactory::dvd_play('nfs://192.168.2.9:/volume1/Filmes/LAST KING OF SCOTLAND/VIDEO_TS');
    	}
    	return null;
    }

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		$play_action = UserInputHandlerRegistry::create_action($this, 'play');

		return array
		(
			GUI_EVENT_KEY_ENTER => $play_action,
			GUI_EVENT_KEY_PLAY => $play_action,
			);
	}

	public static function get_media_url_str($key, $filter_name =null, $type='show')
	{
		hd_print("key: $key" );
		
		self::$type = $type;

		return MediaURL::encode(
			array
			(
				'screen_id'      => self::ID,
				'key'  			 => $key,
				'filter_name'  	 => $filter_name,
				'type'			 => $type
				)
			);
	}

	public function get_all_folder_items(MediaURL $media_url , &$plugin_cookies){
		if (is_null ($media_url->filter_name)){
			$doc = HD::http_get_document(EmplexerConfig::DEFAULT_PLEX. $media_url->key );
		} else {
			$doc = HD::http_get_document( EmplexerConfig::DEFAULT_PLEX . '/library/sections/'. $media_url->key . '/' . $media_url->filter_name);
		}

		// $this->folder_views = $this->get_folder_views();
		hd_print("folder_views = " . print_r($this->folder_views));

		//hd_print($doc);

		$xml = simplexml_load_string($doc);

		// hd_print('get_media_url_str = '.  print_r($xml, true));

		$items = array();
		$bgImage = EmplexerConfig::DEFAULT_PLEX .  $xml->attributes()->art;


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

			// hd_print(print_r($xml, true));
			foreach ($xml->Video as $c)
			{
				$thumb = EmplexerConfig::DEFAULT_PLEX . '/photo/:/transcode?width=340&height=480&url=' . urlencode(EmplexerConfig::DEFAULT_PLEX . (string)$c->attributes()->thumb);
				// $thumb = EmplexerConfig::DEFAULT_PLEX . (string)$c->attributes()->thumb;
				$detailPhoto = EmplexerConfig::DEFAULT_PLEX . (string)$c->attributes()->thumb;
				$httpVidelUrl = EmplexerConfig::DEFAULT_PLEX . (string)$c->Media->Part->attributes()->key;
				$nfsVideoUrl = 'nfs://192.168.2.9:' . (string)$c->Media->Part->attributes()->file; 

				$v = EmplexerConfig::USE_NFS ? $nfsVideoUrl : $httpVidelUrl;

				$cacheKey = (string)$c->attributes()->ratingKey. '.jpg';				
				
				EmplexerArchive::getInstance()->setFileToArchive($cacheKey, $thumb );
				// EmplexerArchive::getInstance()->setFileToArchive($detailPhotoCacheKey, $detailPhoto );


				$media = MediaURL::encode(
					array(
							'movie_id'=>(string)$c->attributes()->index, 
							'video_url' => $v,
							'viewOffset' => (string)$c->attributes()->viewOffset,
							'duration' => (string)$c->Media->attributes()->duration,
							'summary' => (string)$c->attributes()->summary,
							'name' => (string)$c->attributes()->title,
							'thumb' => EmplexerConfig::DEFAULT_PLEX . (string)$c->attributes()->thumb,
							'title' => (string)$xml->attributes()->title1,
							'key' =>  (string) $c->attributes()->ratingKey
						)
				);

				$items[] = array
				(
					PluginRegularFolderItem::media_url => $media ,
					PluginRegularFolderItem::caption => (string) $c->attributes()->title,
					PluginRegularFolderItem::view_item_params =>
					array
					(
						ViewItemParams::icon_path => EmplexerArchive::getInstance()->getFileFromArchive($cacheKey, $thumb),
						ViewItemParams::item_detailed_icon_path => EmplexerArchive::getInstance()->getFileFromArchive($cacheKey, $thumb)
						// ViewItemParams::icon_path => $thumb,
						// ViewItemParams::item_detailed_icon_path => $detailPhoto
						)
					);
			}
			// hd_print(print_r($items, true));
			return $items;
		}


		public  function get_folder_views()
		{
			return EmplexerConfig::GET_EPISODES_LIST_VIEW();
		}


	}
	?>