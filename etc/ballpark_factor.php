<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = $_REQUEST['season'] ? $_REQUEST['season'] : 18;
$id = $_REQUEST['id'] ? $_REQUEST['id'] : 10;
$org = $_REQUEST['org'] ? $_REQUEST['org'] : 1;


//print "$team->name\n\n\n";

$zquery = ("
	SELECT
		franchise_id
	FROM 
		team_franchise
	LEFT JOIN team ON team_franchise.team_id = team.id
	WHERE 
		season = $season
		AND organization = $org
		AND franchise_id < 49
	ORDER BY team.city
");
$zresult = db::Q()->prepare($zquery);
$zresult->execute();

while ($z = $zresult->fetch(PDO::FETCH_OBJ)){

$id = $z->franchise_id;

$team = new Team;
$team->Code($id, 1, $season);
$team->Information($team->id);





// Start Single Team Process
$query = ("
	SELECT
		boxscore_id,
		home_id,
		away_id
	FROM scores
	WHERE 
		(home_id = $id
		OR away_id = $id)
		AND organization = $org
		AND season = $season
		AND type = 2
	ORDER BY boxscore_id
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	$count++;
	
//	print " - $x->boxscore_id (Game $count)\n";


	$yquery = ("
		SELECT
			boxscore_id,
			home_away,
			SUM(hitting_ab) as ab,
			SUM(hitting_s + hitting_db + hitting_tr + hitting_hr) as h,
			SUM(hitting_sh) as sh,
			SUM(hitting_sf) as sf,
			SUM(baserunning_cs) as cs,
			SUM(baserunning_run_scored) as runs
		FROM parsed_boxes
		WHERE 
			boxscore_id = $x->boxscore_id
			AND org = $org
		GROUP BY home_away
		ORDER BY boxscore_id
	");
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
		
		// Counting as home team
		if ($y->home_away == "H" && $x->home_id == $id){
//			print "     $team->abbreviation \t AB: $y->ab | H: $y->h | SH: $y->sh | SF: $y->sf | CS: $y->cs | Runs: $y->runs\n";
			$total->my_home_runs += $y->runs;
			$total->my_runs += $y->runs;

			$total->my_home_ab += $y->ab;
			$total->my_ab += $y->ab;

			$total->my_home_h += $y->h;
			$total->my_h += $y->h;

			$total->my_home_sh += $y->sh;
			$total->my_sh += $y->sh;

			$total->my_home_sf += $y->sf;
			$total->my_sf += $y->sf;

			$total->my_home_cs += $y->cs;
			$total->my_cs += $y->cs;

		}
		else if ($y->home_away == "A" && $x->away_id == $id){
//			print "     $team->abbreviation \t AB: $y->ab | H: $y->h | SH: $y->sh | SF: $y->sf | CS: $y->cs | Runs: $y->runs\n";
			$total->my_away_runs += $y->runs;
			$total->my_runs += $y->runs;

			$total->my_away_ab += $y->ab;
			$total->my_ab += $y->ab;

			$total->my_away_h += $y->h;
			$total->my_h += $y->h;

			$total->my_away_sh += $y->sh;
			$total->my_sh += $y->sh;

			$total->my_away_sf += $y->sf;
			$total->my_sf += $y->sf;

			$total->my_away_cs += $y->cs;
			$total->my_cs += $y->cs;

		}

		// Counting as away team
		else if ($y->home_away == "H" && $x->home_id != $id){
//			print "     AGT \t AB: $y->ab | H: $y->h | SH: $y->sh | SF: $y->sf | CS: $y->cs | Runs: $y->runs\n";
			$total->ag_away_runs += $y->runs;
			$total->ag_runs += $y->runs;

			$total->ag_away_ab += $y->ab;
			$total->ag_ab += $y->ab;

			$total->ag_away_h += $y->h;
			$total->ag_h += $y->h;

			$total->ag_away_sh += $y->sh;
			$total->ag_sh += $y->sh;

			$total->ag_away_sf += $y->sf;
			$total->ag_sf += $y->sf;

			$total->ag_away_cs += $y->cs;
			$total->ag_cs += $y->cs;

		}
		else if ($y->home_away == "A" && $x->away_id != $id){
//			print "     AGT \t AB: $y->ab | H: $y->h | SH: $y->sh | SF: $y->sf | CS: $y->cs | Runs: $y->runs\n";

			$total->ag_home_runs += $y->runs;
			$total->ag_runs += $y->runs;

			$total->ag_home_ab += $y->ab;
			$total->ag_ab += $y->ab;

			$total->ag_home_h += $y->h;
			$total->ag_h += $y->h;

			$total->ag_home_sh += $y->sh;
			$total->ag_sh += $y->sh;

			$total->ag_home_sf += $y->sf;
			$total->ag_sf += $y->sf;

			$total->ag_home_cs += $y->cs;
			$total->ag_cs += $y->cs;
		}
	}


	$yquery = ("
		SELECT
			home_away,
			COUNT(boxscore_id) as total
		FROM parsed_boxes
		WHERE 
			boxscore_id = $x->boxscore_id
			AND org = $org
			AND hitting_description LIKE '%double play%'
			AND hitting_description NOT LIKE '%flied out into a double play%'
			AND hitting_description NOT LIKE '%lined into a double play%'
		GROUP BY home_away
		ORDER BY boxscore_id
	");
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
		
		// Counting as home team
		if ($y->home_away == "H" && $x->home_id == $id){
			$total->my_home_dp += $y->total;
			$total->my_dp += $y->total;
		}
		else if ($y->home_away == "A" && $x->away_id == $id){
			$total->my_away_dp += $y->total;
			$total->my_dp += $y->total;
		}

		// Counting as away team
		else if ($y->home_away == "H" && $x->home_id != $id){
			$total->ag_away_dp += $y->total;
			$total->ag_dp += $y->total;
		}
		else if ($y->home_away == "A" && $x->away_id != $id){
			$total->ag_home_dp += $y->total;
			$total->ag_dp += $y->total;
		}
	}



}

// park_factor = ((Runs scored at home + Runs allowed at home)/((Home AB - Home H + Home CS + Home GDP + Home SH + Home SF) / 27))/((Runs scored on the road + Runs allowed on the road)/((Road AB - Road H + Road CS + Road GDP + Road SH + Road SF) / 27))
// $park_factor = (($total->my_home_runs + $total->ag_home_runs)/(($total->my_home_ab - $total->my_home_h + $total->my_home_cs + $total->my_home_dp + $total->my_home_sh + $total->my_home_sf) / 27))/(($total->my_away_runs + $total->ag_away_runs)/(($total->my_away_ab - $total->my_away_h + $total->my_away_cs + $total->my_away_dp + $total->my_away_sh + $total->my_away_sf) / 27));

$query = ("
	SELECT
		ballpark_id,
		ballpark_year
	FROM team_ballparks
	WHERE 
		franchise_id = $id
		AND organization = $org
		AND season = $season
");
$result = db::Q()->prepare($query);
$result->execute();

if ($x = $result->fetch(PDO::FETCH_OBJ)){

	$total->ballpark_id = $x->ballpark_id;
	$total->ballpark_year = $x->ballpark_year;

}






$home_outs = $total->my_home_ab - $total->my_home_h + $total->my_home_sh + $total->my_home_sf + $total->my_home_cs + $total->my_home_dp + $total->ag_home_ab - $total->ag_home_h + $total->ag_home_sh + $total->ag_home_sf + $total->ag_home_cs + $total->ag_home_dp; 
$home_g = $home_outs / 27; 

$away_outs = $total->my_away_ab - $total->my_away_h + $total->my_away_sh + $total->my_away_sf + $total->my_away_cs + $total->my_away_dp + $total->ag_away_ab - $total->ag_away_h + $total->ag_away_sh + $total->ag_away_sf + $total->ag_away_cs + $total->ag_away_dp; 
$away_g = $away_outs / 27; 

$park_factor = (($total->my_home_runs + $total->ag_home_runs)/($home_g))/(($total->my_away_runs + $total->ag_away_runs)/($away_g));



// Get Team Ballpark
$ballpark = new Ballpark;
$ballpark->Information($total->ballpark_id);


print <<< CONTENT
==========================================

$team->name
$ballpark->name $total->ballpark_year
Park Factor = $park_factor


CONTENT;

if ($_REQUEST['print']){
print <<< CONTENT
// Other Printable Data

park_factor = (($total->my_home_runs + $total->ag_home_runs)/($home_g))/(($total->my_away_runs + $total->ag_away_runs)/($away_g));

// Other Formulas
home_outs = $total->my_home_ab - $total->my_home_h + $total->my_home_sh + $total->my_home_sf + $total->my_home_cs + $total->my_home_dp + $total->ag_home_ab - $total->ag_home_h + $total->ag_home_sh + $total->ag_home_sf + $total->ag_home_cs + $total->ag_home_dp; 
home_g = $home_outs / 27; 

away_outs = $total->my_away_ab - $total->my_away_h + $total->my_away_sh + $total->my_away_sf + $total->my_away_cs + $total->my_away_dp + $total->ag_away_ab - $total->ag_away_h + $total->ag_away_sh + $total->ag_away_sf + $total->ag_away_cs + $total->ag_away_dp; 
away_g = $away_outs / 27; 



Runs: \t H: $total->my_home_runs \t A: $total->my_away_runs \t T: $total->my_runs
GDP: \t H: $total->my_home_dp \t\t A: $total->my_away_dp \t\t T: $total->my_dp
AB: \t H: $total->my_home_ab \t A: $total->my_away_ab \t T: $total->my_ab
H: \t H: $total->my_home_h \t A: $total->my_away_h \t T: $total->my_h
SH: \t H: $total->my_home_sh \t\t A: $total->my_away_sh \t\t T: $total->my_sh
SF: \t H: $total->my_home_sf \t\t A: $total->my_away_sf \t\t T: $total->my_sf
CS: \t H: $total->my_home_cs \t\t A: $total->my_away_cs \t\t T: $total->my_cs


Against
Runs: \t H: $total->ag_home_runs \t A: $total->ag_away_runs \t T: $total->ag_runs
GDP: \t H: $total->ag_home_dp \t\t A: $total->ag_away_dp \t\t T: $total->ag_dp
AB: \t H: $total->ag_home_ab \t A: $total->ag_away_ab \t T: $total->ag_ab
H: \t H: $total->ag_home_h \t A: $total->ag_away_h \t T: $total->ag_h
SH: \t H: $total->ag_home_sh \t\t A: $total->ag_away_sh \t\t T: $total->ag_sh
SF: \t H: $total->ag_home_sf \t\t A: $total->ag_away_sf \t\t T: $total->ag_sf
CS: \t H: $total->ag_home_cs \t\t A: $total->ag_away_cs \t\t T: $total->ag_cs



CONTENT;
}





// Update Statistics Team Table
$query = ("
	UPDATE statistics_team SET ballpark_factor = '$park_factor' WHERE franchise_id = '$team->franchise_id' AND season = '$season' AND type = 2 AND organization = '$org' LIMIT 1
");
$result = db::Q()->prepare($query);
$result->execute();

// End Single Team Process

unset($total);






}



