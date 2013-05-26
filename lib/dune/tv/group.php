<?php

namespace lib\dune\tv;

interface Group
{
    public function get_id();
    public function get_title();
    public function get_icon_url();

    public function is_favorite_channels();
    public function is_all_channels();

    public function get_channels(&$plugin_cookies); // Array<Channel>
}


?>
