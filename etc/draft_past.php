<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

function newpad($value){
	return str_pad($value, 3, " ", STR_PAD_LEFT);
}

$oid = OID;
$fid = FID;

// Pull Back All Free Agents
$query = ("
	SELECT 
		pid,
		draft_pick,
		draft_round,
		franchise_to,
		traded_to
	FROM draft_selections
	WHERE draft_id = 22
	ORDER by draft_pick
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$fran = $x->traded_to ? $x->traded_to : $x->franchise_to;

	$count++;
	$draft_pick = $x->draft_pick ? str_pad($x->draft_pick, 3, " ", STR_PAD_LEFT) . "." : " -- ";
	$draft_round = $x->draft_round ? str_pad($x->draft_round, 1, " ", STR_PAD_LEFT) . "." : " -- ";

	$player = new Player;
	$player->Information($x->pid);
	$player->Statistics();

	$team = new Team;
	$team->Code($fran);
	$team->Information($team->id);

	$player->season_bavg = str_pad($player->season_bavg, 5, " ", STR_PAD_LEFT);
	$player->season_bobp = str_pad($player->season_bobp, 5, " ", STR_PAD_LEFT);
	$player->season_bops = str_pad($player->season_bops, 5, " ", STR_PAD_LEFT);

	$player->season_bopspp = newpad(round($player->season_bopspp));
	$player->season_bab = newpad(round($player->season_bab));
	$player->season_bh = newpad(round($player->season_bh));
	$player->season_bhr = newpad(round($player->season_bhr));
	$player->season_br = newpad(round($player->season_br));
	$player->season_brbi = newpad(round($player->season_brbi));
	$player->season_bbb = newpad(round($player->season_bbb));
	$player->season_bk = newpad(round($player->season_bk));
	$player->season_bsb = newpad(round($player->season_bsb));



	if ($player->role == "B"){
		$hitters .= "$draft_round $draft_pick $player->name_pad $team->abbreviation \t $player->season_g \t $player->season_bavg  $player->season_bobp  $player->season_bops \t $player->season_bopspp \t $player->season_bab \t $player->season_bh \t $player->season_bhr \t $player->season_br \t $player->season_brbi \t $player->season_bbb \t $player->season_bk \t $player->season_bsb\n";	
	}
	else if ($player->role == "P"){
		$pitchers .= "$draft_round $draft_pick $player->name_pad $team->abbreviation \t $player->season_g \t $player->season_pera \t $player->season_pwhip \t $player->season_perapp \t $player->season_pw \t $player->season_pl \t $player->season_ps \t $player->season_pinn \t $player->season_ph \t $player->season_phr \t $player->season_pbb \t $player->season_pk\n";
	}
//	print " ($team->name) \n";


}


print <<< CONTENT
Rd  Pk  Name                 Team \t G \t   AVG \t OBP \t OPS \t OPS+ \t AB \t H \t HR \t R \t RBI \t BB \t K \t SB\n
$hitters



Rd  Pk  Name                 Team \t G \t   ERA \t WHIP \t ERA+ \t W \t L \t S \t INN \t H \t HR \t BB \t K \n
$pitchers

CONTENT;


?>