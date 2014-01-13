<?php

// require_once 'classes/emplexer/utils/translations.php';
require_once 'AutoLoad.php';


// $a = new SimpleXMLElement("http://127.0.0.1/teste.xml", 0, true);
// // $a = new SimpleXMLElement("http://192.168.2.8:32400/library/sections/2/all", 0, true);


// $b = '<dwml>
//   <data>
//     <location>
//       <location-key>point1</location-key>
//         <point latitude="37.39" longitude="-122.07"></point>
//       </location>
//   </data>
// </dwml>';

// $b = new SimpleXMLElement($b);

// // $lats =  $b->xpath('/dwml/data/location/point/@latitude');
// // echo $lats[0];

// // $dom = dom_import_simplexml($a);

// // $dom = $dom->ownerDocument;
// // $domXpath = new DOMXPath($dom);



// // print_r($domXpath->query('/*/@key'));


// echo $a->xpath('/*/@key')[0];

// print_r(memory_get_peak_usage());


// $url = "http://192.168.2.8:32400/library/parts/37080/file.mkv";

// $a = Client::getInstance()->getFinalThumbUrl($url);

// print_r($a);


// echo _("isso é um teste \n");
// echo _("erik");
//
$a = "192";

echo filter_var($a, FILTER_VALIDATE_INT) ? "true\n" :  "false\n";



