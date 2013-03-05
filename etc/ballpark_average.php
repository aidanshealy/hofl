<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = $_REQUEST['season'] ? $_REQUEST['season'] : 18;
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
	WHERE 
		team_franchise.season = ? 
		AND team_franchise.organization = ? 
		AND franchise_id < 50
		AND team.division = 8
	ORDER BY team.city, team.nickname
");
$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $org);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	if ($x->division == 1){ $division = "AL East"; }
	else if ($x->division == 2){ $division = "AL North"; }
	else if ($x->division == 3){ $division = "AL South"; }
	else if ($x->division == 4){ $division = "AL West"; }
	else if ($x->division == 5){ $division = "NL East"; }
	else if ($x->division == 6){ $division = "NL North"; }
	else if ($x->division == 7){ $division = "NL South"; }
	else if ($x->division == 8){ $division = "NL West"; }




	$total++;
	
	$ballpark = new Ballpark;
	$ballpark->Detail($x->ballpark,$x->ballpark_year);
	
	$l1b += $ballpark->factor_lh_singles;
	$l2b += $ballpark->factor_lh_doubles;
	$l3b += $ballpark->factor_lh_triples;
	$lhr += $ballpark->factor_lh_homeruns;

	$r1b += $ballpark->factor_rh_singles;
	$r2b += $ballpark->factor_rh_doubles;
	$r3b += $ballpark->factor_rh_triples;
	$rhr += $ballpark->factor_rh_homeruns;

}

// Averages
$average->l1b = round($l1b/$total,1);
$average->l2b = round($l2b/$total,1);
$average->l3b = round($l3b/$total,1);
$average->lhr = round($lhr/$total,1);

$average->r1b = round($r1b/$total,1);
$average->r2b = round($r2b/$total,1);
$average->r3b = round($r3b/$total,1);
$average->rhr = round($rhr/$total,1);

print <<< CONTENT
Ballpark Averages
Division: $division
Season: $season

\t 1B \t 2B \t 3B \t HR
L: \t $average->l1b \t $average->l2b \t $average->l3b \t $average->lhr
R: \t $average->r1b \t $average->r2b \t $average->r3b \t $average->rhr


CONTENT;







?>