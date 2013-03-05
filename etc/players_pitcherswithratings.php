<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


// Pull Back All Players
$query = ("
	SELECT 
		team_players.pid
	FROM team_players
	LEFT JOIN player on player.pid = team_players.pid
	ORDER BY name_last, name_first
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($player->pid);

	$player->Ratings($player->pid);

	if ($player->role == "P"){

		if ($player->ca_r || $player->fb_r || $player->sb_r || $player->tb_r || $player->ss_r || $player->lf_r || $player->cf_r || $player->rf_r){

			foreach ($player->array_positions as $extra){
				if ($extra != "SP" && $extra != "RP"){
					$player->extra .= $extra . " ";
				}
			}


			print "$player->name_pad $player->extra\n";
		}
	
	}


}



?>