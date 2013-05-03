<?php 


$class_renamer = array(
	'HD' => 'utils'
);


/**
 * Custom load for dune core files
 * @param  String $class class name
 */
function load_dune_core($class)
{
	global $class_renamer;

	if (array_key_exists($class, $class_renamer)){
		$class = $class_renamer[$class];
	}

	$paths[]  = ROOT_DIR.  '/lib/dune';
	// $paths[]  = ROOT_DIR.  '/lib/dune/dune_core';	
	$paths[]  = ROOT_DIR.  '/lib/dune/tv';
	$paths[]  = ROOT_DIR.  '/lib/dune/vod';

	$file_name = strtolower(preg_replace('/(?!^)[[:upper:]]/','_\0',$class));
	
	foreach ($paths as $value) {
		$file = "$value/$file_name.php";
		hd_print("load_dune_core $file <br>");
		if (file_exists($file)){
			hd_print("load_dune_core $file  <b>E</b><br>");
			require_once ($file);	
			break;
		} else {
			hd_print("load_dune_core $file  <b>N</b><br>");
		}
	}
}


function load_php_plex($class){

}



function load($class)
{

	// echo "load $class <br>";
	// $c = str_replace("\\", "/", ROOT_DIR . "/$class.php");

	// if (!file_exists($c)){
	// 	return new Exception("Include File {$c} does not Exists");
	// }

	// require_once ($c);
}


spl_autoload_register('load');
spl_autoload_register('load_dune_core');


?>