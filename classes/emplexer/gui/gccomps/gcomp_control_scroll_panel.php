
<?php


/**
 * Class GCCompControlScrollPanel
 * @author Newloran2
 */
class GGCCompControlScrollPanel extends GCompControlPanel
{
    protected $nativeHeight;
    protected $nativeWidth;


    public function __construct($controls=null,$nativeWidth=1920, $nativeHeight=1080)
    {
        parant::__construct($controls);
        $this->nativeHeight = $nativeHeight;
        $this->nativeWidth  = $nativeWidth;
    }

    public function generate(){
        return array(
            "panel"         => parent::generate(),
            "native_width"  => $this->nativeWidth,
            "native_height" => $this->native_height
        );
    }
}


