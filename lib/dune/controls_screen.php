<?php


// require_once 'lib/screen.php';
// require_once 'lib/user_input_handler.php';
namespace lib\dune;

use lib\dune\MediaURL;
use lib\dune\Screen;
use lib\dune\UserInputHandler;

interface ControlsScreen extends Screen, UserInputHandler
{
    public function get_control_defs(MediaURL $media_url, &$plugin_cookies);
}


?>
