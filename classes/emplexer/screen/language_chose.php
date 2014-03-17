<?php

class LanguageChose extends BaseConfigScreen {

    public function __construct(){
        $this->addControl(new GuiControlButton('save', 'save'));
    }

}
