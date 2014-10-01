<?php

// require_once 'AutoLoad.php';

class GcompTemplateManager implements GeneratableInterface {

    private $templateFile;
    private $templateJson;

    function __construct()
    {
        // $this->templateFile =  "/Users/newloran2/Dropbox/Projeto/Dune/emplexer/gcompsTemplate.json";
        $this->templateFile =  "gcompsTemplate.json";
        // echo "entrou ". $this->templateFile;
        $this->templateJson = json_decode(file_get_contents($this->templateFile), true);
    }

    public function generate(){
        // print_r($this->templateJson);
        $ret = $this->templateJson['base']['items'][1];
        $bgUrl =  $this->templateJson['base']['background_url'];
        $window = new GcompControlWindow($bgUrl);
        foreach ($ret['items'] as $value) {
            hd_print_r("o valor de value é", $value);
            //TODO: chamar a função criadora dinamicamente com call user funcion
            //
            if (isset($value['type'])){
                $function = 'GcompControl' . ucwords($value['type']) . '::initWithRepresentation';
                hd_print_r("chamando a função $function com o valor", $value);
                $a = call_user_func($function, $value);
                if ($a){
                    // $a= GcompControlImage::initWithRepresentation($value);
                    hd_print_r( "vou printar o valor de a", $a);
                    $window->addControl($a);
                }
            }
        }
        return $window;
    }
}





