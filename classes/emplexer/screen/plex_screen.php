<?php 


class PlexScreen extends BaseScreen
{
	public function generateScreen(){
		$viewGroup = (string)$this->data->attributes()->viewGroup;
		echo "viewGroup = $viewGroup \n";	
		return $this->getTemplateByType($viewGroup);
	}

}


 ?>