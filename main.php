<?php

hd_print(memory_get_peak_usage() / 1024 / 1024);


define('ROOT_DIR', dirname(__FILE__));


require_once 'autoload.php';



require_once 'lib/php-plex/Plex.php';

DefaultDunePluginFw::$plugin_class_name = 'EmplexerPlugin';

/**
 * 
 */
class EmplexerPlugin implements DunePlugin {

    protected $plexInstance, $mainServer;

    function __construct() {
        hd_print('construiu');
        $servers = array('main' => array('address' => '192.168.2.9'));
        $this->plexInstance = new Plex();
        $this->plexInstance->registerServers($servers);
        $this->mainServer = $this->plexInstance->getServer('main');
    }

    // GuiAction
    public function handle_user_input(
    &$user_input, &$plugin_cookies) {
        hd_print(__METHOD__ . ':' . print_r($user_input, true));
        hd_print(__METHOD__ . ':' . print_r($plugin_cookies, true));
        $start_time = microtime(TRUE);
        $a = print_r($this->mainServer->getLibrary()->getSection('Animes')->getAllShows(), true);
        // hd_print(print_r($this->mainServer->getLibrary()->getSections(), true));
        $end_time = microtime(TRUE);
        hd_print('teste === ' . ($end_time - $start_time));
        // return $this->get_folder_view(null, $plugin_cookies);
        // return ActionFactory::launch_media_url("http://mp4://192.168.2.9:32400/library/parts/8/file.m4v");

        $pop_up_items = array();
        $pop_up_items[] = array(
            GuiMenuItemDef::caption => 'teste 1'
                // GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
        );
        $pop_up_items[] = array(
            GuiMenuItemDef::caption => 'teste 2'
                // GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
        );
        $pop_up_items[] = array(
            GuiMenuItemDef::is_separator => true
                // GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
        );
        $pop_up_items[] = array(
            GuiMenuItemDef::caption => 'teste 3'
                // GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
        );

        hd_print('memoria: ' . memory_get_peak_usage() / 1024 / 1024);

        return ActionFactory::show_popup_menu($pop_up_items);
    }

    public function change_tv_favorites($op_type, $channel_id, &$plugin_cookies) {
        
    }

    public function get_day_epg($channel_id, $day_start_tm_sec, &$plugin_cookies) {
        
    }

    public function get_folder_view($media_url, &$plugin_cookies) {
        
    }

    public function get_next_folder_view($media_url, &$plugin_cookies) {
        
    }

    public function get_regular_folder_items($media_url, $from_ndx, &$plugin_cookies) {
        
    }

    public function get_tv_info($media_url, &$plugin_cookies) {
        
    }

    public function get_tv_playback_url($channel_id, $archive_tm_sec, $protect_code, &$plugin_cookies) {
        
    }

    public function get_tv_stream_url($media_url, &$plugin_cookies) {
        
    }

    public function get_vod_info($media_url, &$plugin_cookies) {
        
    }

    public function get_vod_stream_url($media_url, &$plugin_cookies) {
        
    }

}

?>
