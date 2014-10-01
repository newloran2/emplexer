<?php


require_once 'lib/regular_screen.php';

abstract class AbstractRegularScreen implements RegularScreen
{
    private $id;

    private $folder_views;
    private $folder_view_index_attr_name;

    

    protected function __construct($id, $folder_views)
    {
        $this->id = $id;

        $this->folder_views = $folder_views;
        $this->set_default_folder_view_index_attr_name();
    }

    

    public function __get($name){
        //hd_print($name);
        if(property_exists(get_class($this), $name)){
            return $this->{$name};
        }
    }

    public function __set($name, $value){
        $this->{$name} = $value;
        //hd_print("$name=$name value=". is_array($value)? print_r($value, true) : $value);
        
        if ($name == "folder_views" ){
            //hd_print("folder_view");
            $this->set_default_folder_view_index_attr_name();
        }
    }

    protected function set_folder_view_index_attr_name($s)
    {
        $this->folder_view_index_attr_name = $s;
    }

    protected function set_default_folder_view_index_attr_name()
    {
        $this->folder_view_index_attr_name = "screen." . $this->id . ".view_idx";
    }

    

    public function get_id()
    { return $this->id; }

    

    public function get_folder_view(MediaURL $media_url, &$plugin_cookies)
    {
        // //hd_print("----> count: " . count($this->folder_views));

        $idx = $this->get_folder_view_index($plugin_cookies);

        // //hd_print("----> idx: $idx");

        $folder_view = $this->folder_views[$idx];

        $folder_view[PluginRegularFolderView::actions] =
            $this->get_action_map($media_url, $plugin_cookies);

        $folder_view[PluginRegularFolderView::initial_range] = 
            $this->get_folder_range($media_url, 0, $plugin_cookies);

        $archive = $this->get_archive($media_url);
        $archive_def = is_null($archive) ? null :
            $archive->get_archive_def();
        return  array
        (
            PluginFolderView::multiple_views_supported  => (count($this->folder_views) > 1 ? 1 : 0),
            PluginFolderView::archive                   => $archive_def,
            PluginFolderView::view_kind                 => PLUGIN_FOLDER_VIEW_REGULAR,
            PluginFolderView::data                      => $folder_view
        );
    }

    

    public function get_next_folder_view(MediaURL $media_url, &$plugin_cookies)
    {
        $idx = $this->get_folder_view_index($plugin_cookies);

        ++$idx;

        if ($idx >= count($this->folder_views))
            $idx = 0;

        $plugin_cookies->{$this->folder_view_index_attr_name} = $idx;

        return $this->get_folder_view($media_url, $plugin_cookies);
    }

    

    private function get_folder_view_index(&$plugin_cookies)
    {
        if (!isset($plugin_cookies->{$this->folder_view_index_attr_name}))
            return 0;

        $idx = $plugin_cookies->{$this->folder_view_index_attr_name};
        
        $cnt = count($this->folder_views);

        if ($idx < 0)
            $idx = 0;
        else if ($idx >= $cnt)
            $idx = $cnt - 1;

        return intval($idx);
    }

    

    public function get_archive(MediaURL $media_url)
    { 
        return null; 
    }
}


?>
