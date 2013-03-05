<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


$season = SEASON;
$org = 1;
print "Majors\n";
$query = ("
	SELECT 
		team_franchise.franchise_id,
		team.id,
		team.short,
		team.city,
		team.nickname
	FROM 
		team_franchise 
	LEFT JOIN team ON team_franchise.team_id = team.id
	WHERE team_franchise.season = ? AND team_franchise.organization = ? AND franchise_id < 50
	ORDER BY franchise_id,team.city, team.nickname
");
$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $org);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id,1);
	$team->Information($team->id);
	
	$ballpark = new Ballpark;
	$ballpark->Information($team->ballpark);
	
	print "$team->franchise_id \t $team->id \t $team->name_pad $team->abbreviation \t $team->ballpark_year $ballpark->name\n ";



}

print "##################\n";
print "##################\n";
print "##################\n";

print "Minors\n";

$season = SEASON;
$org = 2;

$query = ("
	SELECT 
		team_franchise.franchise_id,
		team.id,
		team.short,
		team.city,
		team.nickname
	FROM 
		team_franchise 
	LEFT JOIN team ON team_franchise.team_id = team.id
	WHERE team_franchise.season = ? AND team_franchise.organization = ? AND franchise_id < 50
	ORDER BY franchise_id,team.city, team.nickname
");
$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $org);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id,2);
	$team->Information($team->id);
	
	$ballpark = new Ballpark;
	$ballpark->Information($team->ballpark);
	
	print "$team->franchise_id \t $team->id \t $team->name_pad $team->abbreviation \t $team->ballpark_year $ballpark->name\n ";



}
		
?>