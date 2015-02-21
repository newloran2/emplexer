
<?php
require_once 'AutoLoad.php';
class Emplexer implements DunePlugin {
    public $stream_name;
    private $availableScreens;

    function __construct($foo = null) {
        $this->availableScreens = array(
            'configRoot'  => 'EmplexerSetupScreen',
            'language'    => 'LanguageChose',
            'mediaServer' => 'SetupMediaServer',
            'myPlex'      => 'MyPlexScreen',
            'testGcomps'  => 'BaseGcompsScreen'
        );
        HD::print_info();
    }
    
    function __destruct()
    {
        if (!DEV){
            shell_exec('kill $(pgrep lem-dune.bin)');
        }
    }
    
    public function getScreen($media_url, &$plugin_cookies){
         hd_print_r('this->availableScreens = ' , $this->availableScreens);
        hd_print(__METHOD__ . " MediaURL = $media_url");
        Config::getInstance()->setPluginCookies($plugin_cookies);
        Client::getInstance()->startEmplexerServerAndRegisterAsPlayer();
        // Client::getInstance()->getMyPlexServers();
        if (strstr(strtolower($media_url), 'setup')){
            $menu = $menu = new SetupScreen($media_url);
        } else if ($media_url == 'main'){
            $menu =  new PlexScreen($media_url);
            $a = $menu->generateScreenWithExtraEntries(array(
                array(
                    "caption"          => "Channels",
                    "media_url"        => "/video",
                    "view_item_params" => array()
                ),
                array(
                    "caption"          => "Config Test1",
                    "media_url"        => "configRoot",
                    "view_item_params" => array()
                ),
                array(
                    "caption"          => _("Shared Content"),
                    "media_url"        => "myPlex",
                    "view_item_params" => array()
                ),
                array(
                    "caption"          => "testGcomps",
                    "media_url"        => "testGcomps",
                    "view_item_params" => array()
                )
            )

            );
            // array_push($a['data']['initial_range']['items'], array(
            //     "caption"=> "Channels",
            //     "media_url"=> "/video",
            //     "view_item_params"=> array()
            // ));

            //                 array_push($a['data']['initial_range']['items'], array(
            //                     "caption"=> "local",
            //                     "media_url"=> "local",
            //                     "view_item_params"=> array()
            //                 ));
            //                 array_push($a['data']['initial_range']['items'], array(
            //                     "caption"=> "nfs",
            //                     "media_url"=> "nfs|nfs",
            //                     "view_item_params"=> array()
            //                 ));
            //                 array_push($a['data']['initial_range']['items'], array(
            //                     "caption"=> "smb",
            //                     "media_url"=> "smb",
            //                     "view_item_params"=> array()
            //                 ));
            //
            // array_push($a['data']['initial_range']['items'], array(
            //     "caption"=> "Config Test",
            //     "media_url"=> "configRoot",
            //     "view_item_params"=> array()
            // ));

            // $a['data']['initial_range']['total'] = count($a['data']['initial_range']['items']);
            // $a['data']['initial_range']['count'] = count($a['data']['initial_range']['items']);
            //            hd_print_r("valor = ", $a);
            return $a;

        } else if (strstr(strtolower($media_url), 'nfs')) {
            $d = explode("|", $media_url);
            $menu = new NfsScreen(count($d) > 1 ? $d[1]: $d[0]);
        } else if (array_key_exists($media_url, $this->availableScreens)){
            $menu = new $this->availableScreens[$media_url]();
        } else {
            $menu =   new PlexScreen($media_url);
        }
        return $menu;
           
    }
    public function get_folder_view($media_url, &$plugin_cookies) {
        $a= $this->getScreen($media_url, $plugin_cookies);
        //echo "valor de a =\n";
        //print_r($a);
        return getType($a) !== "object" ? $a :  $a->generateScreen();
    }

    public function get_next_folder_view($media_url, &$plugin_cookies) {
        $menu = $this->getScreen($media_url, $plugin_cookies);
        TemplateManager::getInstance()->setNextTemplateByType($menu->getViewGroup());
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
        hd_print(__METHOD__);
        HD::print_backtrace();
        if (isset($user_input->selected_control_id) && $user_input->selected_control_id === "quickSavePlexPrefs"){
            // hd_print("entrou");
            $plugin_cookies->plexIp = $user_input->plexIp;
            $plugin_cookies->plexPort = $user_input->plexPort;
            Config::getInstance()->setPluginCookies($plugin_cookies);
            return ActionFactory::open_folder($user_input->selected_media_url);
        }
        Config::getInstance()->setPluginCookies($plugin_cookies);
        hd_print(__METHOD__ . ':' . print_r($user_input, true));
        if (isset($user_input->type) && $user_input->type == "closeAndRunThisStaticMethod"){
            return call_user_func($user_input->method, $user_input);
        }

        if (isset($user_input->type) && $user_input->type == "runThisStaticMethod"){
            return call_user_func($user_input->method, $user_input);
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
