<?php

class PlexScreen extends BaseScreen implements ScreenInterface, TemplateCallbackInterface
{

    private $cachemanager;
    private $key;

    function __construct($key=null, $func=null) {
        $a = explode("||", $key);
        $this->key = count($a) > 1 ? $a[0] : null;
        $key = $this->key ? $a[1] : $key;
        parent::__construct($key);
        /* hd_print_r('data =' , $this->data); */
        if (isset($func)){
            $a= explode("||", $func);
            $this->generatePlayList($a[1]);
        }
    }

    public function generateScreen(){
        $data = $this->getTemplateByType($this->getViewGroup());
        return $data;
    }

    public function generateScreenWithExtraEntries($extraEntries){
        $data = $this->generateScreen();
        foreach ($extraEntries as $value) {
            array_push($data['data']['initial_range']['items'], $value);
        }
        $data['data']['initial_range']['total'] = count($data['data']['initial_range']['items']);
        $data['data']['initial_range']['count'] = count($data['data']['initial_range']['items']);
        
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

    public function templateInfo(){
        $ret = TemplateManager::getInstance()->getMovieInfoTemplate("info", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
        return $ret;
    }

    public function templateVodPlay(){
        hd_print("este é o path = ". $this->path);
        $item = $this->data->Video[0];
        $parentUrl  = Client::getInstance()->getUrl(null, (string)$item->attributes()->parentKey . "/children") ;
        $extraParams = array();
        $extraParams['key'] = $item->attributes()->ratingKey;
        $url        = Client::getInstance()->getUrl($this->path, (string)$item->Media->Part->attributes()->key );
        $file       = GetterUtils::getValueOrDefault( $item->Media->Part->attributes()->file);
        // the viedeo are mp4 container i use the especial mp4:// syntax to optimise streaming
        if (strstr($url, "http://") && Client::getInstance()->getRemoteFileType($url) == "video/mp4"){
            $url = str_replace("http://", "http://mp4://", $url);
        }
	
        if (isset($file)){
            $mappedUrl = Config::getInstance()->getMapForUrl($file);
            hd_print("o valor de url = $mappedUrl e file = $file");
            $url = GetterUtils::getValueOrDefault($mappedUrl,$url);
        }
        if (strpos($url,'http') == 0 && strpos($url, 'https') == 0) {
            $url = sprintf("http://192.168.2.41:3005/download?url=%s", urlencode($url));
        }
        hd_print("valor de url = $url");
        $vodInfo = array(
            PluginVodInfo::id                  => 1,
            PluginVodInfo::name                => (string)$item->attributes()->title,
            PluginVodInfo::description         => (string)$item->attributes()->summary,
            PluginVodInfo::poster_url          => Client::getInstance()->getThumbUrl((string)$item->attributes()->art),
            PluginVodInfo::initial_series_ndx  => 0,
            PluginVodInfo::buffering_ms        => 6000,
            PluginVodInfo::initial_position_ms => GetterUtils::getValueOrDefault((string)$item->attributes()->viewOffset, 0),
            PluginVodInfo::advert_mode         => false,
            PluginVodInfo::timer               =>   array(GuiTimerDef::delay_ms => 5000),
            PluginVodInfo::series              => array(
                array(
                    PluginVodSeriesInfo::name => (string)$item->attributes()->title,
                    PluginVodSeriesInfo::playback_url => $url,
                    PluginVodSeriesInfo::playback_url_is_stream_url => true,
                )),
            PluginVodInfo::actions => array(
                GUI_EVENT_PLAYBACK_STOP => ActionFactory::invalidate_folders(array($parentUrl)),
                GUI_EVENT_TIMER => Actions::runThisStaticMethod('PlexScreen::startMonitor', array('key'=>(string) $item->attributes()->ratingKey))
            )
        );
        /* hd_print_r("valor de vodInfo", $vodInfo); */
        return ActionFactory::vod_play_with_vod_info($vodInfo);
    }

    /**
     * Exec the media with default dune player and refresh screen after the playback stops
     */
    public function templatePlay(){
        hd_print(__METHOD__);
        hd_print('templatePlay');
        hd_print("este é o path = ". $this->path);
        $item = isset($this->data->Video[0]) ? $this->data->Video[0] : null;
        $item = isset($this->data->Track[0]) && is_null($item)? $this->data->Track[0] : $item;
        $item = isset($this->data->Photo[0]) && is_null($item)? $this->data->Photo[0] : $item;

        // $url        = Client::getInstance()->getUrl(null, (string)$item->Media->Part->attributes()->key );
        $url        = Client::getInstance()->getUrl($this->path, (string)$item->Media->Part->attributes()->key );
        hd_print("valor final de url = $url");
        $file       = GetterUtils::getValueOrDefault( $item->Media->Part->attributes()->file);
        // $url     = Client::getInstance()->getFinalVideoUrl($url);
        $parentUrl  = Client::getInstance()->getUrl(null, (string)$item->attributes()->parentKey . "/children") ;
        $invalidate = ActionFactory::invalidate_folders(array($parentUrl));
        // //hd_print(__METHOD__ . ":" . print_r($this->data->Video[0]->attributes()->ratingKey, true));

        if (isset($this->data->Video[0])){
            $key = $this->data->Video[0]->attributes()->ratingKey;
            $viewOffset = isset($this->data->Video[0]->attributes()->viewOffset) ? $this->data->Video[0]->attributes()->viewOffset : 0 ;

            Client::getInstance()->startMonitor($key, $viewOffset);
        }

        // the viedeo are mp4 container i use the especial mp4:// syntax to optimise streaming
        if (strstr($url, "http://") && Client::getInstance()->getRemoteFileType($url) == "video/mp4"){
            $url = str_replace("http://", "http://mp4://", $url);
        }

        if (isset($file)){
            $mappedUrl = Config::getInstance()->getMapForUrl($file);
            hd_print("o valor de url = $mappedUrl e file = $file");
            $url = GetterUtils::getValueOrDefault($mappedUrl,$url);
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
                $url =Client::getInstance()->getUrl(null, $value->Part->attributes()->key);
                hd_print("passei aqui: $url");

                $b->addControl(new GuiControlButton(
                    'bt1',
                    sprintf("%sp",$value->attributes()->videoResolution) ,
                    600,
                    ActionFactory::launch_media_url()
                )
            );
            }
            $b->show();
        }

        $url = sprintf("http://192.168.2.41:3005/download?url=%s", urlencode($url));

        // $url = "http://192.168.2.44/cgi-bin/plugins/emplexer2/testeGetter.sh?url=". urlencode($url);
        return ActionFactory::launch_media_url($url);


    }

    public function getField($name, $item, $field=null){
        switch (gettype($name)) {
            case 'double':
            case 'integer':
            case 'boolean':
            case 'NULL':
                return $name;
        }

        if (strstr($name, "gui_skin") || strstr($name, "cut_icon") ){
            return $name;
        } else {
            $fields = explode("||", $name);
        }
        /**
         * https://+plex_item_field:address+/library/sections
         * https:// plex_item_field:address /library/sections
         *
         *
         */

        $currentPath = $this->path;
        foreach ($fields as $value) {
            $field =  explode("::", $value);
            if (count($field) <=1){
                return $value == "null" ?  null : $value;
            }
            if ($field[0] === "plex_field"){
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = $this->data->attributes()->{$field[1]};
                return $ret;
            } else if ($field[0] == 'plex_field_expr'){
                $ret =  $this->getPlexFiledExpression($this->data, $field);
                if (!isset($ret)) continue;
            } else if ($field[0] === "plex_field_roles_expr"){
                $ret = $this->getPlexFieldRolesExpression($this->data, $field);
                if (!isset($ret)) continue;
            } else if ($field[0] === "plex_thumb_field") {
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getThumbUrl($this->data->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                return $ret;
            } else  if ($field[0] === "plex_image_field"){
                if (!isset($this->data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getUrl($currentPath, $this->data->attributes()->{$field[1]});
                hd_print("ret = $ret");
            }
            if (isset($item)){
                //"icon_path": "plex_thumb_item_field:atribute name:w:h:use plex helpers?"
                if ($field[0] === "plex_thumb_item_field") {
                    if (!isset($item->attributes()->{$field[1]})) continue;
                    if (!isset($field[4])){
                        $ret = Client::getInstance()->getThumbUrl($item->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                    } else {
                        //se tiver o campo leafCount no item significa que é uma listagem de série ou temporada então pego o thumb com o badge com a quantidade de episódios
                        if (isset($item->attributes()->leafCount)){
                            $count = (int)$item->attributes()->leafCount - (int)$item->attributes()->viewedLeafCount;
                            // $ret = Client::getInstance()->getEpisodeCouterThumbUrl($item->attributes()->{$field[1]}, $count, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                            $ret = Client::getInstance()->getThumbUrl($item->attributes()->{$field[1]});

                        } else if (isset($item->attributes()->viewOffset)){
                            //se o campo viewOffset vier em item esse é um item de uma listagem de episódios parcialmente vista, então, preciso pedir o thumb com o progressbar.
                            $percent =  (int)$item->attributes()->viewOffset/(int)$item->attributes()->duration *100;
                            $ret = Client::getInstance()->getEpisodeProgressThumbUrl($item->attributes()->{$field[1]}, $percent, $item->attributes()->title, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                        }else {
                            //se não apenas pego o thumb sem nada (caso de um thumb de série/temporada/epiódio que já tenha sido visto completamente)
                            // $ret = Client::getInstance()->getThumbUrl($item->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                            $ret = Client::getInstance()->getEpisodeProgressThumbUrl($item->attributes()->{$field[1]}, $percent, $item->attributes()->title, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
                        }
                    }

                } else  if ($field[0] === "plex_image_item_field"){
                    if (!isset($item->attributes()->{$field[1]})) continue;
                    //                    hd_print("currentPath = $currentPath");
                    $ret = Client::getInstance()->getUrl($currentPath, $item->attributes()->{$field[1]});

                } else if ($field[0] === "plex_item_field"){
                    if (!isset($item->attributes()->{$field[1]})) continue;
                    $ret = $item->attributes()->{$field[1]};
                } else if ($field[0] === "plex_item_field_expr"){

                    
                    $ret = $this->getPlexFiledExpression($item,$field);
                    if (!isset($ret)) continue;
                } else if ($field[0] === "plex_item_xpath"){
                    //very strange but, xpath apply the xpath on entire tree instead the current element, becouse that i need to make a xml from the element and re create the tree to use xpath
                    $dom = new DOMDocument();
                    $dom->loadXML($item->asXml());
                    $xpath = new DOMXPath($dom);
                    $ret = $xpath->evaluate($field[1]);
                    // $element =  simplexml_load_string($item->asXml());
                    // $element =  simplexml_load_string($item->asXml());
                    // $ret = $element->xpath($field[1]);

                    // if (isset($ret) && is_array($ret) && count($ret)>=1 ){
                    //     hd_print("entrou aqui e ret é $ret");
                    //     $ret = Client::getInstance()->getUrl($currentPath, (string)$ret[0]);
                    // } else {
                    //     $ret = null;
                    // }
                    return $ret;
                }
            }
            if (isset($ret)){
                $a = gettype($ret) == "object" ? TranslationManager::getInstance()->getTranslation((string)$ret):TranslationManager::getInstance()->getTranslation($ret);
                return $a;
            }
        }
    }

    protected function templateMovie (){
        $a = parent::templateMovie();
        $actions = $a['data']['actions'];
        $actions[GUI_EVENT_KEY_INFO] = Actions::runThisStaticMethod("PlexScreen::openMovieInfo");
        $a['data']['actions']= $actions;
        return $a;

    }

    public function getData(){
        /* hd_print_r(__METHOD__, $this->data); */	
        return $this->data;
    }

    public function getMediaUrl($data){
        //hd_print(__METHOD__);
        return Client::getInstance()->getUrl($this->path , (string)$this->data->attributes()->key);
    }
    public static function showpopup($user_input){
        $xml = Client::getInstance()->getAndParse($user_input->selected_media_url);
        if (isset($xml->Video)){
            $extraParams = array();
            $extraParams['key'] = (string)$xml->Video->attributes()->ratingKey;
            $alert = new Modal('');

            if (isset($xml->Video->attributes()->viewCount)){
                $label =  _('Mark as unseen');
                $extraParams['function'] = 'unscrobble';
            } else {
                $label = _('Mark as seen');
                $extraParams['function'] = 'scrobble';
            }
            $alert->addControl(new GuiControlButton('mark', $label,-1, Actions::closeAndRunThisStaticMethod('PlexScreen::markAsSeenOrUnSeen', $extraParams), -50));
            return $alert->generate();
        }
    }

    public static function markAsSeenOrUnSeen($user_input){
        $url = Client::getInstance()->getUrl(null, sprintf('/:/%s?key=%s&identifier=com.plexapp.plugins.library', $user_input->function,$user_input->key));
        hd_print(__METHOD__ . " O valor de url = $url");
        Client::getInstance()->get($url);
        return ActionFactory::invalidate_folders(array($user_input->parent_media_url));
    }

    public static function startMonitor($user_input){
        /* hd_print_r(__METHOD__. " O Valor de user_input", $user_input); */
        Client::getInstance()->startMonitor($user_input->key,0);
    }

    public static function openMovieInfo($user_input){
        return ActionFactory::open_folder("movieInfo||". $user_input->selected_media_url);
    }


    protected function getPlexFiledExpression($data,$field){
        $val = $field[1];

        $negate = $val[0] == '!' ? true :  false;
        if ($negate){
            $val = substr($val,1);
            $temp = '!$data->' .$val;
        } else { 
            $temp = '$data->' . $val; 
        }
        $ret = eval('return '.$temp . ';');
        $ret = is_array($ret) && count($ret) == 1 ? $ret[0] : $ret;
        switch (gettype($ret)) {
        case 'double':
        case 'integer':
        case 'boolean':
        case 'string':
        case 'NULL':
            return $ret;
        default:
            return (string)$ret;
        }
        return (string)$ret;
    }
    protected function getPlexFieldRolesExpression($data, $field){
        $temp = '$data->' . $field[1]; 
        $nodes = eval('return '.$temp . ';');
        $rolesStr = "";
        foreach ($nodes as $node){
           $rolesStr .= $node->attributes()->tag;
           if (isset($node->attributes()->role) && !(trim($node->attributes()->role) == "")){
               $rolesStr .= sprintf(" %s %s\n", _('as'), $node->attributes()->role);
           } else {
               $rolesStr .= "\n";
           }
        }
        return $rolesStr;
        
    }        


}


?>
