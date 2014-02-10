    <?php

class PlexScreen extends BaseScreen implements ScreenInterface, TemplateCallbackInterface
{

    private $cachemanager;

    function __construct($key=null, array $extraPaths = null, $func=null) {
        parent::__construct($key, $extraPaths);
        // hd_print("teste ==== $key");
        // echo "teste\n";
        $this->cachemanager =  new CacheManager('/tmp/cache');
        if (isset($func)){
            $a= explode("||", $func);
            // var_dump($a);
            $this->generatePlayList($a[1]);
        }
    }


	public function generateScreen(){


		$viewGroup = (string)$this->data->attributes()->viewGroup;

        if ((isset($this->data->attributes()->content)
            &&$this->data->attributes()->content == "plugins")
            ||(isset($this->data->attributes()->identifier)
            && !strstr((string)$this->data->attributes()->identifier, "library"))){
            $viewGroup = 'plugins';
        }

		if (!$viewGroup && strstr($this->path, 'metadata')){
			$viewGroup = 'play';
		}
		$data = $this->getTemplateByType($viewGroup);
        //download images
        // $this->cachemanager->exec();
        // $this->cachemanager->clear();
        return $data;
	}


    public function generatePlayList($key)
    {
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
        $item = isset($this->data->Video[0]) ? $this->data->Video[0] : null;
        $item = isset($this->data->Track[0]) && is_null($item)? $this->data->Track[0] : $item;
        $item = isset($this->data->Photo[0]) && is_null($item)? $this->data->Photo[0] : $item;

        $a =sprintf("http://%s%s", $this->parsedPath["host"],(string)$item->Media->Part->attributes()->key);
        $data =  Client::getInstance()->get($a , array( CURLOPT_NOBODY, true) );
        hd_print(print_r($data, true));
		$url=Client::getInstance()->getUrl(null, $a);
		$parentUrl =  Client::getInstance()->getUrl(null, sprintf("%s://%s:%s%s", $this->parsedPath["scheme"],$this->parsedPath["host"],$this->parsedPath["port"],(string)$item->attributes()->parentKey . "/children"));
		$invalidate =  ActionFactory::invalidate_folders(array($parentUrl));
        // hd_print(__METHOD__ . ":" . print_r($this->data->Video[0]->attributes()->ratingKey, true));

        // if (!$this->isPlexSync){
        //     $key = $this->data->Video[0]->attributes()->ratingKey;
        //     $viewOffset = isset($this->data->Video[0]->attributes()->viewOffset) ? $this->data->Video[0]->attributes()->viewOffset : 0 ;
        //     Client::getInstance()->startMonitor($key, $viewOffset);
        // }

		return ActionFactory::launch_media_url($url,$invalidate);

	}

	public function getField($name, $item, $data){


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






          if (isset($data)){
            $parsedPath = parse_url(key($data));
            // hd_print_r("parsedPath = ", $parsedPath);
            // hd_print_r("data", $data);
            // HD::print_backtrace();
            if (is_array($data)){
                $data =  current($data);
            }

            // hd_print_r("data depois de current", $data);

            if (isset($data->attributes()->{$field[1]}) && strstr($data->attributes()->{$field[1]}, "/")){
                $field_1 = sprintf("%s://%s:%s%s", $parsedPath["scheme"],$parsedPath["host"],$parsedPath["port"],$data->attributes()->{$field[1]});
            } else {
                 $field_1 =$data->attributes()->{$field[1]};
            }
          }

          if (isset($item) &&  isset($item->attributes()->{$field[1]})  && strstr($item->attributes()->{$field[1]}, "/") ){
                $field_1 = sprintf("%s://%s:%s%s", $parsedPath["scheme"],$parsedPath["host"],$parsedPath["port"],$item->attributes()->{$field[1]});

          }else {
                if (isset($item))
                    $field_1 =  $item->attributes()->{$field[1]};
          }

            hd_print("field_1 = $field_1");

            if ($field[0] === "plex_field"){
                if (!isset($field_1)) continue;
                $ret = $field_1;
            } else if ($field[0] === "plex_thumb_field") {
                if (!isset($field_1)) continue;
                $ret = Client::getInstance()->getThumbUrl($field_1, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
            } else  if ($field[0] === "plex_image_field"){
                if (!isset($field_1)) continue;
                $ret = Client::getInstance()->getUrl($currentPath, $field_1);
            }
            if (isset($item)){

				if ($field[0] === "plex_thumb_item_field") {
	                if (!isset($field_1)) continue;
	                $ret = Client::getInstance()->getThumbUrl($field_1, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
	            } else  if ($field[0] === "plex_image_item_field"){
                    if (!isset($field_1)) continue;
	                $ret = Client::getInstance()->getUrl($currentPath, $field_1);
                    // hd_print("name $name, item $ret currentPath $currentPath" );
	            } else if ($field[0] === "plex_item_field"){
	                if (!isset($field_1)) continue;
	                $ret = $field_1;
	            } else if ($field[0] === "xpath"){
                    $ret = "teste";
                    //TODO: implement xpath replacement
                }
	        }

            if (strstr($field[0], "thumb")){
                $ret = $this->cachemanager->addSession($ret);
                $ret = $ret;
            }
	        if (isset($ret)){
                $a = gettype($ret) == "object" ? TranslationManager::getInstance()->getTranslation((string)$ret):TranslationManager::getInstance()->getTranslation($ret);
                // hd_print("returning plex_fiel value $a");
	        	return $a;
	        }
        }
	}

    public function getData(){
        $a = array($this->path => $this->data);
        foreach ($this->extraData as $key => $value) {
            $a[$key]  = $value;
        }
        // hd_print_r("data = ", $a);
        return $a;

    	// return $this->data;
    }

    public function getMediaUrl($data){
    	return Client::getInstance()->getUrl($this->path , (string)$this->data->attributes()->key);
    }

}


 ?>