<?php

/**
 * Class GCompControlWindow
 * @author Newloran2
 */
class GCompControlWindow extends GcompAbstractGuiControl
{
    protected $backgroundColor;
    protected $backgroundUrl;

    public function __construct($backgroundUrl = null, $backgroundColor = null)
    {
        $this->backgroundColor=$backgroundColor;
        $this->backgroundUrl = $backgroundUrl;
    }
}

