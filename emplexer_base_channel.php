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

    	if ($user_input->control_id == 'enter'){

    		hd_print('-----Entrou porra -------');
	    	if (!$media_url->is_video){
		    	return ActionFactory::open_folder($user_input->selected_media_url);	
	    	} else {
	    		$pop_up_items = array();
	    		foreach ($media_url->video_media_array as $m) {
	    			$name = $m->width . 'x' . $m->height;
	    			if ($m->bitrate){
	    				$name .= '-' . $m->bitrate;
	    			}
	    			$params ['indirect'] = $m->indirect;
	    			$params ['url'] = $m->container == 'mp4' ? str_replace('http://', 'http://mp4://', $m->url) : $m->url ;
	    			$params ['title'] = $m->title;
	    			$params ['summary'] = $m->summary;
	    			$params ['thumb'] = $m->thumb;
	    			$params ['container'] = $m->container;
	    			$params ['selected_media_url'] = $user_input->parent_media_url;
	    			
	    			$pop_up_items[] = array(
						GuiMenuItemDef::caption=> $name ,
						GuiMenuItemDef::action =>  UserInputHandlerRegistry::create_action($this, 'play', $params)
					);
	 
	    		}

	    		
	    		if (count($pop_up_items) > 0){
	    			hd_print('show popup =' . print_r($pop_up_items, true));
					return ActionFactory::show_popup_menu($pop_up_items);
	    		}
	    	}   
	    } else if ($user_input->control_id == 'play') 	{
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
		} 

    }


	public static function get_media_url_str($key, $is_video=false,$videoMediaArray=null)
	{

		return MediaURL::encode(
			array
			(
				'screen_id'         => self::ID,
				'key'               => $key,
				'is_video'          => $is_video,
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
		// hd_print(__METHOD__ . ':' . print_r( $media_url, true  ));
		$base_url = EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this);	
		HD::print_backtrace();
		// hd_print("\n\n\n\nbase_url=$base_url\n\n\n\n");	
		$doc_url =  $base_url . '/' . $media_url->key;
		//TODO arrumar essa gambiarra. o script precisa saber se começa com // ou não.
		$doc_url = str_replace('32400//', '32400/', $doc_url);
		$doc      = HD::http_get_document( $doc_url );	
		$xml      = HD::parse_xml_document($doc);
		
		$items    = array();

		// hd_print(__METHOD__ . ':' . print_r( $xml, true ));



		foreach ($xml->Video as $c) {	
			$thumb    = (string)$c->attributes()->thumb;
			// $thumb = $base_url . '/photo/:/transcode?width=340&height=480&url=' . urlencode($base_url . (string)$c->attributes()->thumb);
			// $key   = (string)$c->attributes()->key;	
			$title    = (string)$c->attributes()->title;
			$summary  = (string)$c->attributes()->summary;
			$key      = (string)$c->attributes()->key;
			$videoMediaArray =  array();
		
			foreach ($c->Media as $m) {
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
				
/*				if ($container == "mp4" ){
					//caso o container seja mp4 uso o sintaxe otimizada para streaming.
					$key = str_replace('http://', 'http://mp4://', $key);
				}
*/
				if ($container != "flv"){
					//dune não tem suporte para flv
					//
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



			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $this->get_media_url_str($key, true, $videoMediaArray) ,
				PluginRegularFolderItem::caption          => "$title",
				PluginRegularFolderItem::view_item_params =>
				array
				(
					ViewItemParams::icon_path               => $base_url . $thumb, // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url),
					ViewItemParams::item_detailed_icon_path =>$base_url . $thumb // EmplexerArchive::getInstance()->getFileFromArchive($cache_file_name, $url)
				)
			);	

			


			
		}

		
		foreach ($xml->Directory as $c) {
			$thumb           = (string)$c->attributes()->thumb;
			// $thumb 			 = $base_url . '/photo/:/transcode?width=340&height=480&url=' . urlencode($base_url . (string)$c->attributes()->thumb);
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


			// hd_print("base_url=$base_url, title=$title, summary=$summary, url=$url, cache_file_name=$cache_file_name, key=$key");

			if ($thumb){
				//cacheia o thum do channel
				// EmplexerArchive::getInstance()->setFileToArchive($cache_file_name, $base_url . $thumb );	
			}

			$items[] = array
			(
				PluginRegularFolderItem::media_url        => $this->get_media_url_str($key, false),
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
}


?>