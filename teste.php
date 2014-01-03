<?php

require_once 'AutoLoad.php';

define('ROOT_DIR', __DIR__);


// $nfs = new NFS('192.168.2.9');
// $shares = $nfs->getAllNfsPaths();
// print_r($shares);

// // echo  explode(":", "192.168.2.9:/volume1/Animes")[1];
// $share = current($shares);
// echo "share = $share\n";
// $nfs->mountNfs($share);

// echo !is_null($nfs->isMonted($share)) ? " montado\n" : "Não montado \n";
// $nfs->umountNfs($share);
// echo !is_null($nfs->isMonted($share)) ? " montado\n" : "Não montado \n";

$templateFile = ROOT_DIR . "/templates/default.json";
$templateJson = json_decode(file_get_contents($templateFile));
// function tag($name, $json){
//     $a = explode(":",$name);
//     // print_r($a);
//     echo "count = $name = " .  (string) count($a) . "\n";
//     if (count($a) == 1) return $json->{$a[0]};
//     for ($i=1 ; $i < count($a) ; $i++) {
//         // echo "name = " . $a[$i] . "\n";
//         // print_r(array_slice($a, $i));
//         return tag(implode(":", array_slice($a, $i)), $json->{$a[$i-1]});
//     }
// }



// // echo gettype($templateJson->base->items->view_item_params);
// //


// print_r(tag("base:items:view_item_params:icon_path", $templateJson));



$a = $templateJson->base->items->view_item_params;

$c = new ArrayObject(array("base" => new ArrayObject(array("view_item_params"=> $a),ArrayObject::ARRAY_AS_PROPS)), ArrayObject::ARRAY_AS_PROPS);

// echo implode(":", array("a" , "c"));

print_r($c);

print_r($c->base->view_item_params);




?>