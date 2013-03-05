<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


$boxscore_id = $_REQUEST['box'] ? $_REQUEST['box'] : "2012081900300";
$org = 1;


$box = new Score;
$box->Information($boxscore_id,$org);
$box->PlayerGame($boxscore_id,$org);

$player = new Player;
$player->Information($box->potg_pid);

print <<< CONTENT

Player of the Game!!!
$player->name_pad $box->potg_score


CONTENT;


print_r($box->potg_results);




?>