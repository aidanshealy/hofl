<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


$start = date("U") - (86400 * 3);

// Find Rookie of the Year
$query = ("
	SELECT 
		SUBSTRING(page,9,5) as pid,
		COUNT(ip) as total,
		page
	FROM
		speed
	WHERE 
		page LIKE '/player/%'
		AND date_create >= $start
	GROUP BY SUBSTRING(page,9,5)
	ORDER BY total DESC
	LIMIT 15
	
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);

	$total++;

	print "$total. $player->name_pad $team->name_pad $x->total\n";

	
}



print "Total: $total";









?>