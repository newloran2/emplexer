<?php
/**
 * Class GCompControlButton
 * @author Newloran2
 */
class GCompControlButton extends GcompAbstractGuiControl
{
    protected $caption;
    protected $selected;
    protected $captionCentered;

    // public function __construct($caption, $selected=false, $captionCentered=true)
    public function __construct($representation)
    {
        parent::__construct($representation);
        $this->caption          = $representation['caption'];
        $this->selected         = $representation['selected'];
        $this->captionCentered = $representation['captionCentered'];
    }

    public static function initWithRepresentation($representation){
        if (!GCompControlButton::isValid($representation)) return null;
        return new GcompControlButton($representation);
    }
    public static function isValid($representation){
        if (!GcompAbstractGuiControl::isValid($representation)){
            return false;
        }
        if (!validadeArrayTypes(array('caption' => 'string', 'selected' => 'boolean', 'captionCentered'=> 'boolean'), $representation)) {
            hd_print('alguma coisa no button não é valido');
            return false;
        }
        hd_print('button valido');
        return true;
    }
    public function getControlType(){
        return "button";
    }

    public function getSpecificDef(){
        return array(
            "caption" => $this->caption,
            "caption_centered" => $this->captionCentered,
            "selected" => $this->selected
        );
    }
}

