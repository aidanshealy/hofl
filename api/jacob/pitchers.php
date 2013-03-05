<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
//header("Content-type: text/plain");
header("Content-Type: text/csv");


$era = new Statistics; $era->Equations("pera");
$whip = new Statistics; $whip->Equations("pwhip");


print "PID,LAH-ID,NAME FIRST,NAME LAST,YEAR,VINTAGE MATCH,PRIME POSITION,FRANCHISE NAME,PROTECTED,ERA,WHIP,G,GS,W,L,S,INN,H,HR,R,ER,BB,K,Starter Rating,Reliever Rating\n";
	
$query = ("
	SELECT 
		team_players.pid,
		statistics_neutral.year,
		statistics_neutral.*,
		$era->eq_stats as era,
		$whip->eq_stats as whip
	FROM team_players 
	LEFT JOIN player ON player.pid = team_players.pid
	LEFT JOIN statistics_neutral ON statistics_neutral.pid = team_players.pid
	WHERE team_players.level != 8 AND statistics_neutral.pinn >= 90
	GROUP BY team_players.pid, statistics_neutral.year
	ORDER BY player.name_last, player.name_first, statistics_neutral.year
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($x->pid);
	$player->Protect($x->pid);
	$player->Ratings($x->pid,0,$x->year);
	
	$player->yes = $player->protected ? "Yes":"No";

	if ($player->rating_starter){
		$player->column_sp = "$player->rating_starter";
	}
	if ($player->rating_reliever){
		$player->column_rp = "$player->rating_reliever";
	}




	$player->vintage_match = $player->vintage == $x->year ? "Active" : NULL;
	
	if ($player->franchise_id){
		$team = new Team;
		$team->Code($player->franchise_id);
		$team->Information($team->id);
	}
	else {
		$team->name = "Free Agent";
		$player->level = 0;
	}

	print "$player->pid,$player->id,$player->name_first,$player->name_last,$x->year,$player->vintage_match,$player->position,$team->name,$player->yes,$x->era,$x->whip,$x->g,$x->gs,$x->pw,$x->pl,$x->ps,$x->pinn,$x->ph,$x->phr,$x->pr,$x->per,$x->pbb,$x->pk,$player->column_sp,$player->column_rp\n";
}	





?>