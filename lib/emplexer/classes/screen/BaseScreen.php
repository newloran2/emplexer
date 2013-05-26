<?php

namespace lib\emplexer\screen;

class BaseScreen implements Screen {

    private $screenName;
    function __construct() {
        $this->screenName = 'baseScreen';
    }

    public function get_folder_view(MediaURL $media_url, &$plugin_cookies) {
        
    }

    public function get_id() {
        return $this->screenName;
    }


}

?>
