<?php 

/**
* 
*/	
class BaseView
{
	
	protected $sections;

	function __construct(Plex_Server $mainServer)
	{
		$this->sections = $mainServer->getLibrary()->getSections();		
	}
}


 ?>