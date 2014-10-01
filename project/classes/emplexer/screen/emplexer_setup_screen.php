<?php

class EmplexerSetupScreen implements ScreenInterface, TemplateCallbackInterface
{
    protected $data;
    public function __construct()
    {
       $this->data = array(
            'Nfs Setup'       => 'nfs',
            _('Language')     => 'language',
            _('Media Server') => 'mediaServer'
       );
    }
    public function generateScreen(){
        $a = TemplateManager::getInstance()->getTemplate("nfsScreen", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
        $actions = array();
        $actions[GUI_EVENT_KEY_ENTER] = Actions::runThisStaticMethod('EmplexerSetupScreen::chose');
        $a['data']['actions'] = $actions;
        return $a;
    }

    public static function chose($user_input){
         return ActionFactory::open_folder($user_input->selected_media_url);
    }

    public function getField($key, $name, $field=null){
        hd_print(__METHOD__ . " name = $key, item =$item , field = $field");
        if (gettype($key) !=  "string"){
            return $key;
        }
        if ($key == "{{key}}"){
            return array_search($name, $this->data);
        } else if ($key  == "{{value}}"){
            return $name;
        } else {
            return $key;
        }
	}

    public function getData(){
        return $this->data;
	}

    public function getMediaUrl($data){
        return null;
	}

}
