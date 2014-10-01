<?php 

/**
 * Class GcompControlLabel
 * @author Newloran2
 */
class GcompControlLabel extends GcompAbstractGuiControl
{
    protected $text;
    protected $maxNumLines=1;
    protected $fontSize=1;
    protected $textColor="white";
    public function __construct($text, $maxNumLines=1, $fontSize=1, $textColor="white")
    {
        parent::__construct();
        $this->text= $text;
        $this->maxNumLines = $maxNumLines;
        $this->fontSize = $fontSize;
        $this->textColor = $textColor;
    }
    public function getControlType(){
        return "label"; 
    }
    public function getSpecificDef(){
        return array(
            'text'=> $this->text,
            'font_size'=> $this->fontSize,
            'text_color'=> $this->textColor,
            'max_num_lines'=> $this->maxNumLines
        ); 
    }
}
