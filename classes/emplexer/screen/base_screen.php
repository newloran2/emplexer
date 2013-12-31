<?php

/**
* Base Menu class for emplexer
*/

abstract class BaseScreen
{

	protected $iconFile = "gui_skin://small_icons/folder.aai";
	protected $openFolder = PLUGIN_OPEN_FOLDER_ACTION_ID;
	protected $handlerUserInput = PLUGIN_HANDLE_USER_INPUT_ACTION_ID;
	protected $path;
	protected $data;
	protected $nextTemplate = false;
	protected $templates =array();

	function __construct($key=null, $nextTemplate=false) {
		if (!$key)
			$key = '/library/sections';

		$this->path = Client::getInstance()->getUrl(null, $key);
		$this->data = Client::getInstance()->getAndParse($this->path);
		$this->nextTemplate = $nextTemplate != null ? $nextTemplate :  array();

		$this->templates = json_decode(Config::getInstance()->templateViewNumber);

		//hd_print(__METHOD__ .  ':' .  print_r($this->templates, true));

	}

	public function getTemplateByType($type){
		$fun = 'template'.ucwords($type);
		return $this->$fun();
	}

	private function getTemplateIndexAndUpdate($key){
		$templateIndex = Config::getInstance()->templateViewNumber;
		$templateIndex != null ? json_decode($templateIndex) :  array();
		$index = isset($templateIndex->{$key}) ? (int)$templateIndex->{$key}+1 : 1;
		$templateIndex->{$key} = $index;
		Config::getInstance()->templateViewNumber = json_encode($templateIndex);

		return $index;

	}




	// //without 	viewGroup i considerer that screnn a generic with directories index
	protected function template(){
		$a = TemplateManager::getInstance()->getTemplate("base", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}
	protected function templateSecondary(){
		$a = TemplateManager::getInstance()->getTemplate("secondary", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}
	protected function templateMovie(){

		$a = TemplateManager::getInstance()->getTemplate("movie", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateShow(){
		$a = TemplateManager::getInstance()->getTemplate("show", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateSeason(){
		$a = TemplateManager::getInstance()->getTemplate("season", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateEpisode(){
		$a = TemplateManager::getInstance()->getTemplate("episode", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->handlerUserInput));
		$a['data']['actions'] = $actions;
		return $a;
	}

	protected function templateArtist(){

		$a = TemplateManager::getInstance()->getTemplate("artist", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}


	protected function templateAlbum()
	{
		$a = TemplateManager::getInstance()->getTemplate("album", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;

	}
	protected function templateTrack()
	{
		$a = TemplateManager::getInstance()->getTemplate("track", $this->path,  $this->data);
		$actions = array(GUI_EVENT_KEY_ENTER => array(GuiAction::handler_string_id => $this->openFolder));
		$a['data']['actions'] = $actions;
		return $a;
	}

	public function __destruct()
	{
		// Config::getInstance()->templateViewNumber = json_encode($this->templates);
	}
	abstract public function generateScreen();

}


?>