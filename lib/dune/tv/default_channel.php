<?php


require_once 'lib/tv/channel.php';



class DefaultChannel implements Channel
{
    protected $_id;
    protected $_title;
    protected $_icon_url;
    protected $_streaming_url;

    protected $_groups; // Array<Group>

    public function __construct($id, $title, $icon_url, $streaming_url)
    {
        $this->_id = $id;
        $this->_title = $title;
        $this->_icon_url = $icon_url;
        $this->_streaming_url = $streaming_url;

        $this->_groups = array();
    }

    public function get_id()
    { return $this->_id; }

    public function get_title()
    { return $this->_title; }

    public function get_icon_url()
    { return $this->_icon_url; }

    public function get_groups()
    { return $this->_groups; }

    public function get_number()
    { return -1; }

    public function has_archive()
    { return false; }

    public function is_protected()
    { return false; }

    public function get_past_epg_days()
    { return 14; }

    public function get_future_epg_days()
    { return 7; }

    public function get_archive_past_sec()
    { return 14 * 86400; }

    public function get_archive_delay_sec()
    { return 31 * 60; }

    public function get_buffering_ms()
    { return 0; }

    public function get_timeshift_hours()
    { return 0; }

    public function get_streaming_url()
    { return $this->_streaming_url; }

    

    public function add_group($group)
    { $this->_groups[] = $group; }
}


?>
