<?php

// require_once 'classes/emplexer/utils/translations.php';
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


$url = 'https://plex.tv/users/sign_in.xml';

        $opts = array( CURLOPT_HTTPHEADER =>
                        array(
                            'X-Plex-Client-Identifier:sdsdsdsds',
                            'X-Plex-Client-Platform:DuneOS',
                            'X-Plex-Device-Name:Dune',
                            'X-Plex-Model:1',
                            'X-Plex-Platform:DuneOS',
                            'X-Plex-Platform-Version:1',
                            'X-Plex-Product:emplexer',
                            'X-Plex-Version:1'
                            ),
                        CURLOPT_USERPWD => "newloran2@gmail.com:bastard123",
                        CURLOPT_HEADER => true,
                        CURLINFO_HEADER_OUT => true,
                        CURLOPT_POST => true
                     );

        // $data = $this->get($url, $opts);

 $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,    false);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT,    10);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,    true);
        curl_setopt($ch, CURLOPT_TIMEOUT,           10);
        // curl_setopt($ch, CURLOPT_USERAGENT,         'DuneHD/1.0');
        curl_setopt($ch,CURLOPT_ENCODING ,           "gzip");
        curl_setopt($ch, CURLOPT_URL,               $url);
        curl_setopt($ch, CURLOPT_USERAGENT, 'curl/7.30.0');
        curl_setopt($ch,CURLOPT_FAILONERROR,true);
        curl_setopt($ch, CURLOPT_HTTPHEADER , array(
                            'X-Plex-Client-Identifier:sdsdsdsds',
                            'X-Plex-Client-Platform:DuneOS',
                            'X-Plex-Device-Name:Dune',
                            'X-Plex-Model:1',
                            'X-Plex-Platform:DuneOS',
                            'X-Plex-Platform-Version:1',
                            'X-Plex-Product:emplexer',
                            'X-Plex-Version:1',
                            'Content-Type: application/xml',
                            ));
        curl_setopt($ch, CURLOPT_USERPWD , "newloran2@gmail.com:bastard123");
        curl_setopt($ch, CURLOPT_HEADER , true);
        curl_setopt($ch, CURLINFO_HEADER_OUT , true);
        curl_setopt($ch, CURLOPT_VERBOSE , true);
        curl_setopt($ch, CURLOPT_POST , true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "");
        // $f = fopen('/tmp/request.txt', 'w');
        // curl_setopt($ch,CURLOPT_STDERR ,$f);
        // fclose($f);




$content = curl_exec($ch);
$a = curl_getinfo($ch);
print_r($a);
$errmsg  = curl_error( $ch );
echo "errormsg = $errmsg\n";
print_r($content);

curl_close($ch);
        // print_r($a);




 echo "memoria maxima : " , memory_get_peak_usage(true),    "\n";


