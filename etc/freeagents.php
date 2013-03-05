<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$pos = $_REQUEST['pos'] ? $_REQUEST['pos'] : "top";

if ($pos == "top"){ unset($pos); }

if ($pos == "catcher"){ $su = "CA"; }
else if ($pos == "firstbase"){ $su = "1B"; }
else if ($pos == "secondbase"){ $su = "2B"; }
else if ($pos == "thirdbase"){ $su = "3B"; }
else if ($pos == "shortstop"){ $su = "SS"; }
else if ($pos == "leftfield"){ $su = "LF"; }
else if ($pos == "centerfield"){ $su = "CF"; }
else if ($pos == "rightfield"){ $su = "RF"; }
else if ($pos == "dh"){ $su = "DH"; }
else if ($pos == "starter"){ $su = "SP"; }
else if ($pos == "reliever"){ $su = "RP"; }

print "Free Agents \t\t Vin \t AVG \t OBP \t SLG \t DB \t TR \t HR \t BB \t SO \t Rating \n";

// Pull Back All Free Agents
$query = ("
	SELECT 
		team_players.pid
	FROM team_players
	LEFT JOIN player on player.pid = team_players.pid
	WHERE level = 8
	ORDER BY name_last, name_first
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($player->pid);
	$player->Ratings($player->pid, 0, $player->vintage);
	$player->Historicals($player->vintage);

	if ($pos == "catcher"){ $player->rate = "$player->ca_r " . str_pad($player->ca_e,3," ",STR_PAD_LEFT) . " $player->ca_arm"; }
	else if ($pos == "firstbase"){ $player->rate = "$player->fb_r " . str_pad($player->fb_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "secondbase"){ $player->rate = "$player->sb_r " . str_pad($player->sb_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "thirdbase"){ $player->rate = "$player->tb_r " . str_pad($player->tb_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "shortstop"){ $player->rate = "$player->ss_r " . str_pad($player->ss_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "leftfield"){ $player->rate = "$player->lf_r " . str_pad($player->lf_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "centerfield"){ $player->rate = "$player->cf_r " . str_pad($player->cf_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "rightfield"){ $player->rate = "$player->rf_r " . str_pad($player->rf_e,3," ",STR_PAD_LEFT); }
	else if ($pos == "dh"){ unset($player->rate); }
	else if ($pos == "starter"){ $player->rate = "$player->rating_starter $player->rating_reliever"; }
	else if ($pos == "reliever"){ $player->rate = "$player->rating_reliever"; }


//			if (!$pos||in_array($su,$player->array_positions)){
	if (!$pos||$player->position == $su){
		$count++;
		print "$player->name_pad $player->position $player->vintage \n";
//		print "$player->name_pad $player->position $player->vintage \t $player->vintage_avg \t $player->vintage_obp \t $player->vintage_slg \t $player->vintage_db \t $player->vintage_tr \t $player->vintage_hr \t $player->vintage_bb \t $player->vintage_k \t $player->rate\n";

	}

}

print $count . " players";

?>