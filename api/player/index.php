<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");
// print "<pre>";

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
	$player->Protect($x->pid,DRAFT);
	$player->Drafted($x->pid,DRAFT);

//	if (($player->protected)||($player->drafted)){
	if ($_REQUEST['show'] == "name"){
		print "$player->pid,$player->franchise_id,$player->level,$player->vintage,$player->name\n";
	}
	else {
		print "$player->pid,$player->franchise_id,$player->level,$player->vintage,$player->points\n";
	}


}

?>