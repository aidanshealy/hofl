<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$oid = OID;
$fid = FID;

// Pull Back All Free Agents
$query = ("
	SELECT 
		watchlist_copy.pid,
		note
	FROM watchlist_copy
	LEFT JOIN player on player.pid = watchlist_copy.pid
	WHERE owner_id = $oid AND franchise_id = $fid
	ORDER BY name_last, name_first
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);


	print "$player->link ($team->name) $x->note<br>";


}



?>