<?php 

/**
* 
*/	
class BaseView
{
	
	protected $sections;

	public function __construct(Plex_Server $mainServer)
	{
		$this->sections = $mainServer->getLibrary()->getSections();		
	}


    public function get_folder_view($media_url, &$plugin_cookies) {

        hd_print(__METHOD__ . $media_url);

        $items       = $this->get_folder_range($media_url, 0, $plugin_cookies);
        // $actions     = $this->get_actions_map($media_url, $plugin_cookies);

        $view_params = $this->get_default_view_params($media_url, $plugin_cookies);
        $base_params = $this->get_default_base_params($media_url, $plugin_cookies);

        $data = array(
            // PluginRegularFolderView::actions               => $actions,
            PluginRegularFolderView::initial_range         => $items,
            PluginRegularFolderView::view_params           => $view_params,
            PluginRegularFolderView::async_icon_loading    => false,
            PluginRegularFolderView::base_view_item_params => $base_params,
            PluginRegularFolderView::not_loaded_view_item_params => array(),
        );

        $async_icon = $this->get_async_icon($media_url, $plugin_cookies);
        if (! empty($async_icon)) {
            $data[PluginRegularFolderView::async_icon_loading] = true;
            $date[PluginRegularFolderView::not_loaded_view_item_params] =
                array(ViewItemParams::icon_path => $async_icon);
        }

        $multiple = count($this->object->getSupportedTemplates()) > 1;
        $res = array(
            PluginFolderView::view_kind                 => PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::multiple_views_supported  => $multiple,
            PluginFolderView::data                      => $data);
        hd_print('--> Default get_folder_view returning: ' . print_r($res, true));
        return $res;
    }



     protected function get_folder_range($media_url, $from_ndx, &$plugin_cookies) {
     	$items = array();
     	
        
        foreach ($this->object->getItems() as $item) {
            $title = stripslashes('' . $item->get(Item::TITLE));
            $thumb = $item->get(Item::THUMBNAIL);
            $thumb = empty($thumb) ? $this->object->get(Channel::IMAGE) : $thumb;
            $descr = stripslashes('' . $this->prepare_description($item));
            $link  = $this->resolve_link($item);

            $vip = array(
                ViewItemParams::icon_path          => '' . $thumb,
                ViewItemParams::item_detailed_info => '' . $descr);
            $items[] = array(
                PluginRegularFolderItem::media_url        => $link,
                PluginRegularFolderItem::caption          => $title,
                PluginRegularFolderItem::view_item_params => $vip);
        }
        return $this->pack_items_range($items);
    }



}


 ?>