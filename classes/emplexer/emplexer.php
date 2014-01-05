
<?php

    // namespace classes\emplexer;
    // require_once 'lib/default_dune_plugin_fw.php';
    // require_once 'Plex/Client.php';

    require_once 'AutoLoad.php';

    class Emplexer implements DunePlugin {

        public $stream_name;

        public function get_folder_view($media_url, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
            if (strstr(strtolower($media_url), 'setup')){
                $menu = $menu = new SetupScreen($media_url);
            } else if ($media_url == 'main'){
                $menu = new PlexScreen($media_url);
                $a = $menu->generateScreen();

                // var_dump($a['data']['initial_range']['items']);

                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "Channels",
                    "media_url"=> Client::getInstance()->getUrl(null, "/video"),
                    "view_item_params"=> array()
                ));
                // $a[]
                // print_r($a);
                $a['data']['initial_range']['total'] = count($a['data']['initial_range']['items']);
                $a['data']['initial_range']['count'] = count($a['data']['initial_range']['items']);

                return $a;

            } else {
                $menu =  new PlexScreen($media_url);
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
            Config::getInstance()->setPluginCookies($plugin_cookies);
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
            if (strstr(strtolower($user_input->media_url), "setup")){
                $menu = new SetupScreen($user_input->media_url);
            } else {
                $menu =  new PlexScreen($user_input->media_url);
            }

            return $menu->generateScreen();
            // return ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, (string)"http://www.google.com"));
        }
}
?>
