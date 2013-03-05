<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

// Find Rookie of the Year
$query = "SELECT pid FROM team_players ";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics();


	if ($player->season_g > 0){
		$player->Career();

		// Subtract Player Statistics
		if ($player->role == "B"){

//			print "$player->link $player->season_bab & $player->career_bab<br>";

			$stat_requirement = $player->career_bab - $player->season_bab;
			
			$stat_requirement = $stat_requirement ? $stat_requirement : "---";
			
			if ($stat_requirement < 220){
				if (($player->season_bops > ".750")&&($player->season_bab > 300)){
//					$results_batter .= "<b>$player->link</b> $player->season_ops - $stat_requirement ($player->season_ab)<br>";

					$total++;
					$results_batter .= ("$player->name_pad $stat_requirement \t $player->season_g \t $player->season_bavg \t $player->season_bops \t $player->season_bab \t $player->season_bh \t $player->season_bdb \t $player->season_btr \t $player->season_bhr \t $player->season_br \t $player->season_brbi  \t $player->season_bbb \t $player->season_bk \t $player->season_bsb \n");
				}
				else {
//					$results_batter .= "$player->link $player->season_ops - $stat_requirement ($player->season_ab)<br>";
				}
			}
			else {
//				$results_batter .= "$player->name $player->season_stat_2 - $stat_requirement ($player->season_stat_6)<br>";
			}

		}
		else {
		
			$stat_requirement = round(($player->career_pouts - $player->season_pouts)/3,1);
		
			if ($stat_requirement < 50){
				if (($player->season_pouts > 180)&&($player->season_pwhip <= 1.5)){
					$stat_requirement = $stat_requirement ? $stat_requirement : "---";
//					$results_pitcher .= "<b>$player->link</b> $player->season_w $player->season_l $player->season_era - $stat_requirement ($player->season_inn)<br>";
					$total++;
					$results_pitcher .= ("$player->name_pad $stat_requirement \t $player->season_g \t $player->season_pera \t $player->season_pwhip \t $player->season_pw \t $player->season_pl \t $player->season_ps \t $player->season_pcg \t $player->season_psh \t $player->season_pinn \t $player->season_ph \t $player->season_phr \t $player->season_pbb \t $player->season_pk\n");
				}
				else {
//					$results_pitcher .= "$player->link $player->season_w $player->season_l $player->season_era - $stat_requirement ($player->season_inn)<br>";
				}
			}
			else {
//				$results_pitcher .= "$player->name $player->season_stat_1 $player->season_stat_2 - $stat_requirement ($player->season_stat_7)<br>";
			}

		}

	}

}


print <<< HTML

Batters \t     CAB \t G \t AVG \t OPS \t AB \t H \t DB \t TR \t HR \t R \t RBI \t BB \t K \t SB
$results_batter

Pitchers \t     CINN \t G \t ERA \t WHIP \t W \t L \t S \t CG \t SH \t INN \t H \t HR \t BB \t K
$results_pitcher

Total Rookies: $total
HTML;



?>