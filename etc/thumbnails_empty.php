<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$d->div = $REQUEST['div'] ? $REQUEST['div'] : 1;
$d->season = $REQUEST['season'] ? $REQUEST['season'] : SEASON;
$d->org = $REQUEST['org'] ? $REQUEST['org'] : ORG;

$query = ("
	SELECT 
		team_players.pid
	FROM team_players
	LEFT JOIN player on player.pid = team_players.pid
	ORDER BY name_last, name_first
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	
	if ($player->photo_missing){
		$count++;
		print "$player->link_photos<br>";
	}

}

print "$count Players";



?>