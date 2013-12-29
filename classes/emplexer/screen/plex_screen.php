<?php 


class PlexScreen extends BaseScreen
{
	public function generateScreen(){
		$viewGroup = (string)$this->data->attributes()->viewGroup;
		echo "viewGroup = $viewGroup \n";	
		if (!$viewGroup && strstr($this->path, 'metadata')){
			echo("uma url para tocar foi inserida\n");
			$viewGroup = 'play';
		}		
		return $this->getTemplateByType($viewGroup);
	}

	public function templatePlay(){
		return ActionFactory::launch_media_url(Client::getInstance()->getUrl(null, (string)$this->data->Video[0]->Media->Part->attributes()->key));
	}

}


 ?>