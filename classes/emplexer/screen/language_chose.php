<?php

class LanguageChose extends BaseConfigScreen {

    public function __construct(){
        $langs = array(
            'translations_en.json'    => _('English'),
            'translations_pt_br.json' => _('Brazilian Portuguese')
            );
        $this->addControl(new GuiControlCombo('language', GetterUtils::getValueOrDefault(Config::getInstance()->language, 'translations_en.json'), $langs));
        $this->addControl(new GuiControlButton('save', _('Save'),100, Actions::runThisStaticMethod('LanguageChose::saveLanguageAndReload')));
    }


    public static function saveLanguageAndReload($user_input){
        hd_print("valor de language = "  . $user_input->language);
        Config::getInstance()->language = $user_input->language;
        TranslationManager::getInstance()->loadTranslationLanguage($user_input->language);
        return ActionFactory::invalidate_folders(array('language'));
    }

}
