<?php 

/**
* emplexer music list
*/

class EmplexerMusicList extends EmplexerBaseChannel{
    const ID ='emplexer_music_video';
    function __construct()
    {
        hd_print(__METHOD__);
        parent::__construct(self::ID);      
    }

    public static function get_media_url_str($key, $type=TYPE_DIRECTORY,$videoMediaArray=null)
    {
        hd_print(__METHOD__);
        return MediaURL::encode(
            array
            (
                'screen_id'         => self::ID,
                'key'               => $key,
                'type'              => $type,
                'video_media_array' => $videoMediaArray
                )
            );
    }
    /**
    * override method to add 'playAll event'
    **/
    public function get_action_map(MediaURL $media_url, &$plugin_cookies)
    {
        UserInputHandlerRegistry::get_instance()->register_handler($this);
        $play_all_action = UserInputHandlerRegistry::create_action($this, 'playAll');
        $events = parent::get_action_map($media_url, $plugin_cookies);

        $events[GUI_EVENT_KEY_PLAY] = $play_all_action;

        hd_print(__METHOD__ .':' . print_r($events, true));
        return $events;
    }

    public function get_folder_views()
    {
        hd_print(__METHOD__);
        return EmplexerConfig::getInstance()->GET_SECTIONS_LIST_VIEW();
    }


    public function handle_user_input(&$user_input, &$plugin_cookies){
        $input = parent::handle_user_input($user_input, $plugin_cookies);
        if (!$input){
            hd_print(__METHOD__);
            if ($user_input->control_id == 'playAll'){
                return $this->doMakePlayListAndPlay($user_input, $plugin_cookies);
            }
        } else {
            return $input;
        }
    }

    public function doMakePlayListAndPlay(&$user_input, &$plugin_cookies){
        hd_print(__METHOD__ . print_r($plugin_cookies, true));
        $media_url = MediaURL::decode($user_input->selected_media_url);
        $url = $this->base_url . $media_url->key;
        $musics = $this->getTrackUrlsFromUrl($url, $plugin_cookies);
        return ActionFactory::launch_media_url($this->makePlayList($musics));
    }

    protected function getTrackUrlsFromUrl($url, &$plugin_cookies){
        $xml = HD::getAndParseXmlFromUrl($url);   
        $musics = array();
        foreach ($xml->Directory as $dir) {
            $url = $this->base_url . $dir->attributes()->key;
            $musics = array_merge($musics, $this->getTrackUrlsFromUrl($url,$plugin_cookies));
        }

        foreach ($xml->Track as $track) {
            $title = $track->attributes()->title;
            if (!$title || $title == ""){
                $title = basename($track->Media->Part->attributes()->file);
            }
            $fileName = $this->base_url . $track->Media->Part->attributes()->key;
            if ($plugin_cookies->connectionMethod == NFS_CONNECTION_TYPE || $plugin_cookies->connectionMethod == SMB_CONNECTION_TYPE ){
                foreach ($plugin_cookies as $key => $value) {
                    
                    if (strstr($track->Media->Part->attributes()->file, $key) !== false){
                        $fileName = str_replace($key, $value, $track->Media->Part->attributes()->file);    
                    }
                } 
            }
            $musics = array_merge($musics, array( "$title" =>  $fileName));
        }

        return $musics;
    }

    /**
    * make the platlist and save on plugin/www
    */
    protected function  makePlayList($musics){
        hd_print(__METHOD__ . ':' . print_r($musics, true));
        $file = '/tmp/www/plugins/emplexer/emplexer_playlist.m3u';
        $m3u = fopen($file, 'w');
        fwrite($m3u, "#EXTM3U\n");
        $count =0;
        foreach ($musics as $key => $value) {
            $count++;
            fwrite($m3u, "#EXTINF:$count, $key\n");
            fwrite($m3u, "$value\n");
        }
        fclose($m3u);
        $playlistUrl = "http://127.0.0.1/plugins/emplexer/emplexer_playlist.m3u";
        //logo a playlist só pra efeito de posterior debug
        hd_print(__METHOD__ . print(HD::http_get_document($playlistUrl, true)));
        return $playlistUrl;
    }
 
}

?>