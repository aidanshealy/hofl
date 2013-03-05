<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$org = $_REQUEST['org'] ? $_REQUEST['org'] : 1;
$type = 2;
$season = $_REQUEST['season'] ? $_REQUEST['season'] : 18;
$id = $_REQUEST['id'] ? $_REQUEST['id'] : 10;

$stat_avg = new Statistics;
$stat_avg->Equations("bavg");

$stat_ops = new Statistics;
$stat_ops->Equations("bops");

$stat_obp = new Statistics;
$stat_obp->Equations("bobp");

$stat_slg = new Statistics;
$stat_slg->Equations("bslg");

$stat_era = new Statistics;
$stat_era->Equations("pera");


print <<< CONTENT
Variables
Season: $season
Org: $org
Type: $type


CONTENT;


// OPS
$query = ("
	SELECT 
		fid,
		division
	FROM standings
	WHERE season = $season AND organization = $org AND type = $type AND month = 0
");

// print $query;

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	if ($x->division <= 4){
		$league = 1;
	}
	else if ($x->division <= 8){
		$league = 2;
	}
	
//	print "$x->fid ($league)\n";
	
	$yquery = "UPDATE statistics SET league = $league WHERE franchise_id = '$x->fid' AND season = $season AND organization = $org AND type = $type ";
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	


}




// OPS
$query = ("
	SELECT 
		league,
		$stat_avg->eq_stats as avg,
		$stat_slg->eq_stats as slg,
		$stat_obp->eq_stats as obp
	FROM statistics
	WHERE
		organization = $org
		AND type = $type
		AND season = $season
	GROUP BY league
	ORDER BY slg
");

// print $query;

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	if ($x->league == 1){

		$totalaa->avg = $x->avg;
		$totalaa->obp = $x->obp;
		$totalaa->slg = $x->slg;
	
	}

	if ($x->league == 2){

		$totalna->avg = $x->avg;
		$totalna->obp = $x->obp;
		$totalna->slg = $x->slg;
	
	}
	
//	$total->obp = $totalaa->obp + $totalna->obp;
//	$total->slg = $totalaa->slg + $totalna->slg;

}

$totalaa->ops = $totalaa->obp + $totalaa->slg;
$totalna->ops = $totalna->obp + $totalna->slg;


// ERA
$query = ("
	SELECT 
		league,
		$stat_era->eq_stats as era
	FROM statistics
	WHERE
		organization = $org
		AND type = $type
		AND season = $season
	GROUP BY league
");

// print $query;

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){


	if ($x->league == 1){

		$totalaa->era = $x->era;
	
	}

	if ($x->league == 2){

		$totalna->era = $x->era;
	
	}

}


print <<< CONTENT

American
AVG: $totalaa->avg
OBP: $totalaa->obp
SLG: $totalaa->slg
OPS: $totalaa->ops

ERA: $totalaa->era


National
AVG: $totalna->avg
OBP: $totalna->obp
SLG: $totalna->slg
OPS: $totalna->ops

ERA: $totalna->era


CONTENT;








?>