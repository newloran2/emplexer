<?php

class PlexScreen extends BaseScreen implements ScreenInterface, TemplateCallbackInterface
{

    private $cachemanager;

    function __construct($key=null, $func=null) {
        parent::__construct($key);
        if (isset($func)){
            $a= explode("||", $func);
            $this->generatePlayList($a[1]);
        }
    }

    public static function showpopup($userInput){
        $popUp = new PopupMenu();
    }
    public function generateScreen(){

        $viewGroup = (string)$this->data->attributes()->viewGroup;

        if ((isset($this->data->attributes()->content)
                    &&$this->data->attributes()->content == "plugins")
                ||(isset($this->data->attributes()->identifier)
                    && !strstr((string)$this->data->attributes()->identifier, "library"))){
            $viewGroup = 'plugins';
        }

        if ($this->data->attributes()->size == 1 && isset($this->data->Video)){
            $viewGroup = 'play';
        }
        if (!$viewGroup && strstr($this->path, 'metadata')){
            $viewGroup = 'play';
        }

        $data = $this->getTemplateByType($viewGroup);
        return $data;
    }


    public function generatePlayList($key)
    {
        $url = Client::getInstance()->getUrl(null, "/library/metadata/$key/children");
        $xml = Client::getInstance()->getAndParse($url);
        foreach ($xml as $value) {
            // //hd_print($value->Video->attributes()->title);
        }

    }

    /**
     * Exec the media with default dune player and refresh screen after the playback stops
     */
    public function templatePlay(){
        //hd_print(__METHOD__);
        //hd_print('templatePlay');
        $item = isset($this->data->Video[0]) ? $this->data->Video[0] : null;
        $item = isset($this->data->Track[0]) && is_null($item)? $this->data->Track[0] : $item;
        $item = isset($this->data->Photo[0]) && is_null($item)? $this->data->Photo[0] : $item;

        $url        = Client::getInstance()->getUrl(null, (string)$item->Media->Part->attributes()->key );
        $file       = GetterUtils::getValueOrDefault( $item->Media->Part->attributes()->file);
        // $url     = Client::getInstance()->getFinalVideoUrl($url);
        $parentUrl  = Client::getInstance()->getUrl(null, (string)$item->attributes()->parentKey . "/children") ;
        $invalidate = ActionFactory::invalidate_folders(array($parentUrl));
        // //hd_print(__METHOD__ . ":" . print_r($this->data->Video[0]->attributes()->ratingKey, true));

        // $key = $this->data->Video[0]->attributes()->ratingKey;
        // $viewOffset = isset($this->data->Video[0]->attributes()->viewOffset) ? $this->data->Video[0]->attributes()->viewOffset : 0 ;

        // Client::getInstance()->startMonitor($key, $viewOffset);

        // the viedeo are mp4 container i use the especial mp4:// syntax to optimise streaming
        if (strstr($url, "http://") && Client::getInstance()->getRemoteFileType($url) == "video/mp4"){
            $url = str_replace("http://", "http://mp4://", $url);
        }

        if (isset($file)){
            $url = Config::getInstance()->getMapForUrl($file);
            hd_print("o valor de url = $url e file = $file");
        }

        if (count($item->Media)>1){
            $b                   = new Modal("", null ,100);
            $menu                = array();
            $d                   = $item->Media;
            $m                   = array();
            foreach ( $d as $key => $value) {
                // $action       = ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, $value->Part->attributes()->key));
                // $m[]          = array(
                //     'caption' = >  sprintf("%sp",$value->attributes()->videoResolution),
                //     'action'  = > $action
                // );
                $b->addControl(new GuiControlButton(
                            'bt1',
                            sprintf("%sp",$value->attributes()->videoResolution) ,
                            600,
                            ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, $value->Part->attributes()->key))
                            )
                        );
            }
            $b->show();
        }


        return ActionFactory::launch_media_url($url);


    }

    public function getField($name, $item, $field=null){
        if (strstr($name, "gui_skin") || strstr($name, "cut_icon") ){
            return $name;
        } else {
            $fields = explode("||", $name);
        }

        $currentPath = $this->path;
        foreach ($fields as $value) {
            $field =  explode(":", $value);
            if (count($field) <=1){
                return $name;
            }


            if ($field[0] === "plex_field"){
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = $this->data->attributes()->{$field[1]};
            } else if ($field[0] === "plex_thumb_field") {
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getThumbUrl($this->data->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
            } else  if ($field[0] === "plex_image_field"){
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getUrl($currentPath, $this->data->attributes()->{$field[1]});
            }
            if (isset($item)){

                if ($field[0] === "plex_thumb_item_field") {
                    if (!isset($item->attributes()->{$field[1]})) continue;
                    $ret = Client::getInstance()->getThumbUrl($item->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                } else  if ($field[0] === "plex_image_item_field"){
                    if (!isset($item->attributes()->{$field[1]})) continue;

                    $ret = Client::getInstance()->getUrl($currentPath, $item->attributes()->{$field[1]});

                } else if ($field[0] === "plex_item_field"){
                    if (!isset($item->attributes()->{$field[1]})) continue;
                    $ret = $item->attributes()->{$field[1]};
                } else if ($field[0] === "plex_item_xpath"){
                    //very strange but, xpath apply the xpath on entire tree instead the current element, becouse that i need to make a xml from the element and re create the tree to use xpath
                    $element =  simplexml_load_string($item->asXml());
                    $ret = $element->xpath($field[1]);
                    if (isset($ret) && is_array($ret) && count($ret)>=1 ){
                        $ret = Client::getInstance()->getUrl($currentPath, (string)$ret[0]);
                    } else {
                        $ret = null;
                    }
                }

                if (isset($ret)){
                    $a = gettype($ret) == "object" ? TranslationManager::getInstance()->getTranslation((string)$ret):TranslationManager::getInstance()->getTranslation($ret);
                    return $a;
                }
            }
        }
    }

    public function getData(){
        //hd_print(__METHOD__);
        return $this->data;
    }

    public function getMediaUrl($data){
        //hd_print(__METHOD__);
        return Client::getInstance()->getUrl($this->path , (string)$this->data->attributes()->key);
    }

}


?>
