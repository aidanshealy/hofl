<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

print "Free Agents \t\t Vin \t AVG \t OBP \t SLG \t DB \t TR \t HR \t BB \t SO \t Rating \n";

// Pull Back All Free Agents
$query = ("
	SELECT 
		player_vintage.pid
	FROM player_vintage
	LEFT JOIN player on player.pid = player_vintage.pid
	WHERE vintage = 1977 AND season = 18
	ORDER BY name_last, name_first
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($player->pid);
	$player->Ratings($player->pid, 0, $player->vintage);
	$player->Historicals($player->vintage);

	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);


	print "$player->name_pad \t $team->name\n";


}