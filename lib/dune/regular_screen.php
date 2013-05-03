<?php


// require_once 'lib/screen.php';

interface RegularScreen extends Screen
{
    public function get_action_map(MediaURL $media_url, &$plugin_cookies);
    public function get_folder_range(MediaURL $media_url, $from_ndx,
        &$plugin_cookies);

    public function get_next_folder_view(MediaURL $media_url, &$plugin_cookies);

    public function get_archive(MediaURL $media_url);
}


?>
