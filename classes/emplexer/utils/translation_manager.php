<?php


/**
*
*/
class TranslationManager
{
    private $translationTable;
    private static $instance;

    private function __construct()
    {
        $this->loadTranslationLanguage('en');
    }

    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new TranslationManager();
        }
        return self::$instance;
    }

    public function loadTranslationLanguage($lang){
        $this->translationTable = json_decode(file_get_contents(ROOT_PATH . "/translations/translations_en.json"), true);
    }


    public function getTranslation($key){
        if (isset($this->translationTable[$key])){
            // hd_print("$key exite");
            return $this->translationTable[$key];
        } else {
            // hd_print("$key não exite");
            return $key;
        }
    }


}

?>