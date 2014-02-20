<?php

// require_once 'classes/emplexer/utils/translations.php';
require_once  'lib/dune_core/bootstrap.php';
require_once 'AutoLoad.php';

error_reporting(E_ALL);


// $n = new NFS('192.168.2.9');

// print_r($n);


// mkdir("/tmp/emplexer3/volume2/photo", 0777, true);
// $n->mountAll();
// $n->umountAll();


// $b = new RemoteFileSystemIterator();

// $a = new NfsFileSystemIterator('192.168.2.9','/volume1/Filmes', '/tmp/emplexer3/volume1/Filmes');

// print_r($a);

// $a->mount();

// // sleep(1);
// echo $a->isMounted() ? "está montado \n":  "não está montado\n";
// echo "total de itens: ", count(iterator_to_array($a)) , "\n" ;
// // // echo $a->current() , "\n";

// $a->rewind();
// $files = new CallbackFilterIterator($a, function ($current, $key, $iterator) {
//     return $current->isDir() && ! $iterator->isDot();
// });

// echo "total de pastas: ", count(iterator_to_array($files)) , "\n";
// // foreach ($files as  $value) {
// //     echo $value , "\n";
// // }
// // $b = new FilesystemIterator('/tmp/emplexer3');
// // print_r($b);

// // print_r(iterator_to_array($b));
// $a->unMount();

// $data = Client::getInstance()->getFinalThumbUrl("http://192.168.2.8:32400/:/plugins/com.plexapp.plugins.youtube/resources/contentWithFallback?urls=http%253A%2F%2Fi1.ytimg.com%2Fvi%2FlDtQwVF_Nc8%2Fhqdefault.jpg%2Chttp%253A%2F%2Fi1.ytimg.com%2Fvi%2FlDtQwVF_Nc8%2Fdefault.jpg");

// print_r($data);
//
//
//


$xml = simplexml_load_file("/tmp/index.xml");

$element = simplexml_load_string($xml->Directory[0]->asXml());
print_r($element->xpath("/*"));





 echo "memoria maxima : " , memory_get_peak_usage(true),    "\n";


