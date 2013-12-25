<?php 

$strings = array( 
    'camelCase', 
    'camelcase', 
    'DunePluginFW',
    'sOme worDswhichDon\'tGoTogether' 
); 

$pattern = '/(.*?[a-z]{1})([A-Z]{1}.*?)/'; 
$replace = '${1}_${2}'; 

foreach($strings as $string) 
{ 
    echo "Original :$string,  Replaced: " . preg_replace($pattern, $replace, $string) . "\n";
} 


echo  "teste = " . file_exists("/tmp/run") . "\n";

$options = getopt("j:");

print_r($options);
?>