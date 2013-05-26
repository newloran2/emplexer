<?php
namespace lib\dune\tv;

interface Channel
{
    public function get_id();
    public function get_title();
    public function get_icon_url();

    public function get_groups(); // Array<Group>

    public function has_archive();
    public function is_protected();
    public function get_past_epg_days();
    public function get_future_epg_days();
    public function get_archive_past_sec();
    public function get_archive_delay_sec();
    public function get_buffering_ms();
    public function get_timeshift_hours();

    public function get_streaming_url();
}


?>
