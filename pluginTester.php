<?php  
	require_once 'lib/dune_core/bootstrap.php';
	require_once 'lib/dune_core/dune_api_nova.php';
	require_once 'lib/dune_core/dune_plugin.php';
	require_once 'lib/dune_core/dune_plugin_fw.php';
	require_once 'AutoLoad.php';

	$xml = simplexml_load_file("dune_plugin.xml");

	// print_r($xml);

	$options = getopt("e:m:");

	$optionEntry = $options['e'];
	$optionMediaUrl = $options['m'];

	$entry = null;


	$config =  new Conf('emplexer_plugin_cookies.properties');


	foreach ($xml->entry_points->entry_point as $e) {
		if ($e->parent_media_url == $optionEntry){
			$entry = $e; 
			break;
		}
	}

	echo("incluindo arquivo ". (string)$xml->params->program);
	require_once (string)$xml->params->program;

	// print_r($entry);

	// print ($entry->actions->key_enter->type . "\n");
	$output = array(
			'op_type_code' => (string)$entry->actions->key_enter->type == "plugin_open_folder" ? "get_folder_view" :  (string)$entry->actions->key_enter->type ,
			'op_id' => "1",
			'input_data' => array(
				"media_url" => $optionMediaUrl == null ? (string)$entry->media_url :  $optionMediaUrl
			),
			"plugin_cookies" => $config->getData
		 );
	// print_r(json_encode($output));


	DunePluginFw::$instance->call_plugin(json_encode($output));
	// print (DefaultDunePluginFw::$plugin_class_name ) . "\n";
	// DefaultDunePluginFw::$instace->call_plugin($output);
	// print_r(DunePluginFw::$instance);
	 // {"op_type_code":"get_folder_view","op_id":"1","input_data":{"media_url":"main_menu"},"plugin_cookies":{}}	


?>