<?php 
	/**
	* List all sections available in plex
	*/
	class SectionScreen extends BaseScreen
	{
		
		public function generateScreen(){
			$path =  '/library/sections';
			$data =  Client::getInstance()->getByPath($path);

			$output = $this->generateSingleList($this->generateItensArray($path, $data));
			return $output;
		}

		private function generateItensArray($lastUrl, $data) {
			$directories = $data->Directory;
			$items = array();

			foreach ($directories as $value) {
				$items[] = array(
					'caption' =>  (string)$value->attributes()->title,	
					'url' => "$lastUrl/". (string)$value->attributes()->key ,
					'type' =>  (string)$value->attributes()->show
				);
			}
			return $items;
		}
		
	}
?>