<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$org = $_REQUEST['org'] ? $_REQUEST['org'] : 1;
$type = 2;
$season = $_REQUEST['season'] ? $_REQUEST['season'] : 18;
$id = $_REQUEST['id'] ? $_REQUEST['id'] : 10;

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
		$stat_slg->eq_stats as slg,
		$stat_obp->eq_stats as obp
	FROM statistics
	WHERE
		organization = $org
		AND type = $type
		AND season = $season
		AND (position != 'SP' AND position != 'MR' AND position != 'CL')
	ORDER BY slg
");

// print $query;

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	$total->obp = $x->obp;
	$total->slg = $x->slg;

}

$total->ops = $total->obp + $total->slg;


// ERA
$query = ("
	SELECT 
		$stat_era->eq_stats as era
	FROM statistics
	WHERE
		organization = $org
		AND type = $type
		AND season = $season
		AND (position = 'SP' OR position = 'MR' OR position = 'CL')
	ORDER BY era
");

// print $query;

$result = db::Q()->prepare($query);
$result->execute();

if ($x = $result->fetch(PDO::FETCH_OBJ)){

	$total->era = $x->era;

}


print <<< CONTENT

OBP: $total->obp
SLG: $total->slg
OPS: $total->ops

ERA: $total->era

CONTENT;


// Lets get a player+



$query = ("
	SELECT
		a.pid
	FROM statistics a
	LEFT JOIN player b ON b.pid = a.pid
	WHERE season = $season AND organization = $org AND type = $type
	GROUP BY a.pid
	ORDER BY b.name_last, b.name_first
");


$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	if ($player->role == "B"){

		$player->Statistics($org,$season,$type);

		// $ops_plus = 100 * ($player_obp / $lg_obp + $player_slg / $lg_slg - 1) / (($park_factor + 1) / 2); 
		// $ops_plus = 100 * (0.376 / 0.341 + 0.715 / 0.476 - 1) / ((1.4119872260523 + 1) / 2) 
		// $ops_plus = 133.0637343 
	
		// Need to pull back a list of seasons which player has been a part of.
		$yquery = ("
			SELECT 
				ROUND((bH+bBB+bHBP)/(bAB+bBB+bHBP+bSF)+((bH-(bDB+bTR+bHR))+(bDB*2)+(bTR*3)+(bHR*4))/bAB,3) as ops,
				ROUND((bH+bBB+bHBP)/(bAB+bBB+bHBP+bSF),3) as obp,
				ROUND(((bH-(bDB+bTR+bHR))+(bDB*2)+(bTR*3)+(bHR*4))/bAB,3) as slg,
				bpa,
				id,
				franchise_id 
			FROM statistics WHERE pid = $player->pid AND type = $type AND season = $season AND organization = $org ORDER BY franchise_id");
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute();
		$count = $yresult->rowCount();
	
		if ($count > 1){
	
			while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
	
				$team = new Team;
				$team->Code($y->franchise_id, $org, $season);
				$team->Information($team->id);
				$team->Statistics($org,$season,$type);
	
				$player->season_bpf_single[] = $team->season_ballpark_factor;
				$player->season_ops_single[] = $y->ops;
				$player->season_obp_single[] = $y->obp;
				$player->season_slg_single[] = $y->slg;
				$player->season_pa_single[] = $y->bpa;
				$player->season_id_single[] = $y->id;
	
			}
			unset($team);
	
			$team->name_pad = str_pad("Multiple Teams ($count)", 30, " ", STR_PAD_RIGHT);
			$team->season_ballpark_factor = 1;
	
		}
		else {
			// If played full season for one team, and that wasn't this team, adjust statistics
	
			$team = new Team;
			$team->Code($player->season_fid, $org, $season);
			$team->Information($team->id);
			$team->Statistics($org,$season,$type);
		
		}
	
	
		if ($count > 1){
			// We have to work out the OPS+ when a player has played on multiple teams.
//			print "$player->name HERE\n";

			foreach ($player->season_pa_single as $k => $v){
				$pa = $v;
				$ops = $player->season_ops_single[$k];
				$obp = $player->season_obp_single[$k];
				$slg = $player->season_slg_single[$k];
				$bpf = $player->season_bpf_single[$k];
				$sid = $player->season_id_single[$k];
				$player->total_pa += $pa;

				$player->season_bopsp_single = round(100 * ((($obp / $total->obp) + ($slg / $total->slg) - 1) / (($bpf + 1) / 2)));
				$player->season_bopsp_array[$k] = round(100 * ((($obp / $total->obp) + ($slg / $total->slg) - 1) / (($bpf + 1) / 2)));
			
				$player->season_equation += $player->season_bopsp_single * $pa;




				$zquery = ("
					UPDATE statistics SET bopsp = '$player->season_bopsp_single' WHERE id = '$sid' LIMIT 1
				");
				$zresult = db::Q()->prepare($zquery);
				$zresult->execute();
				$player->value_insert = True;


			
//				$equation 
			
//				print "  $k = $pa - $ops / $total->ops [$bpf] ($player->season_bopsp_single) [$player->season_equation]\n";
			}

//			print "$player->total_pa\n";

			$player->season_bopsp = round($player->season_equation / $player->total_pa);
//			$player->season_bops = $player->season_bops;
			
		}
		else if ($player->season_g && $team->season_ballpark_factor){
			$player->season_bopsp = round(100 * ((($player->season_bobp / $total->obp) + ($player->season_bslg / $total->slg) - 1) / (($team->season_ballpark_factor + 1) / 2)));
		}
		else {
			$player->season_bopsp = "";
		}
		
		$player->season_bops_pad = str_pad($player->season_bops, 7, " ", STR_PAD_LEFT);
		$player->season_bopsp_pad = str_pad($player->season_bopsp, 3, " ", STR_PAD_LEFT);
		$player->season_g_pad = str_pad($player->season_g, 7, " ", STR_PAD_LEFT);
		$player->season_pa_pad = str_pad($player->season_bpa, 3, " ", STR_PAD_LEFT);
		
		if ($player->season_bops && $player->season_bopsp > 0){
			$batters .= "$player->name_pad $team->name_pad $player->season_g_pad \t $player->season_pa_pad \t $player->season_bops_pad \t $player->season_bopsp_pad \n";
			
			if (!$player->value_insert){
			
				$zquery = ("
					UPDATE statistics SET bopsp = '$player->season_bopsp', bopspp = '$player->season_bopsp' WHERE id = '$player->season_sid' LIMIT 1
				");
				$zresult = db::Q()->prepare($zquery);
				$zresult->execute();			

			}
			else {
			
				foreach ($player->season_bopsp_array as $k => $v){
	
					$sid = $player->season_id_single[$k];
					$pa = $player->season_pa_single[$k];
	
					$player->percentage = round(($pa / $player->total_pa) * $player->season_bopsp,1);
	//				print "$v - $sid - $pa ($player->total_pa) [$player->season_bopsp] $player->percentage \n";
					
					$zquery = ("
						UPDATE statistics SET bopspp = '$player->percentage' WHERE id = '$sid' LIMIT 1
					");
					$zresult = db::Q()->prepare($zquery);
					$zresult->execute();			
	
	
	
				}
			
			}

		}






	}
	else if ($player->role == "P"){









		$player->Statistics($org,$season,$type);

		// $ops_plus = 100 * ($player_obp / $lg_obp + $player_slg / $lg_slg - 1) / (($park_factor + 1) / 2); 
		// $ops_plus = 100 * (0.376 / 0.341 + 0.715 / 0.476 - 1) / ((1.4119872260523 + 1) / 2) 
		// $ops_plus = 133.0637343 
	
		// Need to pull back a list of seasons which player has been a part of.
		$yquery = ("
			SELECT 
				ROUND(per*9/(pinn/3),2) as era,
				(pinn/3) as inn,
				pbf,
				id,
				franchise_id 
			FROM statistics WHERE pid = $player->pid AND type = $type AND season = $season AND organization = $org ORDER BY franchise_id");
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute();
		$count = $yresult->rowCount();
	
		if ($count > 1){
	
			while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
	
				$team = new Team;
				$team->Code($y->franchise_id, $org, $season);
				$team->Information($team->id);
				$team->Statistics($org,$season,$type);
	
				$player->season_bpf_single[] = $team->season_ballpark_factor;
				$player->season_era_single[] = $y->era;
				$player->season_pbf_single[] = $y->pbf;
				$player->season_inn_single[] = $y->inn;
				$player->season_id_single[] = $y->id;
	
			}
			unset($team);
	
			$team->name_pad = str_pad("Multiple Teams ($count)", 30, " ", STR_PAD_RIGHT);
			$team->season_ballpark_factor = 1;
	
		}
		else {
			// If played full season for one team, and that wasn't this team, adjust statistics
	
			$team = new Team;
			$team->Code($player->season_fid, $org, $season);
			$team->Information($team->id);
			$team->Statistics($org,$season,$type);
		
		}
	
	
		if ($count > 1){
			// We have to work out the OPS+ when a player has played on multiple teams.
//			print "$player->name HERE\n";

			foreach ($player->season_pbf_single as $k => $v){
				$pbf = $v;
				$inn = $player->season_inn_single[$k];
				$era = $player->season_era_single[$k];
				$bpf = $player->season_bpf_single[$k];
				$sid = $player->season_id_single[$k];
				$player->total_inn += $inn;
				$player->total_pbf += $pbf;

				$player->season_perap_single = $era > 0 ? round(100 * ((($total->era / $era)) / (($bpf + 1) / 2))) : 100;
				$player->season_perap_array[$k] = $era > 0 ? round(100 * ((($total->era / $era)) / (($bpf + 1) / 2))) : 100;
			
				$player->season_equation += $player->season_perap_single * $pbf;




				$zquery = ("
					UPDATE statistics SET perap = '$player->season_perap_single' WHERE id = '$sid' LIMIT 1
				");
				$zresult = db::Q()->prepare($zquery);
				$zresult->execute();
				$player->value_insert = True;


			
//				$equation 
			
//				print "  $k = $pa - $ops / $total->ops [$bpf] ($player->season_bopsp_single) [$player->season_equation]\n";
			}

//			print "$player->total_pa\n";

			$player->season_perap = round($player->season_equation / $player->total_pbf);
//			$player->season_bops = $player->season_bops;
			
		}
		else if ($player->season_g && $team->season_ballpark_factor){
			$player->season_perap = $player->season_pera > 0 ? round(100 * ((($total->era / $player->season_pera)) / (($team->season_ballpark_factor + 1) / 2))) : 100;
//			print " = round(100 * ((($total->era / $player->season_pera)) / (($team->season_ballpark_factor + 1) / 2)));";
		}
		else {
			$player->season_perap = "";
		}
		
		$player->season_pera_pad = str_pad($player->season_pera, 7, " ", STR_PAD_LEFT);
		$player->season_perap_pad = str_pad($player->season_perap, 3, " ", STR_PAD_LEFT);
		$player->season_g_pad = str_pad($player->season_g, 7, " ", STR_PAD_LEFT);
		$player->season_inn_pad = str_pad($player->season_pinn, 3, " ", STR_PAD_LEFT);
		
		if ($player->season_pera && $player->season_perap > 0){
			$pitchers .= "$player->name_pad $team->name_pad $player->season_g_pad \t $player->season_inn_pad \t $player->season_pera_pad \t $player->season_perap_pad \n";
			
			if (!$player->value_insert){
			
				$zquery = ("
					UPDATE statistics SET perap = '$player->season_perap', perapp = '$player->season_perap' WHERE id = '$player->season_sid' LIMIT 1
				");
				$zresult = db::Q()->prepare($zquery);
				$zresult->execute();			

			}
			else {
			
				foreach ($player->season_perap_array as $k => $v){
	
					$sid = $player->season_id_single[$k];
					$pa = $player->season_pbf_single[$k];
	
					$player->percentage = round(($pa / $player->total_pbf) * $player->season_perap,1);
//					print "$v - $sid - $pa ($player->total_pbf) [$player->season_perap] $player->percentage \n";
					

					$zquery = ("
						UPDATE statistics SET perapp = '$player->percentage' WHERE id = '$sid' LIMIT 1
					");
					$zresult = db::Q()->prepare($zquery);
					$zresult->execute();			

	
	
	
				}
			
			}			
			
		}



	
	
	}
	
	
	
	unset($team,$player,$count);

}	



print <<< CONTENT

Batters\n
$batters




Pitchers\n
$pitchers

CONTENT;





?>