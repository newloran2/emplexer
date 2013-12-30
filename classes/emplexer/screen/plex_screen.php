<?php 


class PlexScreen extends BaseScreen
{
	public function generateScreen(){
		$viewGroup = (string)$this->data->attributes()->viewGroup;
		// echo "viewGroup = $viewGroup \n";	
		
		if (!$viewGroup && strstr($this->path, 'metadata')){
			// echo("uma url para tocar foi inserida " . $this->path . "\n");
			$viewGroup = 'play';


		}		
		// echo ("erik clemente\n");
		return  $this->getTemplateByType($viewGroup);
	}


	/**
	 * Exec the media with default dune player and refresh screen after the playback stops
	 */
	public function templatePlay(){
		// var_dump($this->data->Video[0]->attributes()->parentKey);
		$url=Client::getInstance()->getUrl(null, (string)$this->data->Video[0]->Media->Part->attributes()->key );
		$parentUrl =  Client::getInstance()->getUrl(null, (string)$this->data->Video[0]->attributes()->parentKey . "/children") ;


		$invalidate =  ActionFactory::invalidate_folders(array($parentUrl));

		return ActionFactory::launch_media_url($url,$invalidate);

	}

}


 ?>