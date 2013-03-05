<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$d->div = $REQUEST['div'] ? $REQUEST['div'] : 1;
$d->season = $REQUEST['season'] ? $REQUEST['season'] : SEASON;
$d->org = $REQUEST['org'] ? $REQUEST['org'] : ORG;

$query = ("
	SELECT 
		team_franchise.franchise_id
	FROM 
		team_franchise 
	LEFT JOIN team ON team_franchise.team_id = team.id	
	WHERE 
		team.division = $d->div
		AND team_franchise.season = '$d->season'
		AND team_franchise.organization = '$d->org'
		AND (team_franchise.franchise_id != 59 AND team_franchise.franchise_id != 58)
	ORDER BY team.city
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id, $d->org, $d->season);
	$team->Information($team->id);
	
	print "$team->name<br>";


}





?>