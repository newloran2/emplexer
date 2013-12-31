
<?php

    // namespace classes\emplexer;
    // require_once 'lib/default_dune_plugin_fw.php';
    // require_once 'Plex/Client.php';

    require_once 'AutoLoad.php';

    class Emplexer implements DunePlugin {

        public $stream_name;

        public function get_folder_view($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
            switch ($media_url) {
                case 'main':
                    $menu = new PlexScreen();
                    break;
                default:
                    $menu =  new PlexScreen($media_url);
                    break;
            }

            return $menu->generateScreen();
        }

        public function get_next_folder_view($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
            $menu =  new PlexScreen($media_url, true);

            return $menu->generateScreen();
        }

        public function get_tv_info($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_tv_stream_url($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }
        //Play selected stream with selected quality
        public function get_vod_info($media_url, &$plugin_cookies) {
            // Config::getInstance()->setPluginCookies($plugin_cookies);
            // if (strpos($media_url, "stream_name:") === 0) {
            //     $stream = new GamePlay(substr($media_url, 12),$this->stream_name);
            //     return $stream->generatePlayInfo();
            // }
        }

        public function get_vod_stream_url($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_regular_folder_items($media_url, $from_ndx, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_day_epg($channel_id, $day_start_tm_sec, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_tv_playback_url($channel_id, $archive_tm_sec, $protect_code, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function change_tv_favorites($op_type, $channel_id, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function handle_user_input(&$user_input, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
            // var_dump($user_input);
            $menu =  new PlexScreen($user_input->media_url);
            return $menu->generateScreen();
            // return ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, (string)"http://www.google.com"));
        }
}
?>
