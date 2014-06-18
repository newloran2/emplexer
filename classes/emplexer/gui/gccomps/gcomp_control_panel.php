<?php

/**
 * Class GCompControlPanel
 * @author Newloran2
 */
class GCompControlPanel extends GCompControlWindow
{
    public function __construct($controls=null)
    {
        $this->controls= $controls;
    }

    public function generate(){
        return array(
                "children"=> $this->controls
                );
    }
}



