<?php

// require_once 'classes/emplexer/utils/translations.php';
require_once 'AutoLoad.php';
error_reporting(E_ALL);


$n = new NFS('192.168.2.9');

print_r($n);


// $b = new RemoteFileSystemIterator();

$a = new NfsFileSystemIterator('192.168.2.9','/volume1/Filmes', '/tmp/emplexer3/volume1/Filmes');

print_r($a);

$a->mount();

// sleep(1);
echo $a->isMounted() ? "está montado \n":  "não está montado\n";
echo "total de itens: ", count(iterator_to_array($a)) , "\n" ;
// // echo $a->current() , "\n";

$a->rewind();
$files = new CallbackFilterIterator($a, function ($current, $key, $iterator) {
    return $current->isDir() && ! $iterator->isDot();
});

echo "total de pastas: ", count(iterator_to_array($files)) , "\n";
// foreach ($files as  $value) {
//     echo $value , "\n";
// }
// $b = new FilesystemIterator('/tmp/emplexer3');
// print_r($b);

// print_r(iterator_to_array($b));
// $a->unMount();



 echo "memoria maxima : " , memory_get_peak_usage(true),    "\n";


