<?php
/**
 * @author Erik Clemente (aka newloran2)
 */

/**
* Singleton to manage emplexer templates
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
    }

    protected function reloadTemplate(){
        $this->templateJson = json_decode(file_get_contents($this->templateFile));
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


    private function getTag($template, $tag, $currentPath, &$data){
        if (!isset($this->templateJson->{$template})){
            $template = "base";
        }
        $temp = $this->templateJson->{$template};
        $ret =  array();
        $unset = isset($temp->{$tag}->unset) && $temp->{$tag}->unset == true ;
        if (isset($temp->inherits) && !$unset){
            $ret = $this->getTag($temp->inherits, $tag, $currentPath, $data);
        }
        if (isset($temp->{$tag})){
            foreach ($temp->{$tag} as $key => $value) {
                if ($key  !== "unset"){
                    $ret[$key]  = $value;
                }
            }
        }
        return $ret;


    }

    public function getTemplate($name, $currentPath, &$data){
        $itens = array();
        $folderItems = array();
        foreach ($data as $item)
        {
            $viewItemParams = array();
            if (isset($this->templateJson->base->items->view_item_params)){
                foreach ($this->templateJson->base->items->view_item_params as $key => $value) {
                    $viewItemParams[$key] = $this->getPlexField($value, $currentPath,   $item, $data);
                }
            }
            $folderItems[] = array(
                PluginRegularFolderItem::media_url          => Client::getInstance()->getUrl($currentPath , (string)$item->attributes()->key),
                PluginRegularFolderItem::caption            => $this->getPlexField($this->templateJson->base->items->caption, $currentPath,  $item, $data) ,
                PluginRegularFolderItem::view_item_params  => $viewItemParams
            );
        }

        $availableTemplates = array(
            PluginRegularFolderView::async_icon_loading             => true,
            PluginRegularFolderView::initial_range                  =>
            array(
                PluginRegularFolderRange::items                         =>  $folderItems,
                PluginRegularFolderRange::total                         =>  count($folderItems),
                PluginRegularFolderRange::count                         =>  count($folderItems),
                PluginRegularFolderRange::more_items_available          =>  false,
                PluginRegularFolderRange::from_ndx                      =>  0
                ),
            PluginRegularFolderView::view_params                    => $this->getTag($name, "view_params",  $currentPath, $data),
            PluginRegularFolderView::base_view_item_params          => $this->getTag($name, "base_view_item_params",  $currentPath, $data),
            PluginRegularFolderView::not_loaded_view_item_params    => $this->getTag($name, "not_loaded_view_item_params",  $currentPath, $data),
        );

        $a = array(
            PluginFolderView::view_kind                             =>  PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data                                  => $availableTemplates
        );
        return $a;

    }



    /**
     * parse plex_fields from emplexer template
     *
     * @param  string $field
     * @param  string $currentPath
     * @param  array $item
     * @param  array $data
     * @return string
     */
    public function getPlexField($field, $currentPath, &$item, &$data){
        $fields = explode("||", $field);
        foreach ($fields as $value) {
            $field =  explode(":", $value);
            if (count($field) <=1){
              return $field[0];
          }

          // var_dump($fields);
            if ($field[0] === "plex_field"){
                if (!isset($data->attributes()->{$field[1]})) continue;
                $ret = $data->attributes()->{$field[1]};
            } else if ($field[0] === "plex_thumb_field") {
                // var_dump($data->attributes()->{$field[1]});
                if (!isset($data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getThumbUrl($data->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
            } else  if ($field[0] === "plex_image_field"){
                if (!isset($data->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getUrl($currentPath, $data->attributes()->{$field[1]});
            } else if ($field[0] === "plex_thumb_item_field") {
                // var_dump($data->attributes()->{$field[1]});
                if (!isset($item->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getThumbUrl($item->attributes()->{$field[1]}, isset($field[2])? $field[2]:null, isset($field[3])? $field[3]:null);
            } else  if ($field[0] === "plex_image_item_field"){
                if (!isset($item->attributes()->{$field[1]})) continue;
                $ret = Client::getInstance()->getUrl($currentPath, $item->attributes()->{$field[1]});
            } else if ($field[0] === "plex_item_field"){
                if (!isset($item->attributes()->{$field[1]})) continue;
                $ret = $item->attributes()->{$field[1]};
            }
            if (isset($ret)){
                return (string)$ret;
            }
        }
    }

}

?>