<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


// Pull Back All Players
$query = ("
	SELECT 
		team_players.pid
	FROM team_players
	LEFT JOIN player on player.pid = team_players.pid
	ORDER BY name_last, name_first
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($player->pid);

	$player->Ratings($player->pid, 0, 2011);
	$player->Historicals($player->vintage);

	$position = position_find($player->id, 2011);


	print "- $player->name ($player->position/$position) $player->vintage_inn - $player->vintage_gs \n";

	$zquery = "UPDATE team_players SET position = '$position' WHERE pid = '$player->pid' LIMIT 1";
	$zresult = db::Q()->prepare($zquery);
	$zresult->execute();
	


	unset($position);
}


?>