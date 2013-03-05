<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
// header("Content-type: text/plain");
header("Content-Type: text/csv");


$avg = new Statistics; $avg->Equations("bavg");
$obp = new Statistics; $obp->Equations("bobp");
$slg = new Statistics; $slg->Equations("bslg");
$ops = new Statistics; $ops->Equations("bops");


print "PID,LAH-ID,NAME FIRST,NAME LAST,YEAR,VINTAGE MATCH,PRIME POSITION,FRANCHISE NAME,PROTECTED,AVG,OBP,SLG,OPS,G,AB,H,2B,3B,HR,R,RBI,BB,K,SB\n";
	
$query = ("
	SELECT 
		team_players.pid,
		statistics_neutral.year,
		statistics_neutral.*,
		$avg->eq_stats as avg,
		$obp->eq_stats as obp,
		$slg->eq_stats as slg,
		$ops->eq_stats as ops
	FROM team_players 
	LEFT JOIN player ON player.pid = team_players.pid
	LEFT JOIN statistics_neutral ON statistics_neutral.pid = team_players.pid
	WHERE team_players.level != 8 AND statistics_neutral.bpa >= 350
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

	if ($player->ca_r){
		$player->column_c = "$player->ca_r:$player->ca_e:$player->ca_arm";
	}
	if ($player->fb_r){
		$player->column_fb = "$player->fb_r:$player->fb_e";
	}
	if ($player->sb_r){
		$player->column_sb = "$player->sb_r:$player->sb_e";
	}
	if ($player->tb_r){
		$player->column_tb = "$player->tb_r:$player->tb_e";
	}
	if ($player->ss_r){
		$player->column_ss = "$player->ss_r:$player->ss_e";
	}
	if ($player->lf_r){
		$player->column_lf = "$player->lf_r:$player->lf_e:$player->of_arm";
	}
	if ($player->cf_r){
		$player->column_cf = "$player->cf_r:$player->cf_e:$player->of_arm";
	}
	if ($player->rf_r){
		$player->column_rf = "$player->rf_r:$player->rf_e:$player->of_arm";
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

	print "$player->pid,$player->id,$player->name_first,$player->name_last,$x->year,$player->vintage_match,$player->position,$team->name,$player->yes,$x->avg,$x->obp,$x->slg,$x->ops,$x->g,$x->bab,$x->bh,$x->bdb,$x->btr,$x->bhr,$x->br,$x->brbi,$x->bbb,$x->bk,$x->bsb,$player->column_c,$player->column_fb,$player->column_sb,$player->column_tb,$player->column_ss,$player->column_lf,$player->column_cf,$player->column_rf\n";
}	





?>