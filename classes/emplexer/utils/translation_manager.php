<?php


/**
*
*/
class TranslationManager
{
    private $translationTable;
    private $supportedLanguages = array(
        "translations_en.json", "translations_pt_br.json"
    );
    private static $instance;


    private function __construct()
    {
        $systemLang = Config::getInstance()->language;
        $this->loadTranslationLanguage(GetterUtils::getValueOrDefault($systemLang, 'translation_en.json'));
    }

    public static function getInstance(){
        if (!isset(self::$instance)){
            self::$instance = new TranslationManager();
        }
        return self::$instance;
    }

    public function loadTranslationLanguage($lang){
        if (!in_array($lang, $this->supportedLanguages)){
            hd_print("error: selected language are not supported $lang");
            $lang = "translations_en.json";
        }
        $this->translationTable = json_decode(file_get_contents(ROOT_PATH . "/translations/$lang"), true);
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
