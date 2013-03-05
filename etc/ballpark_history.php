<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = $_REQUEST['season'] ? $_REQUEST['season'] : 18;
$id = $_REQUEST['id'] ? $_REQUEST['id'] : 10;
$org = $_REQUEST['org'] ? $_REQUEST['org'] : 1;


// Start Single Team Process
$query = ("
	SELECT 
		team_franchise.franchise_id,
		team.id,
		team.short,
		team.city,
		team.nickname,
		team.division,
		team.ballpark,
		team.ballpark_year
	FROM 
		team_franchise 
	LEFT JOIN team ON team_franchise.team_id = team.id
	WHERE team_franchise.season = $season AND team_franchise.organization = $org AND franchise_id < 50
	ORDER BY team.city, team.nickname
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){


	print "$x->city [$x->ballpark $x->ballpark_year]\n";

	$dataset = array($x->franchise_id,$season,$org,$x->ballpark,$x->ballpark_year);

	$yquery = ("
		INSERT INTO team_ballparks (franchise_id,season,organization,ballpark_id,ballpark_year) VALUES (?,?,?,?,?);
	");
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute($dataset);


}

?>