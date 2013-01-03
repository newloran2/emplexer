<?php 

class EmplexerPopUp 
{
	
	private $maxSize;
	private $nextText;
	private $previousText;
	private $baseUrl;

	function __construct($maxSize=10, $nextText="Next", $previousText="previous")
	{
		$this->maxSize=$maxSize;
		$this->nextText=$nextText;
		$this->previousText=$previousText;
	}

	public function showPopUpMenu($url){
		hd_print(__METHOD__);
		$doc = HD::http_get_document($url);
		// hd_print(print_r($doc,true));
		$pop_up_items =  array();
		$xml = simplexml_load_string($doc);
		// hd_print(print_r($xml, true));
		$pop_up_items = array();
		foreach ($xml->Directory as $c){
			$key = (string)$c->attributes()->key;
			$prompt = (string)$c->attributes()->prompt;
			hd_print("key===$key");
			if ($key != 'all' &&  $key != 'folder' && !$prompt ){

				$pop_up_items[] = array(
					GuiMenuItemDef::caption=> (string)$c->attributes()->title,
					//GuiMenuItemDef::action =>  ActionFactory::open_folder($this->get_right_media_url($media_url, $key), $key)
					);
			}
		}
		$chucks = array_chunk($pop_up_items, $this->maxSize);

		
		$result = array();
		



		$a = $this->addNextToChunk($chucks,$chucks[0],0);
		hd_print('a ==== ' . print_r($a, true));

		$result[] = $chucks[0];
		$result[] = array(GuiMenuItemDef::is_separator =>true);
		$result[] = array(					
			GuiMenuItemDef::caption=> $this->nextText,
			GuiMenuItemDef::action => ActionFactory::show_popup_menu($this->addNextToChunk($chucks,$chucks[0],0))
		);

		$action = ActionFactory::show_popup_menu($chucks[1]);
		// hd_print(print_r($action, true));
		// hd_print(print_r($chucks, true));
		hd_print('result = ' . print_r($result, true));
		

		return $action;
	}


	private function addNextToChunk($chunckArray, $currentChunk, $index)
	{
		hd_print(__METHOD__);
		// hd_print('chunckArray =' . print_r($chunckArray, true));
		// hd_print('currentChunk =' . print_r($currentChunk, true));
		// hd_print('index =' . print_r($index, true));

		if ($index >= count($chunckArray)){
			hd_print('é maior' . print_r($currentChunk, true));
			return $currentChunk;	
		} else {
			hd_print('Não é maior');
		}

		$currentChunk[] = 	$chunckArray[$index];
		$currentChunk[] = array(GuiMenuItemDef::is_separator =>true);
		$currentChunk[] = array(					
			GuiMenuItemDef::caption=> $this->nextText,
			GuiMenuItemDef::action => ActionFactory::show_popup_menu($this->addNextToChunk($chunckArray, $currentChunk,$index+1))
		);

	}
}


?>