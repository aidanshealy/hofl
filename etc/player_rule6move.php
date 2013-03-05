<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

// Find Rookie of the Year
$query = "SELECT pid FROM team_players WHERE level = 6";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);

	print "$player->name_pad $team->name_pad\n";

	$position = $player->role == "B" ? "OF" : "P";

	$yquery = "UPDATE team_players SET level = 2, points = 1, position = '$position' WHERE pid = $player->pid LIMIT 1";
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

	$total++;
	
}



print "Total: $total";









?>