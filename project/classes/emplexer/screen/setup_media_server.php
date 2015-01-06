<?php

class SetupMediaServer extends BaseConfigScreen 
{
    public function __construct()
    {
        $manualySpecifyServer=GetterUtils::getValueOrDefault(Config::getInstance()->dontFindServer,'NO');
        $this->addControl(new GuiControlText('plexIp', _('Plex Ip'), GetterUtils::getValueOrDefault(Config::getInstance()->plexIp, null)));
        $this->addControl(new GuiControlText('plexPort', _('Plex Port'), GetterUtils::getValueOrDefault(Config::getInstance()->plexPort,null)));
        $this->addControl(new GuiControlText('userName', _('User Name'), GetterUtils::getValueOrDefault(Config::getInstance()->myPlexUserName, null)))
        $this->addControl(new GuiControlText('password', _('Password'), GetterUtils::getValueOrDefault(Config::getInstance()->myPlexPassword, null)))
        $this->addControl(new GuiControlButton('savePrefes', _('Save'), 400, Actions::runThisStaticMethod('SetupMediaServer::save')));
    }

    public static function save($user_input){
        hd_print_r(__METHOD__, $user_input);
        Config::getInstance()->dontFindServer = $user_input->dontFindServer;
        GetterUtils::getValueOrDefault($user_input->plexIp, Config::getInstance()->plexIp);
        GetterUtils::getValueOrDefault($user_input->plexPort, Config::getInstance()->plexPort);
        return ActionFactory::invalidate_folders(array('mediaServer'));
    }
}
