
<?php
    require_once 'AutoLoad.php';

    class Emplexer implements DunePlugin {

        public $stream_name;

        function __construct($foo = null) {
        }

        function __destruct()
        {
            shell_exec('kill $(pgrep lem-dune.bin)');
        }

        public function get_folder_view($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
            // Client::getInstance()->startEmplexerServerAndRegisterAsPlayer();
            // Client::getInstance()->getMyPlexServers();
            if (strstr(strtolower($media_url), 'setup')){
                $menu = $menu = new SetupScreen($media_url);
            } else if ($media_url == 'main'){
                $menu =  new PlexScreen($media_url);
                $a = $menu->generateScreen();

                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "local",
                    "media_url"=> "local",
                    "view_item_params"=> array()
                ));
                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "nfs",
                    "media_url"=> "nfs|nfs",
                    "view_item_params"=> array()
                ));
                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "smb",
                    "media_url"=> "smb",
                    "view_item_params"=> array()
                ));

                array_push($a['data']['initial_range']['items'], array(
                    "caption"=> "Channels",
                    "media_url"=> "/video",
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
                //nfs|192.168.2.9
                //nfs|nfs://192.168.2.9:/volume1/Animes
                $d = explode("|", $media_url);
                // hd_print_r("d ", $d);
                // $menu = new NfsScreen('192.168.2.9');
                // $t = explode("nfs://", $d[1]);
                // $v = $t[1];
                // hd_print_r("v ", $v);
                $menu = new NfsScreen(count($d) >  1 ? $d[1]: $d[0]);
            }
            else {
                $menu =   new PlexScreen($media_url);
            }

            return $menu->generateScreen();
        }

        public function get_next_folder_view($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
            $menu =   new PlexScreen($media_url, true);

            return $menu->generateScreen();
        }

        public function get_tv_info($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_tv_stream_url($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }
        //Play selected stream with selected quality
        public function get_vod_info($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_vod_stream_url($media_url, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_regular_folder_items($media_url, $from_ndx, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_day_epg($channel_id, $day_start_tm_sec, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function get_tv_playback_url($channel_id, $archive_tm_sec, $protect_code, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function change_tv_favorites($op_type, $channel_id, &$plugin_cookies) {
            hd_print(__METHOD__);
            Config::getInstance()->setPluginCookies($plugin_cookies);
        }

        public function handle_user_input(&$user_input, &$plugin_cookies) {
            Config::getInstance()->setPluginCookies($plugin_cookies);
            hd_print(__METHOD__);
            hd_print(__METHOD__ . ':' . print_r($user_input, true));
            if (isset($user_input->type) && $user_input->type == "closeAndRunThisStaticMethod"){
                return call_user_func($user_input->method, $user_input);
            }

            if (isset($user_input->type) && $user_input->type == "runThisStaticMethod"){
                return call_user_func($user_input->method, $user_input);
            }
            HD::print_backtrace();
            if (isset($user_input->selected_control_id) && $user_input->selected_control_id === "quickSavePlexPrefs"){
                // hd_print("entrou");
                $plugin_cookies->plexIp = $user_input->plexIp;
                $plugin_cookies->plexPort = $user_input->plexPort;
                Config::getInstance()->setPluginCookies($plugin_cookies);
                return ActionFactory::open_folder($user_input->selected_media_url);
            }


            if (strstr(strtolower($user_input->selected_media_url), "setup")){
                $menu = new SetupScreen($user_input->selected_media_url);
            } else {
                $menu =   new PlexScreen($user_input->selected_media_url, isset($user_input->function)? $user_input->function : null  );
            }

            //in some cases one video list have a directory entry to open the next page (ex: youtube channel ), in that case i need to return an openfolder to reestar the open process.
            // if (strstr($user_input->selected_media_url, "http://") && Client::getInstance()->getRemoteFileType($user_input->selected_media_url) == "application/xml"){
            //     return ActionFactory::open_folder($user_input->selected_media_url);
            // }

            return $menu->generateScreen();
            // return ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, (string)"http://www.google.com"));
        }
}


?>


