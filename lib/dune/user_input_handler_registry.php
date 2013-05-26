<?php


// require_once('lib/user_input_handler.php');

namespace lib\dune;

use GuiAction;

class UserInputHandlerRegistry
{
    private static $instance = null;

    public static function get_instance()
    {
        if (is_null(self::$instance))
            self::$instance = new UserInputHandlerRegistry();
        return self::$instance;
    }

    public static function create_action(UserInputHandler $handler,
        $name, $add_params = null, $caption=null )
    {
        $params = array(
            'handler_id' => $handler->get_handler_id(),
            'control_id' => $name);
        if (isset($add_params))
            $params = array_merge($params, $add_params);

        return array
        (
            GuiAction::handler_string_id => PLUGIN_HANDLE_USER_INPUT_ACTION_ID,
            GuiAction::caption => $caption,
            GuiAction::data => null,
            GuiAction::params => $params,
        );
    }

    
    

    private $handlers;

    private function __construct()
    {
        $this->handlers = array();
    }

    public function handle_user_input(&$user_input, &$plugin_cookies)
    {
        if (!isset($user_input->handler_id))
            return null;

        $handler_id = $user_input->handler_id;
        if (!isset($this->handlers[$handler_id]))
            return null;

        return $this->handlers[$handler_id]->handle_user_input(
            $user_input, $plugin_cookies);
    }

    public function register_handler(UserInputHandler $handler)
    {
        $handler_id = $handler->get_handler_id();
        $this->handlers[$handler_id] = $handler;
    }
}


?>
