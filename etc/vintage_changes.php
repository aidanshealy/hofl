<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");




// Pull Back All Free Agents
$query = ("
	SELECT 
		season,
		year,
		COUNT(pid) as total,
		COUNT(date_update) as changes,
		ROUND((COUNT(date_update)/COUNT(pid)*100),2) as pct
	FROM player_vintage
	GROUP BY season
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){


	print "$x->year - $x->total - $x->changes ($x->pct%)\n";


}


// Clean up

/*
$query = ("
	SELECT 
		pid,
		season,
		year,
		date_update
	FROM player_vintage
	ORDER BY pid, season
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	$current->date = $x->date_update;
	if ($current->date == $previous->date && !is_null($current->date)){
		print "- $player->name ($x->year) $previous->date - $current->date\n";

    $yquery = "UPDATE player_vintage SET date_update = NULL WHERE pid = $player->pid AND season = $x->season LIMIT 1";
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute();

	}
	$previous->date = $x->date_update;
	



}

*/
