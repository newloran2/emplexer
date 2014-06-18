<?php

// require_once 'classes/emplexer/utils/translations.php';
// define('ROOT_PATH', __DIR__);
// require_once  'lib/dune_core/bootstrap.php';
// require_once 'AutoLoad.php';

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



// $xml = Client::getInstance()->getAndParse("http://192.168.2.8:32400/library/metadata/139");
// $xml = Client::getInstance()->getAndParse("http://192.168.2.8:32400/library/metadata/20306");

// print_r($xml);


// $data = $xml->xpath("//Stream[@codec=\"srt\"][1]/@key");
//
// print_r((string)$data[0]);
//
// $v = array();
// $temp = null;
//
//
// $httpfs = new Httpfs("http://192.168.2.8:32400/library/metadata/42523");
//
// print_r($httpfs);
// $httpfs->mount();
// // $httpfs->umount();
// // foreach ($data as $key => $value) {
// //     $temp = $data
// // }
//
//
//
//
//
//
//
//
//  echo "memoria maxima : " , memory_get_peak_usage(true),    "\n";
//
  // public static function v5($namespace, $name) {
// echo (UUID::v1());



// $fp = stream_socket_client("udp://127.0.0.1:13", $errno, $errstr);
$fp = stream_socket_client("udp://192.168.2.255:32414", $errno, $errstr);
$data =  "M-SEARCH * HTTP/1.1\r\n\r\n";
if (!$fp) {
    echo "ERROR: $errno - $errstr<br />\n";
} else {
    fwrite($fp,$data);
    echo fread($fp, strlen($data));
    fclose($fp);
}
//
