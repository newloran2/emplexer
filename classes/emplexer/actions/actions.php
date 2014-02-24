<?php


class Actions{

    /**
     * that function make close_dialog_and_run handler and set a static method name to call on handler_user_input
     * @param  string $name  ClassName::method
     */
    public function closeAndRunThisStaticMethod($name, $extraParams=null){
        $postAction = array("post_action" => array(
                        GuiAction::handler_string_id => PLUGIN_HANDLE_USER_INPUT_ACTION_ID,
                        GuiAction::params =>  array(
                            'type'=>__FUNCTION__,
                            'method' => $name,
                        ),
                    )
                );

        // hd_print_r("postAction", $postAction);
        foreach ($extraParams as $key => $value) {
            $postAction['post_action']['params'][$key] = $value;
        }
        return array(
            GuiAction::handler_string_id => CLOSE_AND_RUN_ACTION_ID,
            GuiAction::caption =>  null,
            GuiAction::data => $postAction

        );
    }
}


?>