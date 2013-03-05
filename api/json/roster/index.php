<?
$skip = "true";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: application/json");
// header("Content-Type: text/plain");


$query = ("
	SELECT 
		player.name_last,
		player.name_first,
		team_players.*,
		player_decoder.name as code
	FROM team_players 
	LEFT JOIN player ON player.pid = team_players.pid
	LEFT JOIN player_decoder ON player_decoder.pid = team_players.pid AND player_decoder.season = $season
	ORDER BY player.name_last, player.name_first
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

//	$player = new Player;
//	$player->Information($x->pid);
//	$player->Vintage($x->pid,SEASON);
//	$player->Protect($x->pid,$draft_id);
//	$player->Drafted($x->pid,$draft_id);

		if ($x->franchise_id){
			$team = new Team;
			$team->Code($x->franchise_id);
			$team->Information($team->id);
		}
		else {
			$team->city = "Free Agent";
		}


		$results[] = array(
			"name_first" => $x->name_first,
			"name_last" => $x->name_last,
			"pid" => $x->pid,
			"id" => $x->id,
			"short" => $x->code,
			"franchise_id" => $x->franchise_id,
			"team_city" => $team->city,
			"team_nickname" => $team->nickname,
			"level" => $x->level,
			"pps" => $x->points,
			"position" => $x->position,
			"starter" => $x->starter,
			"grandfather" => $x->grandfather
		);

}

echo json_encode($results);

?>