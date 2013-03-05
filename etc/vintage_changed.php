<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


// Clean up


$query = ("
	SELECT 
		player_vintage.pid,
		season,
		year,
		date_update
	FROM player_vintage
	LEFT JOIN player ON player.pid = player_vintage.pid
	WHERE season = 18
	ORDER BY name_last, name_first, season
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($x->pid);

	$previous = new Player;
	$previous->Vintage($x->pid,17);

	$letter = substr($player->name_last,0,1);
	if ($letter != $prev->letter){ print "---\n"; }
	$prev->letter = $letter;

	if(!is_null($x->date_update) && $player->vintage != $previous->vintage && $previous->vintage){
		print "$player->pid - $player->name_pad $player->vintage / $previous->vintage \n";
		$count++;
	}
	else if(!is_null($x->date_update) && $player->vintage != $previous->vintage) {
		$new .= "- $player->name_pad $player->vintage / $previous->vintage\n";
	}
	else if ($previous->vintage == 1975 || $previous->vintage == 1976 || $previous->vintage == 1977){
		$recycle .= "$player->pid - $player->name_pad $player->vintage / $previous->vintage $player->grandfather \n";
	
	} 





/*
	$current->date = $x->date_update;
	if ($current->date == $previous->date && !is_null($current->date)){
		print "- $player->name ($x->year) $previous->date - $current->date\n";

    $yquery = "UPDATE player_vintage SET date_update = NULL WHERE pid = $player->pid AND season = $x->season LIMIT 1";
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute();

	}
	$previous->date = $x->date_update;
*/
	



}

print "---------------------\n";
print $recycle;

print "---------------------\n";
print $new;

print $count;
