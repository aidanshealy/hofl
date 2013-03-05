<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


$player = new Player;
$player->Information(10001);
$player->Vintage(10001,17);

print_r(var_export($player));
