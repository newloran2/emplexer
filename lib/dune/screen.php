<?php

namespace lib\dune;
interface Screen
{
    public function get_id();
    public function get_folder_view(MediaURL $media_url, &$plugin_cookies);
}


?>
