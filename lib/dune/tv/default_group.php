<?php


// require_once 'lib/tv/group.php';



class DefaultGroup implements Group
{
    protected $_id;
    protected $_title;
    protected $_icon_url;

    protected $_channels;

    public function __construct($id, $title, $icon_url)
    {
        if (is_null($icon_url))
            $icon_url = 'gui_skin://small_icons/iptv.aai';

        $this->_id = $id;
        $this->_title = $title;
        $this->_icon_url = $icon_url;

        $this->_channels = new HashedArray();
    }

    public function get_id()
    { return $this->_id; }

    public function get_title()
    { return $this->_title; }

    public function get_icon_url()
    { return $this->_icon_url; }

    public function is_favorite_channels()
    { return false; }

    public function is_all_channels()
    { return false; }

    public function get_channels(&$plugin_cookies)
    { return $this->_channels; }

    public function add_channel($c)
    { $this->_channels->put($c); }
}


?>
