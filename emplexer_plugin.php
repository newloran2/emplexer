<?php


///////////////////////////////////////////////////////////////////////////

require_once 'lib/default_dune_plugin.php';
require_once 'lib/vod/vod_list_screen.php';
require_once 'lib/vod/vod_series_list_screen.php';
require_once 'lib/vod/abstract_vod.php';
require_once 'lib/vod/movie.php';
require_once 'lib/user_input_handler.php';
require_once 'lib/default_archive.php';
require_once 'lib/utils.php';


require_once 'emplexer_vod.php';
require_once 'emplexer_setup_screen.php';
require_once 'emplexer_config.php';
require_once 'emplexer_section_screen.php';
require_once 'emplexer_root_list.php';
require_once 'emplexer_season_list.php';
require_once 'emplexer_video_list.php';
require_once 'emplexer_movie_list.php';
require_once 'emplexer_archive.php';
require_once 'emplexer_fifo_controller.php';


class Emplexer extends DefaultDunePlugin implements UserInputHandler
{
	
	function __construct()
	{

		$this->vod = new EmplexerVod();
		$this->add_screen(new EmplexerSetupScreen());
		$this->add_screen(new EmplexerSectionScreen());
		$this->add_screen(new EmplexerRootList());
		$this->add_screen(new EmplexerSeasonList());
		$this->add_screen(new EmplexerVideoList());
		$this->add_screen(new EmplexerMovieList());


		EmplexerFifoController::getInstance(); // inicia o fifo
	}

	public function get_handler_id(){
		return 'plex_root';
	}

	public function get_vod_info($media_url_str, &$plugin_cookies){
		hd_print(__METHOD__ . ': ' . print_r($media_url_str, true) );
		
		$media_url = MediaURL::decode($media_url_str);

		$params = array('key' => $media_url->key);
		$stop_action = UserInputHandlerRegistry::create_action($this, 'stop', $params);
		$time_action = UserInputHandlerRegistry::create_action($this, 'time', $params);

		
		
		$series_array = array();
		$series_array[] = array(
			PluginVodSeriesInfo::name => $media_url->title,
			PluginVodSeriesInfo::playback_url => $media_url->video_url,
			PluginVodSeriesInfo::playback_url_is_stream_url => true,
			);

		$toBeReturned = array(
			PluginVodInfo::id => $media_url->movie_id,
			PluginVodInfo::series => $series_array,
			PluginVodInfo::name =>  $media_url->title,
			PluginVodInfo::description => $media_url->summary,
			PluginVodInfo::poster_url => $media_url->thumb,
			PluginVodInfo::initial_series_ndx => 0,
			PluginVodInfo::buffering_ms => 3000,
			PluginVodInfo::initial_position_ms =>$media_url->viewOffset,
			PluginVodInfo::advert_mode => false,
			PluginVodInfo::timer =>  array(GuiTimerDef::delay_ms => 5000),
			PluginVodInfo::actions => array(
				GUI_EVENT_PLAYBACK_STOP => $stop_action,
				GUI_EVENT_TIMER => $time_action
				)
			

			);

		hd_print(print_r($toBeReturned, true));
		return $toBeReturned;
	}

	public function handle_user_input(&$user_input, &$plugin_cookies)
	{
		hd_print( __METHOD__ . ' handle_user_input:');
		foreach ($user_input as $key => $value)
			hd_print("$key => $value");
		
		$plugin_dir = dirname(__FILE__);

		if ($user_input->control_id == 'stop')
		{
			EmplexerFifoController::getInstance()->killPlexNotify();
		}

		if ($user_input->control_id == 'time'){
			$key = $user_input->key;
			EmplexerFifoController::getInstance()->startPlexNotify($key, 5 , EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this).'/');
		}

		if ($user_input->control_id == 'pop_up') {
			hd_print("pop_up = $user_input" );
			$media_url = MediaURL::decode($user_input->selected_media_url);

			$key = (string) $media_url->category_id;

			$doc = HD::http_get_document( EmplexerConfig::getPlexBaseUrl($plugin_cookies, $this) . '/library/sections/' . $key);
			$pop_up_items =  array();
			$xml = simplexml_load_string($doc);
			foreach ($xml->Directory as $c){
				$key = (string)$c->attributes()->key;
				$prompt = (string)$c->attributes()->prompt;
				if ($key != 'all' &&  $key != 'folder' && !$prompt ){
					$pop_up_items[] = array(
						GuiMenuItemDef::caption=> (string)$c->attributes()->title,
						GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
					);
				}
			}

			$action = ActionFactory::show_popup_menu($pop_up_items);	
			hd_print(__METHOD__ . ': ' . print_r($action, true));
			return $action;
			
		}
		if ($user_input->control_id == 'play'){
    		hd_print('playAction');
    		$media_url = MediaURL::decode($user_input->selected_media_url);
    		if (strpos($media_url->video_url, "VIDEO_TS.IFO")){
				$url = dirname($media_url->video_url);
				hd_print("URL=$url");
				return ActionFactory::dvd_play($url);
			}else {
				return ActionFactory::vod_play();
			}

    	}


    	if ($user_input->control_id == 'btnSalvar'){
    		hd_print("botão salvar = $plugin_cookies" );
    		$plugin_cookies->plexIp    = $user_input->plexIp;
			$plugin_cookies->plexPort  = $user_input->plexPort;
			$plugin_cookies->username = $user_input->plexPort;
			hd_print(__METHOD__  . ': ' .  print_r($plugin_cookies, true));
			return ActionFactory::show_title_dialog('Configuration successfully saved.');
			
    	}

		return null;
	}
	
	private function get_right_media_url(MediaURL $media_url,$filter_name)
	{
		$episodes = array( 'newest' , 'recentlyAdded', 'recentlyViewed', 'onDeck');
		$season = array('all','recentlyViewedShows','unwatched');
		
		if (in_array($filter_name , $episodes)){			
			hd_print( __METHOD__ . ':episode ');
			return EmplexerVideoList::get_media_url_str($media_url->category_id, $filter_name);
		} else {
			hd_print( __METHOD__ . ':season');
			// return EmplexerSeasonList::get_media_url_str($media_url->category_id, $filter_name);
			return EmplexerRootList::get_media_url_str($media_url->category_id, $filter_name);
		}
	}

}


?>
