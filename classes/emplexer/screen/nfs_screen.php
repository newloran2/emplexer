<?php
/**
 *
 */
class NfsScreen  implements ScreenInterface , TemplateCallbackInterface
{

    private $path;
    private $decomposedPath;
    private $nfs;
    private $localFileIterator;
    private $serverIps;
    private  $data;
    function __construct($path=null) {
        $ipsString = Config::getInstance()->nfsIps;
        if ($ipsString){
            $this->serverIps =  json_decode(urldecode($ipsString));
        }

        $this->path = $path;
        $this->decomposedPath = parse_url($this->path);

        if (isset($path) && filter_var($path, FILTER_VALIDATE_URL)){
            $this->nfs = new NFS($path);
        } else  if (isset($path) && strstr($path, "/")){
            $this->localFileIterator =  new FilesystemIterator($path);
        }

    }

    public function getField($key, $name, $field=null){

        if (gettype($key) !=  "string"){
            return $key;
        } 
        if ($key == "{{key}}"){
            $ret =  array_search($name, $this->data);
            if ($field == "caption"){
                if($v = Config::getInstance()->isValueInPluginCookies($name)){
                    return sprintf("%s (%s)", $ret, $v);
                }
            }
            return $ret;
        } else if ($key  == "{{value}}"){
            return $name;
        } else {
            return $key;
        }
    }

    public function getData(){
        $ret = array();
        if (isset($this->localFileIterator)){
            $a = isset($this->localFileIterator)? $this->localFileIterator :   $this->nfs->getIteratorForNfsPath($this->path);
            if (!isset($this->localFileIterator)){
                $a->mount();
            }
            $files = new CallbackFilterIterator($a, function ($current, $key, $iterator) {
                    return $current->isDir() && ! $iterator->isDot();
                    });

            foreach ($files as $key => $value) {
                $ret[basename($value)] = "nfs|$value";
            }
        } else if (!isset($this->decomposedPath['path'])){
            $files =  $this->nfs->getAllNfsPaths();

            foreach ($files as $key => $value) {
                $ret[basename($value)] = "nfs|$value";
            }

        } else if (isset($this->decomposedPath['scheme']) && isset($this->decomposedPath['host'])){
            $a =  $this->nfs->getIteratorForNfsPath($this->path);
            $a->mount();

            foreach ($a->getOnlyFolders() as $key => $value) {
                $ret[basename($value)] = "nfs|$value";
            }


        } else {
            $ips =  Config::getInstance()->nfsIps;
            if (!isset($ips)){
                $b = new Modal(_("NFS server ip"));
                $b->addControl(new GuiControlText("nfsIp", null, "192.168.2.9", 400));
                $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp')),10);

                $b->show();
            }

            $ips =  explode(',', $ips);
            $ret = array();
            foreach ( $ips as $ip) {
                $ret[$ip] = "nfs|nfs://$ip";
            }

            // return $ret;
        }
        $this->data =  $ret;
        return $ret;
    }

    public function getMediaUrl($data){
        return null;
    }


    public static function chose($user_input){
        Config::getInstance()->{$user_input->selected_item_label} = $user_input->selected_media_url;
        return ActionFactory::invalidate_folders(array($user_input->parent_media_url), null);

    }
    public static function saveNfsIp($user_input){
        if (!filter_var($user_input->nfsIp, FILTER_VALIDATE_IP)){
            $b = new Modal(_("NFS server ip"), null ,-1);
            $b->addControl(new GuiControlLabel(_('Please enter a valid ip.')));
            $b->addControl(new GuiControlText("nfsIp", null, $user_input->nfsIp, 400));
            $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp')), 10);

            $b->show();
        }

        if (!NFS::isANFSServer($user_input->nfsIp)){
            $b = new Modal(_("NFS server ip"));

            $b->addControl(new GuiControlLabel(_('This ip does not have an active nfs server.'),-25));
            $b->addControl(new GuiControlLabel(_('Please check and try again.'), -25));
            $b->addControl(new GuiControlText("nfsIp", null, $user_input->nfsIp, 400));
            $b->addControl(new GuiControlButton(null, _("Save"), 100, Actions::closeAndRunThisStaticMethod('NfsScreen::saveNfsIp'), 10));
            $b->show();
        }

        $nfsIps = Config::getInstance()->nfsIps;
        if (!isset($nfsIps)) {
            $nfsIps =array($user_input->nfsIp);
        } else {
            $nfsIps = explode(',', $nfsIps);
            $nfsIps[] = $user_input->nfsIp;
        }

        Config::getInstance()->nfsIps = implode(',', $nfsIps);


        return ActionFactory::open_folder("nfs");
    }



    public function generateScreen(){
        $a = TemplateManager::getInstance()->getTemplate("nfsScreen", array($this, 'getMediaUrl'),  array($this, 'getData'), array($this, 'getField'));

        $actions = array(
                GUI_EVENT_KEY_ENTER => array(
                    GuiAction::handler_string_id => PLUGIN_OPEN_FOLDER_ACTION_ID
                    )
                );
        if (isset($this->decomposedPath['host'])){

            $b = new PlexSectionPopupMenu(Actions::runThisStaticMethod('NfsScreen::chose'));
            $actions[GUI_EVENT_KEY_POPUP_MENU] = $b->generate();
        }
        $a['data']['actions'] = $actions;
        return $a;
    }
}

?>

