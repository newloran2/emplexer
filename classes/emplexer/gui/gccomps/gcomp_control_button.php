<?php
/**
 * Class GCompControlButton
 * @author Newloran2
 */
class GCompControlButton
{
    protected $caption;
    protected $selected;
    protected $captionCentered;

    public function __construct($caption, $selected=false, $captionCentered=true)
    {
        $this->caption          = $caption;
        $this->selected         = $selected;
        $this->caption_centered = $captionCentered;
    }

    public function getControlType(){
        return "button";
    }

    public function getSpecificDef(){
        return array(
            "caption" => $this->caption,
            "caption_centered" => $this->caption_centered,
            "selected" => $this->selected
        );
    }
}

