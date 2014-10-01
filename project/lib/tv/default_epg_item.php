<?php


require_once 'lib/tv/epg_item.php';



class DefaultEpgItem implements EpgItem
{
    protected $_title;
    protected $_description;
    protected $_start_time;
    protected $_finish_time;

    public function __construct($title, $description, $start_time, $finish_time)
    {
        $this->_title = $title;
        $this->_description = $description;
        $this->_start_time = $start_time;
        $this->_finish_time = $finish_time;
    }

    public function get_title()
    { return $this->_title; }

    public function get_description()
    { return $this->_description; }

    public function get_start_time()
    { return $this->_start_time; }

    public function get_finish_time()
    { return $this->_finish_time; }
}


?>
