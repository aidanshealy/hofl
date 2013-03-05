<?
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
ob_start();

echo str_repeat(' ', 4096);

$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");



$boxscore_id = $_REQUEST['box'] ? $_REQUEST['box'] : "2012081900300";
$org = $_REQUEST['org'];


$query = ("
	SELECT boxscore_id FROM scores WHERE season = 18 AND organization = $org AND type = 2 AND played = 1 ORDER BY boxscore_id 
");		

$result = db::Q()->prepare($query);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$box = new Score;
	$box->Information($x->boxscore_id,$org);
	$box->PlayerGame($x->boxscore_id,$org);



	$player = new Player;
	$player->Information($box->potg_pid);
	
	if ($player->role == "B"){
		$total->batters++;
	}
	else {
		$total->pitchers++;
	}
	
	print "$box->game_month/$box->game_day/$box->game_year \t $player->name_pad $box->potg_score\n";

	ob_flush();
	flush();

	$insert = "UPDATE scores SET game_pog = '$box->potg_pid', game_pog_score = '$box->potg_score' WHERE boxscore_id = '$x->boxscore_id' AND organization = $org LIMIT 1";
	$xresult = db::Q()->prepare($insert);
	$xresult->execute();

//	print $insert . "\n";



}



print <<< CONTENT

Batters: $total->batters
Pitchers: $total->pitchers


CONTENT;





?>