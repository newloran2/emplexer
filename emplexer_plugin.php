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
require_once 'emplexer_popup.php';
require_once 'emplexer_movie_description_screen.php';
require_once 'emplexer_base_channel.php';
require_once 'emplexer_list_video.php';

require_once 'lib/vod/vod_movie_screen.php';

//implements UserInputHandler
class Emplexer extends DefaultDunePlugin 
{
	
	function __construct()
	{
		hd_print(__METHOD__);
		if (EmplexerConfig::CREATE_LOG_FOLDER){
			if (file_exists('/D') && is_dir('/D')){
				if (!file_exists('/D/dune_plugin_logs/')){
					mkdir('/D/dune_plugin_logs/');
					hd_print('log_dir created');	
				}
			}
		}

		

		$this->vod = new EmplexerVod();
		$this->add_screen(new EmplexerSetupScreen());
		$this->add_screen(new EmplexerSectionScreen());
		$this->add_screen(new EmplexerRootList());
		$this->add_screen(new EmplexerSeasonList());
		$this->add_screen(new EmplexerVideoList());
		$this->add_screen(new EmplexerMovieList());
		// $this->add_screen(new EmplexerMovieDescriptionScreen());
		$this->add_screen(new VodMovieScreen($this->vod))	;
		$this->add_screen(new EmplexerBaseChannel());
		$this->add_screen(new EmplexerListVideo());

		EmplexerFifoController::getInstance(); // inicia o fifo
	}

	public function get_handler_id(){
		hd_print(__METHOD__);
		return 'plex_root';
	}

	public function get_vod_info($media_url_str, &$plugin_cookies){
		hd_print(__METHOD__);
		hd_print(__METHOD__ . ': ' . print_r($media_url_str, true) );
		hd_print(__METHOD__ . ': ' . print_r($plugin_cookies, true) );
		// hd_print(print_r(debug_backtrace(), true));
		HD::print_backtrace();


		$media_url = MediaURL::decode($media_url_str);


		if ($media_url->screen_id == 'emplexer_base_channel'){
				$toPlay = $media_url->video_media_array[$plugin_cookies->channel_selected_index];
			return EmplexerBaseChannel::get_vod_info($toPlay);
		}

		if ($media_url->screen_id == 'vod_movie'){
			
			return $this->vod->get_vod_info($media_url, $plugin_cookies);
			// EmplexerVod::get_vod_info($media_url, $plugin_cookies);
		}

		$handler = $media_url->back_screen_id == EmplexerVideoList::ID ? EmplexerVideoList::ID : EmplexerMovieList::ID;


		$params = array('key' => $media_url->key, 'back_screen_id' => $media_url->back_screen_id , 'back_key' => $media_url->back_key, 'back_filter_name' => $media_url->back_filter_name);
		$stop_action = UserInputHandlerRegistry::create_action($this->get_screen_by_id($handler) , 'stop', $params);
		$time_action = UserInputHandlerRegistry::create_action($this->get_screen_by_id($handler), 'time', $params);

		
		
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

	private function get_right_media_url(MediaURL $media_url,$filter_name)
	{
		hd_print(__METHOD__);
		$episodes = array( 'newest' , 'recentlyAdded', 'recentlyViewed', 'onDeck');
		$season = array('all','recentlyViewedShows','unwatched');
		
		if (in_array($filter_name , $episodes)){			
			return EmplexerVideoList::get_media_url_str($media_url->category_id, $filter_name);
		} else {
			return EmplexerRootList::get_media_url_str($media_url->category_id, $filter_name);
		}
	}

	private function open_popup_for_filter($current_url, $key){
		hd_print(__METHOD__);
		$key = (string) $media_url->category_id;

		$doc = HD::http_get_document( $current_url . '/' . $key);
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
	}
	private function is_filfer($filter_name){
		hd_print(__METHOD__);
		$filter = array('firstCharacter','genre','year','contentRating','folder');
		return in_array($filter_name, $filter);
	}

}


?>
