<?php 



namespace lib\emplexer\menu;
/**
*  Represent and popup menu
*/
class Menu extends PopUp
{
	private $teste;
	function __construct()
	{
		# code...
	}
        
        
        public function getTeste() {
            return $this->teste;
        }

        public function setTeste($teste) {
            $this->teste = $teste;
        }


        
}

 ?>