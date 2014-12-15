<?php
/**
 * @author Erik Clemente (aka newloran2)
 */

/**
* Singleton to manage emplexer templates
{"input_data":{"media_url":"movieInfo||http://192.168.2.8:32400/library/metadata/32309?checkFiles=1&includeExtras=1"},"plugin_cookies":{"plexIp":"192.168.2.8","plexPort":"32400", "nfsIps": "192.168.2.9"},"op_type_code":"get_folder_view","op_id":"2"}
*/
class TemplateManager
{

    private $reload =true; //if true reload the template on every call
    private $templateFile ;
    private $templateJson;
    private static $instance;

    private function __construct()
    {
        $this->templateFile = ROOT_PATH . "/templates/default.json" ;
        $this->reloadTemplate();
    }

    protected function reloadTemplate(){
        $this->templateJson = json_decode(file_get_contents($this->templateFile), true);
        /* hd_print_r("templatePath =" . $this->templateFile, $this->templateJson); */		
     }


    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance =  new TemplateManager();
        }
        if (self::$instance->reload) {
            self::$instance->reloadTemplate();
        }

        return self::$instance;
    }

    private function getTag1($template, $tag){
        $arrays = array();
        $n = $template;
        if (!isset($this->templateJson[$n])){
            $n="base";
        }

        do{
            array_unshift($arrays, $this->templateJson[$n]);
            if (isset($this->templateJson[$n]['inherits']) && (!isset($this->templateJson[$n][$tag]['unset']) || !$this->templateJson[$n][$tag]['unset'])){
                $n= $this->templateJson[$n]['inherits'];
            } else {
                $n= null;
            }
        }while (isset($n));
        $a = call_user_func_array('array_replace_recursive', $arrays);
        return $a;

    }


    private function walk(&$item, $key, $data){
        $getFieldCallBack = $data[0];
        $upItem           = $data[1];
        $json             = $data[2];
        $item             = call_user_func_array($getFieldCallBack, array($item,$upItem , $key));
    }

    private function getTag($template, $tag, $getFieldCallBack, &$item = null,  $json=null){
        $t = $this->getTag1($template, $tag);
        //hd_print_r(__METHOD__ . " $template 1", $t);
        array_walk_recursive($t,array($this, 'walk'), array($getFieldCallBack, $item, $json));
        //hd_print_r(__METHOD__ . " $template 2", $t);
        return isset($t[$tag]) ?  $t[$tag] : null;
    }

    public function getTemplate($name, $getMediaUrlCallback, $getDataCallback, $getFieldCallBack){

        $itens       = array();
        $folderItems = array();
        $data        = call_user_func($getDataCallback);

        foreach ( $data as $item)
        {
            $folderItems[] =  $this->getTag($name, "items",  $getFieldCallBack, $item, $data);
        }


        // foreach ( $data as $key => $xml)
        // {
        //     //hd_print("key = $key");
        //     foreach ($xml as $item) {
        //         $folderItems[] =  $this->getTag($name, "items",  $getFieldCallBack, $item, array($key=>$xml));
        //     }

        // }

        // var_dump($this->templateJson[$name]['async_icon_loading']);
        $async_icon_loading = isset($this->templateJson[$name]['async_icon_loading'])? $this->templateJson[$name]['async_icon_loading']  : true;
        $availableTemplates = array(
            PluginRegularFolderView::async_icon_loading          => $async_icon_loading,
            PluginRegularFolderView::initial_range               =>
            array(
                PluginRegularFolderRange::items                  => $folderItems,
                PluginRegularFolderRange::total                  => count($folderItems),
                PluginRegularFolderRange::count                  => count($folderItems),
                PluginRegularFolderRange::more_items_available   => false,
                PluginRegularFolderRange::from_ndx               => 0
                ),
            PluginRegularFolderView::view_params                 => $this->getTag($name, "view_params",  $getFieldCallBack),
            PluginRegularFolderView::base_view_item_params       => $this->getTag($name, "base_view_item_params",  $getFieldCallBack),
            PluginRegularFolderView::not_loaded_view_item_params => $this->getTag($name, "not_loaded_view_item_params",  $getFieldCallBack),
        );

        $a = array(
            PluginFolderView::view_kind => PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data      => $availableTemplates
        );
        return $a;
    }

    public function getMovieInfoTemplate($name, $getMediaUrlCallback, $getDataCallback, $getFieldCallBack){
        $external =  $this->getTag($name, "external", $getFieldCallBack);
        $params =  $this->getTag($name, "params", $getFieldCallBack);
        $movie =  $this->getTag($name, "movie", $getFieldCallBack);
        
        $template = $external;
        $template['params'] = $params;
        $template['movie'] = $movie;
        $ret = array(
            PluginFolderView::view_kind => PLUGIN_FOLDER_VIEW_MOVIE,
            PluginFolderView::multiple_views_supported => false,
            PluginFolderView::data      => $template        
        );
        return $ret;
    }


}

?>
