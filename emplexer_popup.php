<?php 

class popUp 
{
	
	private $maxSize;
	private $nextText;
	private $previousText;

	function __construct($maxSize=10, $nextText="Next", $previousText="previous")
	{
		$this->maxSize=$maxSize;
		$this->nextText=$nextText;
		$this->previousText=$previousText;
	}
}


?>