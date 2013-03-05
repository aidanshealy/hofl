<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");


$query = ("
	SELECT 
		*, 
		SUM(hitting_pa) as pa,
		SUM(hitting_hr) as hr,
		ROUND((SUM(hitting_s)+SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr))/SUM(hitting_ab),3) as avg,
		ROUND(((SUM(hitting_s)+SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr))+SUM(hitting_bb)+SUM(hitting_hbp))/(SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp)+SUM(hitting_sf)),3) as obp,
		ROUND((((SUM(hitting_s)+SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr))-(SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr)))+(SUM(hitting_db)*2)+(SUM(hitting_tr)*3)+(SUM(hitting_hr)*4))/SUM(hitting_ab),3) as slg,
		ROUND(((SUM(hitting_s)+SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr))+SUM(hitting_bb)+SUM(hitting_hbp))/(SUM(hitting_ab)+SUM(hitting_bb)+SUM(hitting_hbp)+SUM(hitting_sf)),3)+ROUND((((SUM(hitting_s)+SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr))-(SUM(hitting_db)+SUM(hitting_tr)+SUM(hitting_hr)))+(SUM(hitting_db)*2)+(SUM(hitting_tr)*3)+(SUM(hitting_hr)*4))/SUM(hitting_ab),3) as ops
	FROM parsed_boxes 
	WHERE
		season = '18' 
		AND org = '1' 
		AND type = 2
		AND hitting_strikes = 2
	GROUP BY player_id 
	HAVING SUM(hitting_pa) >= 160

	ORDER BY avg ,player_id 
	LIMIT 100 
");
$result = db::Q()->prepare($query);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->player_id);

	$count++;

	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);
	

//	print "$count. $player->link - $x->perpa ($x->pitches/$x->pa)\n";

	$player->name = str_pad($player->name, 20);
	$countn = str_pad($count, 3, " ", STR_PAD_LEFT);
	$x->avg = format_average($x->avg);
	$x->ops = format_average($x->ops);

	$results .= "$countn. $player->name &#9 $team->abbreviation &#9 $x->pa &#9 $x->avg &#9 $x->ops &#9 $x->hr\n";

}


print <<< CONTENT
<pre>
Rk. &#9 Name &#9&#9&#9 Team &#9 PA &#9 AVG &#9 OPS &#9 HR
$results
</pre>
CONTENT;

?>