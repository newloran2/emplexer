<?php


// require_once 'lib/abstract_regular_screen.php';

abstract class AbstractPreloadedRegularScreen
    extends AbstractRegularScreen
{    
    protected function __construct($id, $folder_views)
    {
        parent::__construct($id, $folder_views);
    }

    public abstract function get_all_folder_items(
        MediaURL $media_url, &$plugin_cookies);

    public function get_folder_range(MediaURL $media_url, $from_ndx,
        &$plugin_cookies)
    {
        return HD::create_regular_folder_range(
            $this->get_all_folder_items($media_url, &$plugin_cookies));
    }
}


?>
