
<?php

    require_once 'AutoLoad.php';

    class Emplexer implements DunePlugin {

        public $stream_name;

        function __construct($foo = null) {
            // Client::getInstance()->startEmplexerServer();
            // sleep(1);
            // Client::getInstance()->registerAsPlayer();
        }

        public function get_folder_view($media_url, &$plugin_cookies) {

            Config::getInstance()->setPluginCookies($plugin_cookies);
            // Client::getInstance()->getMyPlexServers();
            if (strstr(strtolower($media_url), 'setup')){
                $menu = $menu = new SetupScreen($media_url);
            } else if ($media_url == 'main'){
                $menu = new PlexScreen($media_url, array("http://5.147.181.234:32400/library/sections"));
                $a = $menu->generateScreen();

                // $myPlex = new PlexScreen("https://plex.tv/library/sections");

                // $b = $myPlex->generateScreen();

                // hd_print(print_r($b, true));

                // var_dump($a['data']['initial_range']['items']);

                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "Channels",
                    "media_url"=> Client::getInstance()->getUrl(null, "/video"),
                    "view_item_params"=> array()
                ));

                // array_push($a['data']['initial_range']['items'], array(
                //     "caption"=> "MyPlex",
                //     "media_url"=> Client::getInstance()->getUrl(null, "https://plex.tv/library/sections"),
                //     "view_item_params"=> array()
                // ));
                // $a[]
                // print_r($a);
                $a['data']['initial_range']['total'] = count($a['data']['initial_range']['items']);
                $a['data']['initial_range']['count'] = count($a['data']['initial_range']['items']);

                return $a;

            } else if (strstr(strtolower($media_url), 'nfs')) {
                $menu = new NfsScreen('192.168.2.9');
            }
            else {
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
            hd_print(__METHOD__ . ':' . print_r($user_input, true));
            if ($user_input->selected_control_id === "quickSavePlexPrefs"){
                // hd_print("entrou");
                $plugin_cookies->plexIp = $user_input->plexIp;
                $plugin_cookies->plexPort = $user_input->plexPort;
                Config::getInstance()->setPluginCookies($plugin_cookies);
                return ActionFactory::open_folder($user_input->selected_media_url);
            }

            Config::getInstance()->setPluginCookies($plugin_cookies);



            if (strstr(strtolower($user_input->selected_media_url), "setup")){
                $menu = new SetupScreen($user_input->selected_media_url);
            } else {
                $menu =  new PlexScreen($user_input->selected_media_url, isset($user_input->function)? $user_input->function : null  );
            }

            return $menu->generateScreen();
            // return ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, (string)"http://www.google.com"));
        }
}
?>
