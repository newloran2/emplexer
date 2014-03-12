<?php

/**
 * 
 */
class PlexSectionPopupMenu 
{
    
    private $popup;
    private $xml;
    private $url;
    private $action;
    public function __construct($action)
    {
        $this->popup= new PopupMenu();
        $this->url = sprintf("http://%s:%s/library/sections", Config::getInstance()->plexIp, Config::getInstance()->plexPort);
        $this->xml =Client::getInstance()->getAndParse($this->url);
        $this->action = $action;
    }

    public function generate(){
        foreach ($this->xml as $dir){ 
            $menuItem = new GuiControlMenuItem((string)$dir->attributes()->title, null);
            $menuLocations =new PopupMenu();
            foreach($dir as $location){
                $menuLocations->addItem(new GuiControlMenuItem((string)$location->attributes()->path, $this->action));
            }
            $menuItem->setAction($menuLocations->generate());
            $this->popup->addItem($menuItem);
             
        }
        return $this->popup->generate();
    }
}
