<?php
///////////////////////////////////////////////////////////////////////////

require_once 'lib/dune/default_dune_plugin_fw.php';
require_once 'lib/php-plex/Plex.php';


DefaultDunePluginFw::$plugin_class_name = 'EmplexerPlugin';

///////////////////////////////////////////////////////////////////////////


/**
* 		
*/
class EmplexerPlugin implements DunePlugin
{
	
	protected $plexInstance, $mainServer;

	function __construct()
	{
		hd_print('construiu');
		$servers = array('main' => array('address' => '192.168.2.9'));
		$this->plexInstance = new Plex();
		$this->plexInstance->registerServers($servers);
		$this->mainServer = $this->plexInstance->getServer('main');		
	}

	 // PluginFolderView
	public function get_folder_view( $media_url,  &$plugin_cookies){
		hd_print(__METHOD__ . ': teste' );
		return null;
	}

    // PluginFolderView
	public function get_next_folder_view($media_url,&$plugin_cookies){}

    // PluginTvInfo
	public function get_tv_info($media_url,&$plugin_cookies){}

    // String
	public function get_tv_stream_url($media_url,&$plugin_cookies){}

    // PluginVodInfo 
	public function get_vod_info($media_url,&$plugin_cookies){}

    // String
	public function get_vod_stream_url($media_url,&$plugin_cookies){}

    // PluginRegularFolderRange 
	public function get_regular_folder_items($media_url,$from_ndx,&$plugin_cookies){}

    // List<PluginTvEpgProgram>
	public function get_day_epg($channel_id,$day_start_tm_sec,&$plugin_cookies){}

    // String 
	public function get_tv_playback_url($channel_id,$archive_tm_sec,$protect_code, &$plugin_cookies){}

    // GuiAction
	public function change_tv_favorites($op_type, $channel_id, &$plugin_cookies){}

    // GuiAction
	public function handle_user_input(
		&$user_input,
		&$plugin_cookies){
		hd_print(__METHOD__ . ':'.  print_r($user_input, true) );
		hd_print(__METHOD__ . ':'.  print_r($plugin_cookies, true) );
		$start_time = microtime(TRUE);
		// hd_print(print_r($this->mainServer->getLibrary()->getSection('Animes')->getAllShows(), true));
		hd_print(print_r($this->mainServer->getLibrary()->getSections(), true));
		$end_time = microtime(TRUE);
		hd_print('teste === ' . ($end_time - $start_time));
	}
}

?>
