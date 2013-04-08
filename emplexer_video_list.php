<?php 


/**
* 
*/
class EmplexerVideoList extends AbstractPreloadedRegularScreen implements UserInputHandler
{
	const ID = "emplexer_video_list";
	static $type;
	private $last_media_url;

	function __construct($id=null,$folder_views=null)
	{
		hd_print(__METHOD__);
		if (!is_null($id) && !is_null($folder_views) ){
			// hd_print('parent' . print_r($folder_views, true));
			parent::__construct($id, $folder_views);
		}else {			
			parent::__construct(self::ID, $this->get_folder_views());
		}
	}

	public function get_handler_id(){
		hd_print(__METHOD__);
		return self::ID;
	}

	public function handle_user_input(&$user_input, &$plugin_cookies){
		hd_print(__METHOD__);
		hd_print(print_r($user_input, true));
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);
		$media_url = MediaURL::decode($user_input->selected_media_url);
		if ($user_input->control_id == 'play'){			
			hd_print(__METHOD__ . ':' .  print_r($user_input, true));
			if (strpos($media_url->video_url, "VIDEO_TS.IFO")){
				$url = dirname($media_url->video_url);
				return   ActionFactory::dvd_play($url);
			} else if (strpos(strtolower($media_url->video_url), ".iso")){

				return ActionFactory::launch_media_url($media_url->video_url);   //ActionFactory::dvd_play($media_url->video_url);

			} else if (strpos(strtolower($media_url->video_url), "bdmv")){
				$pos=strpos(strtolower($media_url->video_url),'bdmv');
				$url =substr($media_url->video_url,0,$pos-1);
				hd_print('tentando tocar bluray com a url ' . $url );
				return  ActionFactory::bluray_play($url);
			}
			else if (strpos(strtolower($media_url->video_url), ".m2ts")){
				return ActionFactory::launch_media_url($media_url->video_url);
				
			} else {
				
				$url = $media_url->video_url;
				$key = $media_url->key;
				$startPosition = $media_url->viewOffset / 1000; 
				$plexFileId = $media_url->key;
				$timeToMark=DEFAULT_TIME_TO_MARK;
				$basePlexURL = $base_url;
				$pooling=5;
				$time_action = UserInputHandlerRegistry::create_action($this, 'time', null);
				// EmplexerFifoController::getInstance()->startPlexNotify($key, 5 , EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this).'/');
				// EmplexerFifoController::getInstance()->startDefaultPlayBack($url,$startPosition,$plexFileId,$timeToMark,$basePlexURL,$pooling);
				// EmplexerFifoController::getInstance()->startSetPlayBackPosition(200);
				// return ActionFactory::launch_media_url($media_url->video_url);
				return ActionFactory::vod_play();
			}
		} 

		if ($user_input->control_id == 'stop')
		{
			hd_print('ENTREI NO EVENTO STOP');
			$media_url =  $this->get_media_url_str($user_input->back_key, $user_input->back_filter_name);
			EmplexerFifoController::getInstance()->killPlexNotify();
			$action =  ActionFactory::invalidate_folders(
				array(
					$media_url,
					)
				);
			return $action;
		} 

