<?php
class autoloader {

    public static $loader;
    private $isDune = false;

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    public function __construct()
    {
    	$this->isDune =  file_exists("/tmp/run");
		if (!$this->isDune){
			spl_autoload_register(array($this,'duneCore'));	
		}
		spl_autoload_register(array($this,'emplexer'));
        spl_autoload_register(array($this,'plex'));
        spl_autoload_register(array($this,'dune'));
   	}

   	private function putUndersCoreOnCamelCase($class){
   		$pattern = '/(.*?[a-z]{1})([A-Z]{1}.*?)/'; 
		$replace = '${1}_${2}'; 	
		$fileName =  strtolower(preg_replace($pattern, $replace, $class));
		return $fileName;
   	}

    
    public function emplexer($class)
    {
    	// echo "autoload emplexer $class\n";
        $fileName = $this->putUndersCoreOnCamelCase($class);
    	$s = explode('_', $fileName);
        
        if (count($s) >=2){
            $type = $s[1];
            $name = $s[0];

            
            $fileName = strtolower("classes/emplexer/$type/$fileName.php"); 
            
            if (file_exists($fileName)){
                require_once "$fileName";    
            } else {
                echo "Arquivo não existe : $fileName.php\n" ;
            }
        } else { 
            $fileName = strtolower("classes/emplexer/$class.php"); 
            if (file_exists($fileName)){
                require_once "$fileName";    
            }  else {
                echo "arquivo não existe $fileName\n";
            }
        }
    	
    }


    public function plex($class)
    {
        // echo "autoload dune $class\n";    

        $fileName = $this->putUndersCoreOnCamelCase($class);

        $fileName = "classes/plex/$fileName.php"; 
        
        if (file_exists($fileName)){
            require_once "$fileName";    
        }

    }    

    public function dune($class)
    {
		// echo "autoload dune $class\n";    

		$fileName = $this->putUndersCoreOnCamelCase($class);

		$fileName = "lib/$fileName.php"; 
        
        if (file_exists($fileName)){
            require_once "$fileName";    
        }

    }

    public function duneCore($class)
    {
		// echo "autoload duneCore $class\n";    
		$fileName = $this->putUndersCoreOnCamelCase($class);
        $fileName = "lib/dune_core/$fileName.php"; 
        
        if (file_exists($fileName)){
            require_once "$fileName";    
        }
        
    }


}

//call
autoloader::init();
?>