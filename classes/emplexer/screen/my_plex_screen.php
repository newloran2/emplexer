<?php

class MyPlexScreen extends PlexScreen{


    function __construct($key=null, $func=null){
        if (!isset($key)){
            $key = "https://plex.tv/pms/servers?auth_token=testToken";
        }
        parent::__construct($key);
        hd_print_r("data-> ", $this->data);
    }

    public function generateScreen() {
        return $this->getTemplateByType("servers");
    }

    public function getMediaUrl($data){
        parent::getMediaUrl($data);
    }
}

?>

