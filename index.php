<?php
	header("Content-Type: application/json");
	 error_reporting(E_ALL);
 	ini_set("display_errors", 1);
	require_once 'lib/dune_core/bootstrap.php';
	require_once 'lib/dune_core/dune_api_nova.php';
	require_once 'lib/dune_core/dune_plugin.php';
	require_once 'lib/dune_core/dune_plugin_fw.php';
	require_once 'lib/utils.php';
	require_once 'AutoLoad.php';


	$xml = simplexml_load_file("dune_plugin.xml");

	// print_r($xml);

	file_put_contents("/tmp/out.json", "\n\n");
	$data =json_decode($_POST['data']);
	// hd_print_r("data = ", $data);
	// var_dump("data");
	// var_dump($data->plugin_cookies);
	// $data =json_decode(base64_decode($_POST['data']));
	file_put_contents("/tmp/out.json", print_r($_POST, true) ,FILE_APPEND );

	// $config =  new Conf('emplexer_plugin_cookies.properties');


	// //echo("incluindo arquivo ". (string)$xml->params->program);
	require_once (string)$xml->params->program;


	$optionType = isset($data->op_type_code) ?  $data->op_type_code : 'get_folder_view';
	//  if (isset($data->input_data->media_url)){
	//  	$optionMediaUrl = $data->input_data->media_url;
	//  }else if (isset($data->input_data->selected_media_url)){
	// 	$optionMediaUrl = $data->input_data->selected_media_url;
	// }else {
	// 	$optionMediaUrl = array('media_url'=>'main');
	// }
	//
	if (!isset($data->input_data)){
		$optionMediaUrl = array('media_url'=>'main');
	} else {
		$optionMediaUrl =  $data->input_data;
	}




	$entry = null;


	// print_r($entry);

	// print ($entry->actions->key_enter->type . "\n");
	$output = array(
			'op_type_code' => $optionType ,
			'op_id' => "1",
			// 'input_data' => array(
			// 	"media_url" => $optionMediaUrl == null ? (string)$entry->media_url :  $optionMediaUrl
			// ),
			'input_data' => $optionMediaUrl,
			"plugin_cookies" => $data->plugin_cookies
		 );
	// print_r(json_encode($output));


	file_put_contents("/tmp/out.json", json_encode($output), FILE_APPEND);

	$out = DunePluginFw::$instance->call_plugin(json_encode($output));
	echo ($out);
	file_put_contents("/tmp/out.json", "\n\n", FILE_APPEND);
	file_put_contents("/tmp/out.json", $out, FILE_APPEND);
	// print (DefaultDunePluginFw::$plugin_class_name ) . "\n";
	// DefaultDunePluginFw::$instace->call_plugin($output);
	// print_r(DunePluginFw::$instance);
	 // {"op_type_code":"get_folder_view","op_id":"1","input_data":{"media_url":"main_menu"},"plugin_cookies":{}}

function convert($size)
 {
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
 }

// echo ("memoria = " .  memory_get_peak_usage(true) . "\n");

?>