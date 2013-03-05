<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

function newpad($value){
	return str_pad($value, 3, " ", STR_PAD_LEFT);
}

$draft = 24;
$oid = OID;
$fid = FID;

$completed = array();


// Pull Back All Free Agents
$query = ("
	SELECT 
		pid,
		draft_pick,
		draft_round,
		franchise_to,
		traded_to,
		franchise_from
	FROM draft_selections
	WHERE draft_id = $draft
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

	$from = new Team;
	$from->Code($x->franchise_from);
	$from->Information($from->id);

	$drafted[$x->franchise_from]++;

	// Figure out when a team hits a total of 5 players
	$final = array_diff_assoc($drafted, $completed);

	foreach($final as $v){
		if ($v == 5){

//			print " -- Team ($from->abbreviation) hits five, protect three -- \n";
			$completed[$x->franchise_from] = 5;
			$search = 1;
		}

	}


	foreach($drafted as $k => $v){
		if ($v == 6 && $k == $x->franchise_from && !in_array($x->franchise_id, $closed)){

			$closed[] = $x->franchise_from;
			$star = "*";

		}
	}

	// Print Results
	print "$draft_round $draft_pick $player->name_pad $team->abbreviation \t $from->abbreviation $star \t\n";


	if ($search == 1){

		print "\n        $from->name hit five players  \n";

		$yquery = ("
			SELECT 
				pid
			FROM draft_protected
			WHERE draft_id = $draft AND franchise_id = '$x->franchise_from' AND pre = 0
		");
		$yresult = db::Q()->query($yquery);
		
		while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
		
			$protect = new Player;
			$protect->Information($y->pid);
		
			print "         $protect->name ($protect->points)\n";
		
		}

		print "\n";
	
		unset($search);
	}

	unset($star);



}


?>