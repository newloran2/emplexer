<?php

namespace lib\dune;

interface UserInputHandler
{
    public function get_handler_id();

    public function handle_user_input(&$user_input, &$plugin_cookies);
}


?>
