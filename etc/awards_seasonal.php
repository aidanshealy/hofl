<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$org = new Organization;
$org->Information(1);

// Get All of the Awards to build navigation
$query = "SELECT award_id, name, short, league FROM awards_type WHERE seasonal = 1 ORDER BY sort";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	if ($x->league == 1){
		$league = $org->league_one . " ";
	}
	else if ($x->league == 2){
		$league = $org->league_two . " ";
	}
	else {
		unset($league);
	}

	print "$league$x->name\n";





	// Get Award Nominees
	$yquery = ("
		SELECT 
			connect_player,
			a.id
		FROM core a
		LEFT JOIN player b ON a.connect_player = b.pid
		WHERE 
			type = 26 AND misc = $x->award_id 
		ORDER BY b.name_last, b.name_first
	");
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	
	while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
		$player = new Player;
		$player->Information($y->connect_player);


		// Add up votes for this player
		$zquery = ("
			SELECT 
				SUM(rating_one) as total,
				COUNT(rating_one) as votes
			FROM core
			WHERE 
				type = 27 AND misc = $x->award_id AND connect_player = $player->pid
		");
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute();
		
		
		while ($z = $zresult->fetch(PDO::FETCH_OBJ)){
			$player->total = $z->total;
			$player->votes = $z->votes;
		}

		print "  $player->name_pad \t\t $player->total \t $player->votes \t \n";

	}



	// How Many Owners Have Voted


	// Get Owner List
	$zquery = ("
		SELECT
			owner_id,
			name_first,
			name_last,
			franchise_id
		FROM
			owner
		WHERE
			franchise_id > 0 AND franchise_id <= 50 AND franchise_id IS NOT NULL
		ORDER BY owner.name_first, owner.name_last
	");
	$zresult = db::Q()->prepare($zquery);
	$zresult->execute();

	while ($z = $zresult->fetch(PDO::FETCH_OBJ)){
	
		$owner = new Owner;
		$owner->Information($z->owner_id);
		
	
		$dataset = array($z->owner_id,$x->award_id);
		$yquery = "SELECT SUM(rating_one) as total FROM core WHERE user_id = ? AND type = 27 AND misc = ?";
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute($dataset);
//		$total = $result->rowCount();

//		print_r($dataset);
	
		if ($y = $yresult->fetch(PDO::FETCH_OBJ)){
			if ($y->total > 0){
				$owners->voted .= "      $owner->name_pad \t $y->total\n";
				$owners->voted_total++;
			}
			else {
				$owners->not .= "      $owner->name_pad\n";
				$owners->not_total++;
			}
		}

	}


	print "    - Voted ($owners->voted_total)\n" . $owners->voted;
	print "\n";
	print "    - Not Voted ($owners->not_total)\n" . $owners->not;
/*
	$zquery = ("
		SELECT 
			DISTINCT(user_id) as owners
		FROM core
		WHERE 
			type = 27 AND misc = $x->award_id
		GROUP BY user_id
	");
	$zresult = db::Q()->prepare($zquery);
	$zresult->execute();
	
	
	while ($z = $zresult->fetch(PDO::FETCH_OBJ)){
		$player->owners[] = $z->owners;
	}

	print_r($player->owners);
*/

//	print "  - Voters ($player->owners)\n";


	print "\n\n";

	unset($owners);
}



?>