<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$locked = array();
$draft_id = DRAFT;


$query = ("
	SELECT 
		COUNT(franchise_from) as total,
		franchise_from
	FROM draft_selections
	WHERE draft_id = $draft_id AND franchise_from != 0
	GROUP BY franchise_from
	HAVING total >= 6 
	ORDER BY total DESC
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
//	print "$x->franchise_from - $x->total\n";
	$locked[] = $x->franchise_from;

}


$query = ("
	SELECT 
		team_players.pid 
	FROM team_players 
	LEFT JOIN player ON player.pid = team_players.pid
	ORDER BY player.name_last, player.name_first
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($x->pid,SEASON);
	$player->Protect($x->pid,$draft_id);
	$player->Drafted($x->pid,$draft_id);

//	if (($player->protected)||($player->drafted)){
// 	if (($player->protected)){
	if (($player->protected)||($player->drafted)||in_array($player->franchise_id,$locked)){
		if ($_REQUEST['show'] == "name"){
			print "$player->pid,$player->id,$player->name\n";
		}
		else {
			print "$player->pid,$player->id\n";
		}
	}


}

?>