<?php 



/**
* classe generica para uso de channels do Plex.
*/
class EmplexerBaseChannel extends AbstractPreloadedRegularScreen implements UserInputHandler
{
	const ID ='emplexer_base_channel';

	private $channel_type;
	function __construct(String $channel_type)
	{
		parent::__construct(self::ID, $this->get_folder_views());
		$channel_type = $this->channel_type;
		
	}



	public function get_handler_id(){
		return self::ID;
	}

	public function handle_user_input(&$user_input, &$plugin_cookies){
		hd_print(__METHOD__ . '   teste :' .  print_r($user_input, true));
		$media_url = MediaURL::decode($user_input->selected_media_url);
    	// hd_print(print_r($media_url, true));
		HD::print_backtrace();
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);	

		if ($user_input->control_id == 'enter'){

			hd_print("-----Entrou porra ------- type =" . print_r($media_url, true));
			if ($media_url->type == TYPE_DIRECTORY ){
				hd_print("entrei no if de dirctory media_url->type=" . $media_url->type);
				return ActionFactory::open_folder($user_input->selected_media_url);	
			} else  if ($media_url->type == TYPE_VIDEO){
				hd_print("----------entrei em else de video---------- type =" .  $media_url->type  . ' video_media_array = ' . print_r($media_url->video_media_array, true));
				$pop_up_items = array();
				foreach ($media_url->video_media_array as $m) {
					$name = $m->width . 'x' . $m->height;
					if ($m->bitrate){
						$name .= '-' . $m->bitrate;
					}
					$params ['indirect'] = $m->indirect;
					$params ['url'] = $m->container == 'mp4' ? str_replace('http://', 'http://mp4://', $m->url) : $m->url ;
					$params ['title'] = $m->title;
					$params ['summary'] =  $m->summary;
					$params ['thumb'] = $m->thumb;
					$params ['container'] = $m->container;
	    			//$params ['selected_media_url'] = $user_input->parent_media_url;

					$pop_up_items[] = array(
						GuiMenuItemDef::caption=> $name ,
						GuiMenuItemDef::action =>  UserInputHandlerRegistry::create_action($this, 'play', $params)
						);

				}

	    		// hd_print('show popup =' . print_r($pop_up_items, true));
				if (count($pop_up_items) > 0){	    			
					return ActionFactory::show_popup_menu($pop_up_items);
				}
			} else if ($media_url->type ==  TYPE_TRACK){
				hd_print("----------entrei em else de track---------- type =" .  $media_url->type  . ' video_media_array = ' . print_r($media_url->video_media_array, true));
				return ActionFactory::launch_media_url($media_url->video_media_array->key, $media_url->video_media_array->title);
			}  else if ($media_url->type == TYPE_CONF){

				hd_print("----------entrei em else de conf---------- type =" .  $media_url->type  . ' video_media_array = ' . print_r($media_url->video_media_array, true));

				return $this->showPrefScreen($media_url->key, $plugin_cookies);
			} else if ($media_url->type == TYPE_SEARCH){
				hd_print("----------entrei em else de search---------- type =" .  $media_url->type  . ' video_media_array = ' . print_r($media_url->video_media_array, true));
				return $this->showSearchScreen($media_url->key , 'teste', $plugin_cookies);
			} 

		}
		else if ($user_input->control_id == 'play') 	{
			$url = $user_input->url;
			if ($user_input->indirect){
				$doc = HD::http_get_document( str_replace('http://mp4://', 'http://', $user_input->url));
				$xml = HD::parse_xml_document($doc);
				$url = $xml->Video->Media->Part->attributes()->key;
				$user_input->url = $user_input->container == 'mp4' ? str_replace('http://','http://mp4://', $url) : $url;
			}

			$params['selected_media_url'] = $user_input->selected_media_url;
			$series_array = array();
			$series_array[] = array(
				PluginVodSeriesInfo::name => $user_input->title,
				PluginVodSeriesInfo::playback_url => $user_input->url,
				PluginVodSeriesInfo::playback_url_is_stream_url => true,
				);

			$toBeReturned = array(
				PluginVodInfo::id => 1,
				PluginVodInfo::series => $series_array,
				PluginVodInfo::name =>  $user_input->title,
				PluginVodInfo::description => $user_input->summary,
				PluginVodInfo::poster_url => $user_input->thumb,
				PluginVodInfo::initial_series_ndx => 0,
				PluginVodInfo::buffering_ms => 3000,
				// PluginVodInfo::initial_position_ms =>$media_url->viewOffset,
				PluginVodInfo::advert_mode => false,
				// PluginVodInfo::timer =>  array(GuiTimerDef::delay_ms => 5000),
				PluginVodInfo::actions => array(
					GUI_EVENT_PLAYBACK_STOP => UserInputHandlerRegistry::create_action($this, 'enter', $params),
					)
				);
			return ActionFactory::vod_play_with_vod_info($toBeReturned);
		} else if ($user_input->control_id == 'stop') {

			$media_url =  MediaURL::decode($user_input->selected_media_url);
			EmplexerFifoController::getInstance()->killPlexNotify();
			$action =  ActionFactory::invalidate_folders(
				array(
					$media_url,
					)
				);
			return $action;
		} else if ($user_input->control_id == 'savePrefs'){
			$url = "$base_url/:/plugins/".$user_input->identifier . "/prefs/set?";
			$notValidKeys =  array('identifier', 'handler_id', 'control_id', 'selected_control_id', 'parent_media_url', 'selected_media_url', 'sel_ndx');
			foreach ($user_input as $key => $value) {
				if (!in_array($key, $notValidKeys)){
					$url .= "&". $key . '=' . $value;
				}
			}
			$url =  str_replace('?&', '?', $url);
			hd_print("url = $url");
			HD::http_get_document($url);

		} else if ( $user_input->control_id ==  'search') {
			$url = $user_input->key . '&query=' . urlencode($user_input->query);
			
			hd_print("search url =$url");
			return ActionFactory::open_folder( $this->get_media_url_str($url, TYPE_DIRECTORY));
		}

	}
	


	public static function get_media_url_str($key, $type=TYPE_DIRECTORY,$videoMediaArray=null)
	{

		return MediaURL::encode(
			array
			(
				'screen_id'         => self::ID,
				'key'               => $key,
				'type'          	=> $type,
				'video_media_array' => $videoMediaArray
				)
			);
	}	

	public function get_action_map(MediaURL $media_url, &$plugin_cookies)
	{
		UserInputHandlerRegistry::get_instance()->register_handler($this);
		$enter_action = UserInputHandlerRegistry::create_action($this, 'enter');
		return array
		(
			GUI_EVENT_KEY_ENTER => $enter_action,
			GUI_EVENT_KEY_PLAY  => $enter_action,
			);
	}


	public function get_all_folder_items(MediaURL $media_url, &$plugin_cookies)
	{
		hd_print(__METHOD__ . ':' . print_r( $media_url, true  ));
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);	
		HD::print_backtrace();
		// hd_print("\n\n\n\nbase_url=$base_url\n\n\n\n");	
		$doc_url =  HD::is_url($media_url->key) ? $media_url->key : $base_url . (HD::starts_with($media_url->key , '/' ) ? $media_url->key : '/'. $media_url->key);
		hd_print("key = " . $media_url->key . ", base_url = $base_url");
		//TODO arrumar essa gambiarra. o script precisa saber se começa com // ou não.
		// $doc_url = str_replace('32400//', '32400/', $doc_url);
		$doc      = HD::http_get_document( $doc_url );	
		$xml      = HD::parse_xml_document($doc);

		$items    = array();

		// hd_print(__METHOD__ . ':' . print_r( $xml, true ));



		foreach ($xml->Video as $c) {	
			$thumb    = (string)$c->attributes()->thumb;
			// $thumb = $base_url . '/photo/:/transcode?width=340&height=480&url=' . urlencode($base_url . (string)$c->attributes()->thumb);
			// $key   = (string)$c->attributes()->key;	
			$title    = (string)$c->attributes()->title;
			$summary  = "" ; //(string)$c->attributes()->summary;
			$key      = (string)$c->attributes()->key;
			$videoMediaArray =  array();
			hd_print("key = $key , base_url = $base_url thumb = $thumb");


			foreach ($c->Media as $m) {
				hd_print('entrou em media');
				$container = (string)$m->attributes()->container;
				$height = (string)$m->attributes()->height;
				$width = (string)$m->attributes()->width;
				$audioCodec = (string)$m->attributes()->audioCodec;

				$videoCodec = (string)$m->attributes()->videoCodec;
				$videoResolution = (string)$m->attributes()->videoResolution;
				$bitrate = (string)$m->attributes()->bitrate;
				$indirect = (string)$m->attributes()->indirect;
				$key_url = $base_url . (string)$m->Part->attributes()->key;
				$key = $key_url;

				if ($container != "flv"){
					//dune não tem suporte para flv
					//$base_url . $thumb;
					$thumb = strpos($thumb, $base_url) === false ? $base_url . $thumb : $thumb;
					hd_print("thumb = $thumb");
					$videoMediaArray[] = array
					(
						'container' => $container,
						'height' => $height,
						'width' => $width,
						'audioCodec' => $audioCodec,
						'videoCodec' => $videoCodec,
						'videoResolution' => $videoResolution,
						'bitrate'=>$bitrate,
						'indirect' => $indirect ? true : false,
						'summary' => $summary,
						'title' => $title,
						'thumb' => $thumb,

						'url' => $key
						);	
				}
			}

			if (count($videoMediaArray) <=0){
				$videoMediaArray[] = array
				(
					'container' => strpos($key, ".mp4") ?  true : false,
					'height' => '?',
					'width' => '?',
					'audioCodec' => '?' ,
					'videoCodec' => '?',
					'videoResolution' => '?',
					'bitrate'=>'?',
					'indirect' =>  false,
					'summary' => $summary,
					'title' => $title,
					'thumb' => $thumb,

					'url' => $key
					);		
			}


			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $this->get_media_url_str($key, TYPE_VIDEO, $videoMediaArray) ,
				PluginRegularFolderItem::caption          => "$title",
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path               => $thumb, // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url),
					ViewItemParams::item_detailed_icon_path => $thumb // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url)
					)
				);	
		}


		foreach ($xml->Track as $t) {	

			$title   = (string)$t->attributes()->title;
			$title   = $title ? $title : 	(string)$t->attributes()->track;
			$thumb   = (string)$t->attributes()->thumb;
			$thumb   = HD ::is_url($thumb) ? $thumb : $base_url . $thumb; 
			$key     =  $t->Media ? $base_url . (string)$t->Media->Part->attributes()->key : (string)$t->attributes()->key ;
			$summary = (string)$t->attributes()->summary;
			
			$params = array();
			$params['title']   = $title;
			$params['thumb']   = $thumb;
			$params['key']     = $key;
			// $params['summary'] = $cairo_ps_surface_dsc_comment(surface, comment)ary;

			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $this->get_media_url_str($key, TYPE_TRACK, $params),
				PluginRegularFolderItem::caption          => "$title",
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path               => $thumb, 
					ViewItemParams::item_detailed_icon_path => $thumb 
					)
				);	

		}

		
		foreach ($xml->Directory as $c) {
			$thumb           = (string)$c->attributes()->thumb;
			// $thumb 			 = $base_url . '/photo/:/transcode?width=340&height=480&url=' . urlencode($base_url . (string)$c->attributes()->thumb);
			//caso no xml corrente não tenha contenttype significa que é raiz e ai o key é somente o nome do channel EX: youtube.
			//dessa forma é preciso concatenar o key corrente com o anterior.
			hd_print('media_url->key =' . $media_url->key . ' c->attributes()->key = ' . (string)$c->attributes()->key );
			// if (!strpos((string)$c->attributes()->key , $media_url->key)){
			if (!HD::starts_with((string)$c->attributes()->key, '/')){
				hd_print((string)$c->attributes()->key . ' não começa com /');
				$key         = $media_url->key   . '/' . (string)$c->attributes()->key;	
			} else {
				hd_print((string)$c->attributes()->key . ' começa com /');
				$key         = (string)$c->attributes()->key;	
			}
			
			$title           = (string)$c->attributes()->title;
			$summary	     = (string)$c->attributes()->summary;
			$url             = $base_url . $key;
			$cache_file_name = "channel_$title.jpg";
			$settings        = (string)$c->attributes()->settings;
			$search          = (string)$c->attributes()->search;
			$prompt          = (string)$c->attributes()->prompt;

			$type = !$settings ? TYPE_DIRECTORY : TYPE_CONF;
			$type = !$search ? $type : TYPE_SEARCH;



			hd_print("base_url=$base_url, title=$title, summary=$summary, url=$url, cache_file_name=$cache_file_name, key=$key");

			if ($thumb){
				//cacheia o thum do channel
				// EmplexerArchive::getInstance()->setFileToArchive($cache_file_name, $base_url . $thumb );	
			}

			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $this->get_media_url_str($key, $type),
				PluginRegularFolderItem::caption          => $title,
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path               => $base_url . $thumb, // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url),
					ViewItemParams::item_detailed_icon_path =>$base_url . $thumb // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url)
					)
				);
		}

		// hd_print(__METHOD__ . ':' . print_r( $items, true  ));
		return $items;
	}

	public function get_folder_views()
	{
		// return EmplexerConfig::GET_SECTIONS_LIST_VIEW();
		$a =EmplexerConfig::GET_EPISODES_LIST_VIEW();

		// hd_print(__METHOD__ . ':' . print_r($a, true) );
		return $a;
	}


	public function showPrefScreen($key, &$plugin_cookies){
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);
		$url = $base_url . $key;
		$xml = HD::getAndParseXmlFromUrl($url);
		$identifier = (string)$xml->attributes()->identifier;

		$defs = array();


		hd_print(__METHOD__ . ':' . print_r($xml, true));

		foreach ($xml as $setting) {
			$secture  = (bool)$setting->attributes()->secture;
			$default  = (string)$setting->attributes()->default;
			$value  = (string)$setting->attributes()->value;
			$label  = (string)$setting->attributes()->label;
			$values  = (string)$setting->attributes()->values;
			$type  = (string)$setting->attributes()->type;
			$option  = (string)$setting->attributes()->option;
			$id  = (string)$setting->attributes()->id;


			if ($type == "text"){
				ControlFactory::add_text_field(
					$defs, 
					null, 
					null,
					$name            = $id, 
					$title           = $label,  
					$initial_value   = $value,
					$numeric         = false, 
					$password        = ($option == 'hidden'), 
					$has_osk         = false, 
					$always_active   = 0, 
					$width           = 500
					);
			}

			if ($type == "bool"){
				ControlFactory::add_combobox(
					$defs,
					null,
					null,
					$name 					=  $id,
					$title 					=  $label,
					$initial_value			=  $value == "true" ? 0 : 1  ,
					$value_caption_pairs 	=  array('true', 'false'),
					$width					=  500,
					$need_confirm			=  false,
					$need_apply				=  false
					);
			}

			if ($type == 'enum'){

				$values = explode('|', $values);
				ControlFactory::add_combobox(
					$defs,
					null,
					null,
					$name 					=  $id,
					$title 					=  $label,
					$initial_value			=  $value,
					$value_caption_pairs 	=  $values,
					$width					=  500,
					$need_confirm			=  false,
					$need_apply				=  false
					);	
			}			
		}
		
		$params['identifier'] = $identifier;
		$savePrefsAction = UserInputHandlerRegistry::create_action($this, 'savePrefs', $params);

		ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
			'btnSavePrefs', 'save', 200, $savePrefsAction);

		$a =  ActionFactory::show_dialog('Prefs', $defs);

		hd_print(__METHOD__ . ':' . print_r($a, true));

		return $a;
	}


	public function showSearchScreen($key, $title, &$plugin_cookies){
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);
		$url = $base_url . $key;
		
		$defs = array();

		ControlFactory::add_text_field(
			$defs, 
			null, 
			null,
			$name            = 'query', 
			$title           =  $title,  
			$initial_value   = "",
			$numeric         = false, 
			$password        = false, 
			$has_osk         = false, 
			$always_active   = 0, 
			$width           = 500
			);

		$params['key'] = $key;
		$searchAction = UserInputHandlerRegistry::create_action($this, 'search', $params);		

		ControlFactory::add_custom_close_dialog_and_apply_buffon($defs,
			'search', 'Search', 100, $searchAction);

		$a = ActionFactory::show_dialog('Search' ,$defs);

		hd_print("search = " . print_r($a, true));
		return $a;
	}
}


?>