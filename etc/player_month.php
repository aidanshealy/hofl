<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$type = $_GET['type'] ? $_GET['type'] : "B";
$org = $_GET['org'] ? $_GET['org'] : 1;
$month = $_GET['month'] ? $_GET['month'] : 4;
$year = $_GET['year'] ? $_GET['year'] : YEAR;

print "	<link rel='stylesheet' href='/objects/css/global.css' type='text/css' media='screen' />";

print "<body class='body-login'>";


if ($type == "B"){

	// Lets get Batters of the Month First
	$query = ("
		SELECT 
			*, 
			SUM(hitting_hr) as hr,
			SUM(hitting_db) as db,
			SUM(hitting_tr) as tr,
			SUM(hitting_rbi) as rbi,
			SUM(hitting_bb) as bb,
			SUM(hitting_k) as k,
			ROUND(SUM(hitting_s+hitting_db+hitting_tr+hitting_hr)/SUM(hitting_ab),3) as avg,
			ROUND(((((((SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp))*2.4)+(SUM(hitting_s+hitting_db+hitting_tr+hitting_hr)+SUM(hitting_bb)-SUM(baserunning_cs)+SUM(hitting_hbp)))*(((SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp))*3)+(((SUM(hitting_s+hitting_db+hitting_tr+hitting_hr)-SUM(hitting_db)-SUM(hitting_tr)-SUM(hitting_hr))+(SUM(hitting_db)*2)+(SUM(hitting_tr)*3)+(SUM(hitting_hr)*4))+(0.24*(SUM(hitting_bb)+SUM(hitting_hbp)))+(0.62*SUM(baserunning_sb))-(0.03*SUM(hitting_k)))))/((SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp))*9))-((SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp))*0.9)),1) as rc,
			ROUND((SUM(hitting_s+hitting_db+hitting_tr+hitting_hr)+SUM(hitting_bb)+SUM(hitting_hbp))/(SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp)+SUM(hitting_sf))+((SUM(hitting_s+hitting_db+hitting_tr+hitting_hr)-(SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr)))+(SUM(hitting_db)*2)+(SUM(hitting_tr)*3)+(SUM(hitting_hr)*4))/SUM(hitting_ab),3) as ops
		FROM parsed_boxes 
		WHERE player_id > 0 AND hitting_pa > 0 AND org = $org AND game_year = $year AND game_month = '$month'
		GROUP BY player_id 
		HAVING SUM(hitting_pa) >= 80
	
		ORDER BY rc DESC,player_id 
		LIMIT 150
	");
	$result = db::Q()->prepare($query);
	$result->execute();
	
	while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
		$player = new Player;
		$player->Information($x->player_id);
	
		$team = new Team;
		$team->Code($player->franchise_id,$org);
		$team->Information($team->id);
	
	
		$count++;
	
		$stat_line = format_average($x->avg)."avg " . format_average($x->ops)."ops " . $x->db."db " . $x->tr."tr " . $x->hr."hr " . $x->rbi."rbi " . $x->bb."bb " . $x->k."k " . $x->rc."rc ";
	

		$results = "<div class='fl sp'>$player->photo_small</div><div class='fl' style='padding-top: 2px;'><strong>$player->link</strong> $team->name <div class='dim'>$stat_line</div></div><div class='cl'>&nbsp;</div>";

		if ($team->division == 1 || $team->division == 2 || $team->division == 3 || $team->division == 4){
			$results_al .= "$results<input type='text' class='field' value=\"$results\"><div class='cl'>&nbsp;</div>";
		}
		else {
			$results_nl .= "$results<input type='text' class='field' value=\"$results\"><div class='cl'>&nbsp;</div>";
		}
	
//		print $results;
//		print "<input type='text' class='form-field' value=\"$results\"><div class='cl'>&nbsp;</div>";
	
	
	//	print "$count. $player->pid - <strong>$player->name</strong> $team->abbreviation $x->ops / $x->rc<br>";
	
	
	}

}
else {

//			 


	// Lets get Pitcher here
	$query = ("
		SELECT 
			*,
			COUNT(pitcher_id) as games,
			SUM(pitching_er) as er,
			SUM(pitching_h) as h,
			SUM(pitching_bb) as bb,
			SUM(pitching_k) as k,
			SUM(pitching_pch) as pch,
			SUM(pitching_win) as w,
			SUM(pitching_loss) as l,
			ROUND(SUM(pitching_er)*9/((SUM(truncate(pitching_inn, 0) * 3) + (truncate(pitching_inn * 10, 0) - (truncate(pitching_inn, 0) * 10)))/3),2) AS era,
			ROUND((SUM(pitching_bb)+SUM(pitching_h))/ROUND((SUM(truncate(pitching_inn, 0) * 3) + (truncate(pitching_inn * 10, 0) - (truncate(pitching_inn, 0) * 10)))/3,1),3) as whip, 
			SUM(truncate(pitching_inn, 0) * 3) + (truncate(pitching_inn * 10, 0) - (truncate(pitching_inn, 0) * 10)) AS outs,
			ROUND((SUM(truncate(pitching_inn, 0) * 3) + (truncate(pitching_inn * 10, 0) - (truncate(pitching_inn, 0) * 10)))/3,1) AS inn

		FROM parsed_boxes 
		WHERE pitcher_id > 0 AND player_id = 0 AND org = $org AND game_year = $year AND game_month = '$month'
		GROUP BY pitcher_id 
		HAVING outs >= 100
	
		ORDER BY whip,pitcher_id 
		LIMIT 150
	");
	$result = db::Q()->prepare($query);
	$result->execute();
	
	while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
		$player = new Player;
		$player->Information($x->pitcher_id);
	
		$team = new Team;
		$team->Code($player->franchise_id,$org);
		$team->Information($team->id);
	
	
		$ppg = round($x->pch/$x->games,1);
	
		$count++;
	
		$stat_line = $x->w."-".$x->l . " " . format_pitcher($x->era)."era " . format_pitcher($x->whip)."whip " . $x->inn."inn " . $x->h."h " . $x->er."er " . $x->bb."bb " . $x->k."k ". $ppg."ppg ";
		
		if ($team->division == 1 || $team->division == 2 || $team->division == 3 || $team->division == 4){
			$results = "<div class='fl sp'>$player->photo_small</div><div class='fl' style='padding-top: 2px;'><strong>$player->link</strong> $team->name <div class='dim'>$stat_line</div></div><div class='cl'>&nbsp;</div>";
			$results_al .= "$results<input type='text' class='field' value=\"$results\"><div class='cl'>&nbsp;</div>";
		}
		else {
			$results = "<div class='fl sp'>$player->photo_small</div><div class='fl' style='padding-top: 2px;'><strong>$player->link</strong> $team->name <div class='dim'>$stat_line</div></div><div class='cl'>&nbsp;</div>";
			$results_nl .= "$results<input type='text' class='field' value=\"$results\"><div class='cl'>&nbsp;</div>";
		}
		
//		print $results;
	
	
	//	print "$count. $player->pid - <strong>$player->name</strong> $team->abbreviation $x->ops / $x->rc<br>";
	
	
	}




}

print <<< CONTENT

<table>
<tr valign='top'>
	<td>
		$results_al
	</td>
	<td>
		$results_nl
	</td>
</tr>
</table>

CONTENT;



?>