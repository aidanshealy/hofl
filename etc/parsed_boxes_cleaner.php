<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = SEASON;
$type = TYPE;
$org = 1;

$query = ("
	SELECT
		pitcher_team_id, 
		season,
		type,
		org,
		COUNT(DISTINCT(boxscore_id)) AS games,
		SUM(pitching_outs) AS pitching_outs,
		SUM(pitching_er) AS pitching_er,
		SUM(pitching_h) AS pitching_h,
		SUM(pitching_bb) AS pitching_bb,
		SUM(pitching_k) AS pitching_k,

		(SELECT SUM(pitching_pch) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 1 AND a.pitcher_team_id = b.pitcher_team_id) AS starter_pch,
		(SELECT SUM(pitching_outs) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 1 AND a.pitcher_team_id = b.pitcher_team_id) AS starter_outs,
		(SELECT SUM(pitching_er) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 1 AND a.pitcher_team_id = b.pitcher_team_id) AS starter_er,
		(SELECT SUM(pitching_h) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 1 AND a.pitcher_team_id = b.pitcher_team_id) AS starter_h,
		(SELECT SUM(pitching_bb) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 1 AND a.pitcher_team_id = b.pitcher_team_id) AS starter_bb,

		(SELECT SUM(pitching_pch) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 0 AND a.pitcher_team_id = b.pitcher_team_id) AS reliever_pch,
		(SELECT SUM(pitching_outs) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 0 AND a.pitcher_team_id = b.pitcher_team_id) AS reliever_outs,
		(SELECT SUM(pitching_er) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 0 AND a.pitcher_team_id = b.pitcher_team_id) AS reliever_er,
		(SELECT SUM(pitching_h) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 0 AND a.pitcher_team_id = b.pitcher_team_id) AS reliever_h,
		(SELECT SUM(pitching_bb) FROM parsed_boxes b WHERE season = $season AND type = $type AND org = $org AND pitching_started = 0 AND a.pitcher_team_id = b.pitcher_team_id) AS reliever_bb

		FROM parsed_boxes a
	WHERE 
		season = $season
		AND type = $type
		AND org = $org
	GROUP BY pitcher_team_id

");
$result = db::Q()->prepare($query);

print "$query";
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$yquery = "SELECT franchise_id FROM parsed_boxes_team WHERE season = $season AND type = $type AND organization = $org AND franchise_id = $x->pitcher_team_id LIMIT 1";
	
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	if ($y = $yresult->fetch(PDO::FETCH_OBJ)){

		$dataset = array(
			$x->games,
			$x->pitching_outs,
			$x->pitching_er,
			$x->pitching_h,
			$x->pitching_bb,
			$x->pitching_k,
			$x->starter_pch,
			$x->starter_outs,
			$x->starter_er,
			$x->starter_h,
			$x->starter_bb,
			$x->reliever_pch,
			$x->reliever_outs,
			$x->reliever_er,
			$x->reliever_h,
			$x->reliever_bb
		);

		$zquery = ("
			UPDATE parsed_boxes_team 
			SET 
				games = ?, 
				pitching_outs = ?, 
				pitching_er = ?, 
				pitching_h = ?, 
				pitching_bb = ?, 
				pitching_k = ?, 
				starter_pch = ?, 
				starter_outs = ?, 
				starter_er = ?, 
				starter_h = ?, 
				starter_bb = ?, 
				reliever_pch = ?, 
				reliever_outs = ?, 
				reliever_er = ?, 
				reliever_h = ?, 
				reliever_bb = ? 
			WHERE season = $season AND type = $type AND organization = $org AND franchise_id = $x->pitcher_team_id LIMIT 1
		");
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute($dataset);

	}
	else {

		$dataset = array($x->pitcher_team_id,$x->season,$x->type,$x->org);

		$zquery = "INSERT INTO parsed_boxes_team (franchise_id, season, type, organization) VALUES (?,?,?,?)";
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute($dataset);
	
	}


}


?>