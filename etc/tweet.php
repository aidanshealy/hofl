<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$hash = display_body("tweet #tweet diddly tweet #html");

print $hash;



?>