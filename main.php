<?php
///////////////////////////////////////////////////////////////////////////

define('ROOT_PATH', __DIR__);
function hd_print_r($text, $data){
    hd_print($text);
    hd_print(print_r($data, true));
}

require_once 'classes/emplexer/emplexer.php';
require_once 'lib/default_dune_plugin_fw.php';
require_once 'AutoLoad.php';
  error_reporting(E_ALL);
 ini_set('display_errors', 1);


DefaultDunePluginFw::$plugin_class_name = 'Emplexer';

///////////////////////////////////////////////////////////////////////////
?>
