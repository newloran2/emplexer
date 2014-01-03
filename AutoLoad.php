<?php
class autoloader {
//teste
    public static $loader;
    private $isDune = false;
    private $includes ;

    public static function init()
    {
        if (self::$loader == NULL)
            self::$loader = new self();

        return self::$loader;
    }

    private function rglob($pattern, $flags = 0) {
        $files = glob($pattern, $flags);
        foreach (glob(dirname($pattern).'/*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
            $files = array_merge($files, $this->rglob($dir.'/'.basename($pattern), $flags));
        }
        return $files;
    }

    public function __construct()
    {

        $classes  =  $this->rglob(dirname(__FILE__)."/classes/*.php");
        $dune     =  $this->rglob(dirname(__FILE__)."/lib/*.php");

        $this->includes = array_merge($classes, $dune);
        // print_r($this->includes);
    	$this->isDune =  file_exists("/tmp/run");
		if (!$this->isDune){
			// spl_autoload_register(array($this,'duneCore'));
		}

        spl_autoload_register(array($this,'generic'));
   	}

   	private function putUndersCoreOnCamelCase($class){
   		$pattern = '/(.*?[a-z]{1})([A-Z]{1}.*?)/';
		$replace = '${1}_${2}';
		$fileName =  strtolower(preg_replace($pattern, $replace, $class));
    	return $fileName;
   	}

    public function generic($class){
        $fileName  = $this->putUndersCoreOnCamelCase($class);
        $files = preg_grep("/.*\/$fileName.php/", $this->includes);
        if (count($files) == 1){
            require_once current($files) ;
        } else {
            echo "arquivo $fileName não existe ou há mais de um com o mesmo nome\n";
            print_r($this->includes);
        }
    }

    public function duneCore($class)
    {
		// //echo "autoload duneCore $class\n";
		$fileName = $this->putUndersCoreOnCamelCase($class);
        $fileName = "lib/dune_core/$fileName.php";

        //echo "$fileName \n\n";
        if (file_exists($fileName)){
            //echo "incluindo $fileName\n";
            require_once "$fileName";
        } else {
            //echo "arquivo não existe $fileName\n";
        }

    }


}

//call
autoloader::init();
?>