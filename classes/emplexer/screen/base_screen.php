	<?php

/**
* Base Menu class for emplexer
*/

abstract class BaseScreen implements TemplateCallbackInterface
{

	protected $iconFile = "gui_skin://small_icons/folder.aai";
	protected $openFolder = PLUGIN_OPEN_FOLDER_ACTION_ID;
	protected $handlerUserInput = PLUGIN_HANDLE_USER_INPUT_ACTION_ID;
	protected $path;
	protected $data;
	protected $nextTemplate = false;
	protected $templates =array();

	function __construct($key=null, $nextTemplate=false) {
		if (!$key || $key === 'main')
			$key = '/library/sections';

		$this->path = Client::getInstance()->getUrl(null, $key);
		$this->data = Client::getInstance()->getAndParse($this->path);
		$this->nextTemplate = $nextTemplate != null ? $nextTemplate :  array();

		$this->templates = json_decode(Config::getInstance()->templateViewNumber);

	}

	public function getTemplateByType($type){
		hd_print(__METHOD__);
		$fun = 'template'.ucwords($type);
		return $this->$fun();
	}

	private function getTemplateIndexAndUpdate($key){
		hd_print(__METHOD__);
		$templateIndex = Config::getInstance()->templateViewNumber;
		$templateIndex != null ? json_decode($templateIndex) :  array();
		$index = isset($templateIndex->{$key}) ? (int)$templateIndex->{$key}+1 : 1;
		$templateIndex->{$key} = $index;
		Config::getInstance()->templateViewNumber = json_encode($templateIndex);

		return $index;

	}
	public function callback($name, $currentPath, &$data){
		hd_print(__METHOD__);
		var_dump($data);
	}
	protected function template(){
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("base", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}
	protected function templateSecondary(){
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("secondary", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}
	protected function templateMovie(){
		hd_print(__METHOD__);

		$a = TemplateManager::getInstance()->getTemplate("movie", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->handlerUserInput));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateShow(){
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("show", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateSeason(){
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("season", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateEpisode(){
		hd_print(__METHOD__);
		// var_dump($this->data);
		$a = TemplateManager::getInstance()->getTemplate("episode", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(
			GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->handlerUserInput),
			GUI_EVENT_KEY_PLAY =>  array(
				GuiAction::handler_string_id => $this->handlerUserInput,
				GuiAction::params => array(
					"function"=> "playAll||". $this->data->attributes()->key
				)
			),
            GUI_EVENT_KEY_POPUP_MENU => Actions::runThisStaticMethod("PlexScreen::showpopup")
		);
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateArtist(){
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("artist", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}


	protected function templateAlbum()
	{
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("album", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;

	}
	protected function templateTrack()
	{
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("track", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->handlerUserInput));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templatePhoto()
	{
		hd_print(__METHOD__);
		$a = TemplateManager::getInstance()->getTemplate("photo", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => isset($this->data->Photo)? $this->handlerUserInput : $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templatePlugins()
	{
		hd_print(__METHOD__);
		hd_print('templatePlugins');
		$a = TemplateManager::getInstance()->getTemplate("plugins", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));
		if (isset($this->data->Video) && isset($this->data->Video[0]->Media->Part)){
            $actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->handlerUserInput));
        } else {
        	$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
        }

		$a['data']['actions'] = $actions;
		return $a;

	}

    public function getField($name, $item, $field = null){
        hd_print("entrou no getField de BaseScreen name =$name e item = $item");
	}
    public function getData(){

	}
    public function getMediaUrl($data){

	}




}

?>
