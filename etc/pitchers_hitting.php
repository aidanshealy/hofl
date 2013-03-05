<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


print "Rk \t Name \t\t\t Team \t AVG \t OPS \t HR \t R \t RBI \n";

// Pull Back All Players
$query = ("
	SELECT 
		SUM(bhr) as bhr,
		SUM(br) as br,
		SUM(brbi) as brbi,
		ROUND(SUM(bh)/SUM(bab),3) as bavg,
		ROUND((SUM(bH)+SUM(bBB)+SUM(bHBP))/(SUM(bAB)+SUM(bBB)+SUM(bHBP)+SUM(bSF)),3) as bobp,
		ROUND(((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) as bslg,
		ROUND((SUM(bH)+SUM(bBB)+SUM(bHBP))/(SUM(bAB)+SUM(bBB)+SUM(bHBP)+SUM(bSF))+((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) as bops,
		statistics.pid,
		SUM(bab)+SUM(bbb)+SUM(bhbp)+SUM(bsh)+SUM(bsf) as pa
		FROM statistics
		LEFT JOIN player_role ON player_role.pid = statistics.pid
		WHERE organization = 1 AND type = 2 AND player_role.role = 'P'
		
		GROUP BY pid
		HAVING pa >= 60
		ORDER BY bops
		
		
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	
	$rank++;
	
	$team = new Team;
	$team->Code($player->franchise_id);
	$team->Information($team->id);
	
//	$ops = format_average($x->bops);
	
	
	print "$rank. \t $player->name_pad \t $team->abbreviation \t $x->bavg \t $x->bops \t $x->bhr \t $x->br \t $x->brbi\n";



}


?>