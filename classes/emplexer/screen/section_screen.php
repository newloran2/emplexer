<?php 
	require_once 'BaseScreen.php';
	/**
	* List all sections available in plex
	*/
	class SectionScreen extends BaseScreen
	{
		
		public function generateScreen(){
			$data =  Client::getInstance()->getByPath('/library/sections');
			$output = $this->generateSingleList($this->generateItensArray($data));
			// print_r($output);
			return $output;
		}

		private function generateItensArray($data) {
			$directories = $data->Directory;
			$items = array();

			foreach ($directories as $value) {
				$items[] = array(
					'caption' =>  (string)$value->attributes()->title,	
					'url' => 'teste'
				);
			}
			return $items;
		}
		
	}
?>