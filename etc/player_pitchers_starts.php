<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

// Find Rookie of the Year
$query = "SELECT pid FROM team_players";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($x->pid);
	$player->Historicals($player->vintage);
	$player->Ratings($player->pid);

		print "$player->name_pad $team->name_pad $player->vintage \t $player->vintage_gs Rat: $player->rating_starter\n";

	if ($player->role == "P" && $player->vintage_gs >= 14){
		$team = new Team;
		$team->Code($player->franchise_id);
		$team->Information($team->id);


		$total++;

	}

	
}



print "Total: $total";









?>