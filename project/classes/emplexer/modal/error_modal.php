<?php

class ErrorModal extends Modal {
    function __construct(Exception $e, $title = "Unhandled PHP plugin error"){
         parent::__construct($title);
         $this->addControl(new GuiControlText(null, null, $e->getMessage()));
         foreach(debug_backtrace() as $f){
             $text = sprintf("%s at %s:%s", $f['function'], basename($f['file']), $f['line']);
             $this->addControl(new GuiControlText(null, null, $text));
         }
    }
}