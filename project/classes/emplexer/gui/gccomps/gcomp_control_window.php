<?php

/**
 * Class GCompControlWindow
 * @author Newloran2
 */
class GCompControlWindow extends GcompControlContainer
{
    protected $backgroundColor;
    protected $backgroundUrl;
    protected $previousScreen;

    public function __construct($backgroundUrl = null, $backgroundColor = null)
    {
        parent::__construct();
        $this->backgroundColor = $backgroundColor;
        $this->backgroundUrl   = $backgroundUrl;
        $this->previousScreen = "main";
    }
    public static function testeGompWindow(){
        hd_print("que maravilha funcionou");
    }
    public function generate()
    {
        $a= parent::generate();
        $res = array(
            'view_kind'=> 'view_gcomps',
            'data'=> array(
                'actions' => array(
                    'key_enter' => Actions::runThisStaticMethod('GcompControlWindow::testeGompWindow'),
                    'key_return'=> ActionFactory::open_folder($this->previousScreen)
                ),
                'window_def'=> array(
                    'background_url'=> $this->backgroundUrl,
                    'comp_defs'=> $a
                )
            )
        );


        return $res;
    }

    public function setPreviousScreen($screenName){
        $this->previousScreen = $screenName;
    }
}