		if ($user_input->control_id == 'time'){
			$key = $user_input->key;
			EmplexerFifoController::getInstance()->startPlexNotify($key, 5 , EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this).'/');
		}


		if ($user_input->control_id == 'info') {

			$extra = array('bgImage' => $media_url->art );
			return ActionFactory::open_folder(VodMovieScreen::get_media_url_str($media_url->detail_info_key, $extra));
		}

		if ($user_input->control_id == 'pop_up'){
			$key = (int) $media_url->key;
			$was_seen = $media_url->was_seen;
			$url = null;
			if ($was_seen){
				// http://192.168.2.9:32400/:/unscrobble?key=19547&identifier=com.plexapp.plugins.library
				$url= EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . "/:/unscrobble?key=$key&identifier=com.plexapp.plugins.library" ;
			} else {
				// http://192.168.2.9:32400/:/scrobble?key=19547&identifier=com.plexapp.plugins.library
				$url= EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . "/:/scrobble?key=$key&identifier=com.plexapp.plugins.library" ;
			}
			hd_print(__METHOD__ . 'url:' .$url );

			$params['url'] = $url;

			$pop_up_items[] = array(
				GuiMenuItemDef::caption=> $was_seen ? 'mark as unwatched' : 'mark as watched' ,
				// GuiMenuItemDef::caption=> 'mark as unread'  ,
				GuiMenuItemDef::action =>  UserInputHandlerRegistry::create_action($this, 'mark', $params)
				);

			hd_print(__METHOD__ . ' pop_up_items:' .print_r($pop_up_items, true) );		
			return ActionFactory::show_popup_menu($pop_up_items);
		}

		if ($user_input->control_id == 'mark'){
			hd_print('mark = '. print_r($user_input, true));
			
			$back_media_url =  MediaURL::decode($user_input->selected_media_url);
			$media_url = $this->get_media_url_str($media_url->back_key, $media_url->back_filter_name);

			HD::http_get_document($user_input->url);

			$action =  ActionFactory::invalidate_folders(
				array(
					$media_url,
					)
				);
			return $action;

		}

	}   

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		hd_print(__METHOD__);
		UserInputHandlerRegistry::get_instance()->register_handler($this);
		$play_action = UserInputHandlerRegistry::create_action($this, 'play');
		$info_action = UserInputHandlerRegistry::create_action($this, 'info');
		$pop_up_action = UserInputHandlerRegistry::create_action($this, 'pop_up', null,'Filtos');

		$a = array
		(
			GUI_EVENT_KEY_ENTER => $play_action,
			GUI_EVENT_KEY_PLAY => $play_action,
			GUI_EVENT_KEY_POPUP_MENU => $pop_up_action,
			GUI_EVENT_KEY_INFO => $info_action

			);

		// hd_print(print_r($a, true));
		return $a;
	}

	public static function get_media_url_str($key, $filter_name =null, $type='show')
	{
		hd_print(__METHOD__);
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
		hd_print(__METHOD__);
		hd_print(__METHOD__ . ': ' . print_r($media_url, true));
		hd_print(__METHOD__ . ': ' . print_r($plugin_cookies, true));

		$base_url =  EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);
		if (is_null ($media_url->filter_name)){
			$xml =    HD::getAndParseXmlFromUrl(EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this). $media_url->key );
		} else {
			$xml = HD::getAndParseXmlFromUrl( EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/'. $media_url->key . '/' . $media_url->filter_name);
		}
		$items = array();


		foreach ($xml->Video as $c)
		{
			$thumb        = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/photo/:/transcode?width=320&height=480&url=' . EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . (string)$c->attributes()->thumb;		
			$bgImage = $base_url .  (string)$c->attributes()->art;
			// if (EmplexerConfig::$USE_CACHE  === 'false'){
			// 	$thumb = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . (string)$c->attributes()->thumb;
			// }
			$detailPhoto  = $thumb;
			$httpVidelUrl = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . (string)$c->Media->Part->attributes()->key;
			$nfsVideoUrl  = 'nfs://' . $plugin_cookies->plexIp . ':' . (string)$c->Media->Part->attributes()->file; 
			if ($plugin_cookies->connectionMethod == 'smb'){
				$smbVideoUrl  = 'smb://' . $plugin_cookies->userName . ':' .  $plugin_cookies->password . '@' . $plugin_cookies->plexIp . '/' . (string)$c->Media->Part->attributes()->file;	
				$videoUrl[SMB_CONNECTION_TYPE]  = $smbVideoUrl;
			}
			
			$videoUrl[HTTP_CONNECTION_TYPE] = $httpVidelUrl;
			$videoUrl[NFS_CONNECTION_TYPE]  = $nfsVideoUrl;

			// $v = EmplexerConfig::USE_NFS ? $nfsVideoUrl : $httpVidelUrl;
			// $v = $videoUrl[$plugin_cookies->connectionMethod];
			
			if ($plugin_cookies->connectionMethod == HTTP_CONNECTION_TYPE) 
				$v = $httpVidelUrl;
			else
				$v = $this->getPlayBackUrl($plugin_cookies, (string)$c->Media->Part->attributes()->file, $plugin_cookies->connectionMethod);

			if (!$v){
				hd_print('connectionMethod not setted use http as default');
				$v = $httpVidelUrl;
			}

			hd_print("-----------videoUrl = $v-----------");

			$cacheKey = (string)$c->attributes()->ratingKey. '.jpg';				
			
			if ($c->attributes()->thumb){
				EmplexerArchive::getInstance()->setFileToArchive($cacheKey, $thumb );				
			}

			$media = MediaURL::encode(
				array(
					'movie_id'=>$v, 
					'video_url' => $v,
					'viewOffset' => (string)$c->attributes()->viewOffset,
					'duration' => (string)$c->Media->attributes()->duration,
					// 'summary' => utf8_decode((string)$c->attributes()->summary),
					'summary' => str_replace(array("\r\n", "\r", "\n",  "\""), " ", (string)$c->attributes()->summary)	,
					// 'summary' => (string)$c->attributes()->summary	,
					'name' => (string)$c->attributes()->title,
					'thumb' => $thumb,
					'art'   => $bgImage,
					'title' => (string)$xml->attributes()->title1,
					'key' =>  (string) $c->attributes()->ratingKey,
					'back_screen_id' => $media_url->screen_id,
					'back_key' => $media_url->key,
					'back_filter_name' => $media_url->filter_name,
					'was_seen' => $c->attributes()->viewCount ? true : false,
					'detail_info_key' =>(string)$c->attributes()->key
					)
				);

			$info = $this->getDetailedInfo($c);
			// 'Serie:' . (string)$c->attributes()->grandparentTitle . ' || ' .
			// 'Episode Name :' . (string)$c->attributes()->title. ' || ' .
			// 'EP:'  . 'S'.(string)$c->attributes()->parentIndex . 'E'. (string)$c->attributes()->index . '||' .
			// 'summary:'. str_replace('"', '' , (string)$c->attributes()->summary);





              // hd_print(print_r($media, true));


			$hasSeenCaptionColor = (!$plugin_cookies->hasSeenCaptionColor || $plugin_cookies->hasSeenCaptionColor == DEFAULT_HAS_SEEN_CAPTION_COLOR ) ? null : $plugin_cookies->hasSeenCaptionColor;
			$notSeenCaptionColor = (!$plugin_cookies->notSeenCaptionColor || $plugin_cookies->notSeenCaptionColor == DEFAULT_HAS_SEEN_CAPTION_COLOR ) ? null : $plugin_cookies->notSeenCaptionColor;
			$item_caption_color = $c->attributes()->viewCount ? $hasSeenCaptionColor :  $notSeenCaptionColor;

			$item_caption_color = (!$item_caption_color) ? $item_caption_color : $item_caption_color-1 ;

			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $media ,
				PluginRegularFolderItem::caption          => (string) $c->attributes()->title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path                 => EmplexerArchive::getInstance()->getFileFromArchive($cacheKey, $thumb),
					ViewItemParams::item_detailed_icon_path   => EmplexerArchive::getInstance()->getFileFromArchive($cacheKey, $thumb),
					ViewItemParams::item_detailed_info        => $info,
					ViewItemParams::item_caption_color        => $item_caption_color,
					// ViewItemParams::icon_dx                   =>  100,
					
					// ViewItemParams::icon_dy                   =>  100,
					// ViewItemParams::icon_sel_dx               =>  100,
					// ViewItemParams::icon_sel_dy               =>  100,					
					// ViewItemParams::item_caption_dx           =>  -200,
					// ViewItemParams::item_caption_dy           =>  500,
					// ViewItemParams::item_caption_sel_dy		  =>  0,
					// ViewItemParams::item_caption_wrap_enabled => true,
					// ViewItemParams::item_caption_width 		  => 500 


				)
			);
		}
		hd_print(print_r($items, true));
		return $items;
	}


	public  function get_folder_views()
	{
		hd_print(__METHOD__);
		return EmplexerConfig::GET_EPISODES_LIST_VIEW();
	}

	public function getDetailedInfo(SimpleXMLElement &$node){
		hd_print(__METHOD__);
		$info =
		'Serie:' . (string)$node->attributes()->grandparentTitle . ' || ' .
		'Episode Name :' . (string)$node->attributes()->title. ' || ' .
		'EP:'  . 'S'.(string)$node->attributes()->parentIndex . 'E'. (string)$node->attributes()->index . '||' .
		'summary:'. str_replace('"', '' , (string)$node->attributes()->summary);

		return $info;
	}


	public function getPlayBackUrl(&$plugin_cookies, $filePath, $type=HTTP_CONNECTION_TYPE){

		if ($type == HTTP_CONNECTION_TYPE) return $filePath;

		foreach ($plugin_cookies as $key => $value) {
			if (strpos($filePath, $key) !== false){
				return str_replace($key, $value, $filePath);
			}
		}
		// $nfsVideoUrl  = 'nfs://' . $plugin_cookies->plexIp . ':' . $filePath; 
	}


}
?>