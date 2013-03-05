<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = SEASON;
$type_stats = TYPE;

$team = new Team;
$team->Code(10);
$team->Information($team->id);

$query = ("
	SELECT
		season, 
		ROUND(pitching_er*9/(pitching_outs/3),2) as era,  
		ROUND((pitching_bb+pitching_h)/(pitching_outs/3),2) as whip,
		ROUND(starter_er*9/(starter_outs/3),2) as starter_era,  
		ROUND((starter_bb+starter_h)/(starter_outs/3),2) as starter_whip,
		ROUND(reliever_er*9/(reliever_outs/3),2) as reliever_era,  
		ROUND((reliever_bb+reliever_h)/(reliever_outs/3),2) as reliever_whip,
		ROUND(starter_pch/games,2) as starter_pch,  
		ROUND(reliever_pch/games,2) as reliever_pch,  
		pitching_bb as pitching_bb,  
		pitching_k as pitching_k,  

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND pitching_k < (
						SELECT
							pitching_k
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_k,								

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND pitching_bb < (
						SELECT
							pitching_bb
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_bb,								

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND(pitching_er*9/(pitching_outs/3),2) < (
						SELECT
							ROUND(pitching_er*9/(pitching_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_era,								

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND((pitching_bb+pitching_h)/(pitching_outs/3),2) < (
						SELECT
							ROUND((pitching_bb+pitching_h)/(pitching_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_whip,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND(starter_er*9/(starter_outs/3),2) < (
						SELECT
							ROUND(starter_er*9/(starter_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_starter_era,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND((starter_bb+starter_h)/(starter_outs/3),2) < (
						SELECT
							ROUND((starter_bb+starter_h)/(starter_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_starter_whip,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND(reliever_er*9/(reliever_outs/3),2) < (
						SELECT
							ROUND(reliever_er*9/(reliever_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_reliever_era,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND((reliever_bb+reliever_h)/(reliever_outs/3),2) < (
						SELECT
							ROUND((reliever_bb+reliever_h)/(reliever_outs/3),2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_reliever_whip,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND(starter_pch/games,2) < (
						SELECT
							ROUND(starter_pch/games,2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_starter_pch,							

		(
			SELECT
				COUNT(season)
			FROM parsed_boxes_team
			WHERE 
				season = $season
				AND type = $type_stats
				AND organization = $team->organization
				AND ROUND(reliever_pch/games,2) < (
						SELECT
							ROUND(reliever_pch/games,2)
						FROM parsed_boxes_team
						WHERE 
							season = $season
							AND type = $type_stats
							AND organization = $team->organization
							AND franchise_id = $team->franchise_id)
				) + 1 AS rank_reliever_pch,							

	franchise_id

	FROM parsed_boxes_team
	WHERE 
		season = $season
		AND type = $type_stats
		AND organization = $team->organization
		AND franchise_id = $team->franchise_id
");
$result = db::Q()->prepare($query);

print "$query";
$result->execute();

if ($x = $result->fetch(PDO::FETCH_OBJ)){

	print_r($x);

}


?>