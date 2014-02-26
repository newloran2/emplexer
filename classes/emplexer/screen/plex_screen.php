    <?php

class PlexScreen extends BaseScreen implements ScreenInterface, TemplateCallbackInterface
{

    private $cachemanager;

    function __construct($key=null, $func=null) {
        hd_print(__METHOD__);
        parent::__construct($key);
        // echo "teste\n";
        // $this->cachemanager =  new CacheManager('/tmp/cache');
        if (isset($func)){
            $a= explode("||", $func);
            // var_dump($a);
            $this->generatePlayList($a[1]);
        }
    }


	public function generateScreen(){
        hd_print(__METHOD__);
        // print_r($this->data);

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

        hd_print("viewGroup selecionado $viewGroup");

		$data = $this->getTemplateByType($viewGroup);
        //download images
        // $this->cachemanager->exec();
        // $this->cachemanager->clear();
        return $data;
	}


    public function generatePlayList($key)
    {
        hd_print(__METHOD__);
        $url = Client::getInstance()->getUrl(null, "/library/metadata/$key/children");
        // $url .= "?unwatched=1";

        // var_dump($url);

        $xml = Client::getInstance()->getAndParse($url);
        // var_dump($xml);
        foreach ($xml as $value) {
            // hd_print($value->Video->attributes()->title);
        }

    }

	/**
	 * Exec the media with default dune player and refresh screen after the playback stops
	 */
	public function templatePlay(){
        hd_print(__METHOD__);
        hd_print('templatePlay');
        $item = isset($this->data->Video[0]) ? $this->data->Video[0] : null;
        $item = isset($this->data->Track[0]) && is_null($item)? $this->data->Track[0] : $item;
        $item = isset($this->data->Photo[0]) && is_null($item)? $this->data->Photo[0] : $item;

		$url=Client::getInstance()->getUrl(null, (string)$item->Media->Part->attributes()->key );
        // $url =  Client::getInstance()->getFinalVideoUrl($url);
		$parentUrl =  Client::getInstance()->getUrl(null, (string)$item->attributes()->parentKey . "/children") ;
		$invalidate =  ActionFactory::invalidate_folders(array($parentUrl));
        // hd_print(__METHOD__ . ":" . print_r($this->data->Video[0]->attributes()->ratingKey, true));

        // $key = $this->data->Video[0]->attributes()->ratingKey;
        // $viewOffset = isset($this->data->Video[0]->attributes()->viewOffset) ? $this->data->Video[0]->attributes()->viewOffset : 0 ;

        // Client::getInstance()->startMonitor($key, $viewOffset);

        // the viedeo are mp4 container i use the especial mp4:// syntax to optimise streaming
        if (strstr($url, "http://") && Client::getInstance()->getRemoteFileType($url) == "video/mp4"){
            $url = str_replace("http://", "http://mp4://", $url);
        }
        hd_print("count de media é =  " . count($item->Media) );

        if (count($item->Media)>1){
            $b = new Modal("", null ,100);
            $menu = array();
            $d = $item->Media;
            $m = array();
            foreach ( $d as $key => $value) {
                // $action =  ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, $value->Part->attributes()->key));
                // $m[] = array(
                //     'caption' =>  sprintf("%sp",$value->attributes()->videoResolution),
                //     'action' => $action
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
            // return ActionFactory::show_popup_menu($m);



        }


		return ActionFactory::launch_media_url($url);


	}

	public function getField($name, $item){
        // hd_print(__METHOD__);
        if (strstr($name, "gui_skin") || strstr($name, "cut_icon") ){
            // hd_print("gui_skin or cut_icon detected returning the nam $name");
    		return $name;
    	} else {
    		$fields = explode("||", $name);
    	}

		$currentPath = $this->path;
        foreach ($fields as $value) {
            $field =  explode(":", $value);
            if (count($field) <=1){
                // hd_print("single value that's not plex_field returning name $name");
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
                    // if ($ret === "http://192.168.2.8:32400/:/plugins/com.plexapp.plugins.youtube/resources/contentWithFallback?urls=http%253A%2F%2Fi1.ytimg.com%2Fvi%2FlDtQwVF_Nc8%2Fhqdefault.jpg%2Chttp%253A%2F%2Fi1.ytimg.com%2Fvi%2FlDtQwVF_Nc8%2Fdefault.jpg") {
                    // $ret = Client::getInstance()->getFinalThumbUrl($ret);
                    // }

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

                    // hd_print("valor de ret2 $ret" );
                }
	        }

            // if (strstr($field[0], "thumb")){
            //     $ret = $this->cachemanager->addSession($ret);
            // }
	        if (isset($ret)){
                $a = gettype($ret) == "object" ? TranslationManager::getInstance()->getTranslation((string)$ret):TranslationManager::getInstance()->getTranslation($ret);
                // hd_print("returning plex_fiel value $a");
	        	return $a;
	        }
        }
	}

    public function getData(){
        hd_print(__METHOD__);
    	return $this->data;
    }

    public function getMediaUrl($data){
        hd_print(__METHOD__);
    	return Client::getInstance()->getUrl($this->path , (string)$this->data->attributes()->key);
    }

}


 ?>