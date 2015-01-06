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

    private $reload =false; //if true reload the template on every call
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
        HD::print_backtrace();
        $this->mixTemplate();
    }

    // mix all templates with their inheritances in a single json. 
    protected function mixTemplate(){
        $a = array();
        //mix all external inheritance
        foreach($this->templateJson as $key => $value ){
            $mix = array();
            array_unshift($mix, $value['templates']);
            $current = $this->templateJson[$key];
            while (isset($current['inherits'])){
                $b = $this->templateJson[$current['inherits']]['templates'];
                array_unshift($mix, $b);
                $current = $this->templateJson[$current['inherits']];
            }
            $mix = array_replace_recursive($mix);
            foreach($mix as $k => $v){
                foreach($v as $ki => $vi) {
                    $a[$key]['templates'][$ki] = $vi;    
                }
            }
        }

        //mix all internal inheritance
        $mix =array();
        foreach (array_reverse($a) as $key => $value) {
            $b=array();
            foreach ($value['templates'] as $k => $v) {
                $teste = array();
                array_unshift($teste, $v); 
                $current = isset($v['inherits']) ? $value['templates'][$v['inherits']] : null;
                while(isset($current)){
                    array_unshift($teste,$current);
                    $current = isset($current['inherits']) ? $value['templates'][$current['inherits']] : null;
                }
                $mix[$key]['templates'][$k]=call_user_func_array('array_replace_recursive', $teste);
            }
        }
        $this->templateJson = $mix;        
        

        
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
        //if template are not seted the base are used instead
        if (!isset($this->templateJson[$n])){
            $n="base";
        }
        $currentTemplate = Config::getInstance()->${n};

        /*
          This a complicated block.
          To do the inherits behaviour i append all inherits tree in the arrays variable.
          that append is in reverse order becouse the last element replace the previous.
          eg:
          "base":{
          "name": "emplexer"
          "age": 1
          }

          "child":{
          "inherits":"base",
          "name": "emplexer2"
          "age": 2
          }

          "grandson":{
          "inherits":"child",
          "name": "emplexer3"
          "age": 1
          }

          at the end of the parser the name field will be emplexer3 because the grandson inherits child and override the field name of the chield and chield
          inherits the base and override the name field.

          This behaviour are done recursively in all elements
        */
          
        do{
            array_unshift($arrays, $this->templateJson[$n]);
            if (isset($this->templateJson[$n]['inherits']) && (!isset($this->templateJson[$n][$tag]['unset']) || !$this->templateJson[$n][$tag]['unset'])){
                $n= $this->templateJson[$n]['inherits'];
            } else {
                $n= null;
            }

        }while (isset($n));
        $a = call_user_func_array('array_replace_recursive', $arrays);
        $arrays = array();
        array_unshift($arrays, $inherits);
        
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
        $currentTemplate = Config::getInstance()->${template};
        $t = $this->templateJson[$template]['templates'][$currentTemplate];
        array_walk_recursive($t,array($this, 'walk'), array($getFieldCallBack, $item, $json));
        $a= isset($t[$tag]) ?  $t[$tag] : null;
        return $a;
    }

    public function getTemplate($name, $getMediaUrlCallback, $getDataCallback, $getFieldCallBack){
        $currentTemplate = Config::getInstance()->${name};
        $itens       = array();
        $folderItems = array();
        $data        = call_user_func($getDataCallback);
        
        foreach ( $data as $item)
        {
            $folderItems[] =  $this->getTag($name, "items",  $getFieldCallBack, $item, $data);
        }

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
            PluginFolderView::multiple_views_supported => count($this->templateJson[$currentTemplate]['templates']) > 1 ? 1 : 0,
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



    public function countTemplatesByType($type){
        return count($this->templateJson[$type]['templates']);
    }

    public function setNextTemplateByType($type){
        foreach ($this->templateJson[$type]['templates'] as $key => $value){
        }
    }
    
}

?>
