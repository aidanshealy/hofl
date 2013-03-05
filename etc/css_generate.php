<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

// print_r($institution);


generate_css($institution);

print "CSS Generated";



?>