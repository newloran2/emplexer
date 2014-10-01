<?php


interface EpgItem
{
    public function get_title();
    // Return string.

    public function get_description();
    // Return string.

    public function get_start_time();
    // Return int -- UNIX time.

    public function get_finish_time();
    // Return int -- UNIX time.
}


?>
