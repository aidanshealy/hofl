<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$since = date("U", mktime(0,0,0,12,1,2009));

$teams = new Team;
$teams->All();

print <<< CONTENT
Name                           Total     Total   Total   Total
                               Complete  Comnts  Score   Average

CONTENT;


foreach ($teams->list_franchise as $v){

	$team = new Team;
	$team->Code($v);
	$team->Information($team->id);



	$yquery = "SELECT id, title, one_id, two_id FROM trades WHERE (one_id=:fid OR two_id=:fid) AND (status = 3) AND completed = 1 AND date_update > $since ORDER BY date_update DESC";
	$yresult = db::Q()->prepare($yquery);
	$yresult->bindParam(":fid", $team->franchise_id);
	$yresult->execute();
			
	while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
		$team->total_trades++;

//		print "   $y->title\n";

		$zquery = "SELECT rating_other FROM core WHERE connect_trade = $y->id AND type = 52 ORDER BY date_update DESC";
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute();
			
		while ($z = $zresult->fetch(PDO::FETCH_OBJ)){

			$team->total_comments++;

			if ($y->one_id == $team->franchise_id){

				if ($z->rating_other == 1){ $team->total_value = $team->total_value + 5; }
				else if ($z->rating_other == 2){ $team->total_value = $team->total_value + 4; }
				else if ($z->rating_other == 3){ $team->total_value = $team->total_value + 3; }
				else if ($z->rating_other == 4){ $team->total_value = $team->total_value + 2; }
				else if ($z->rating_other == 5){ $team->total_value = $team->total_value + 1; }

			}
			else if ($y->two_id == $team->franchise_id){


				if ($z->rating_other == 1){ $team->total_value = $team->total_value + 1; }
				else if ($z->rating_other == 2){ $team->total_value = $team->total_value + 2; }
				else if ($z->rating_other == 3){ $team->total_value = $team->total_value + 3; }
				else if ($z->rating_other == 4){ $team->total_value = $team->total_value + 4; }
				else if ($z->rating_other == 5){ $team->total_value = $team->total_value + 5; }


			}
			
//			print "    - $z->rating_other\n";



		}
	
	}


	$team->total_average = number_format($team->total_value/$team->total_comments,2);


	$sort->array[] = array(
		$team->name_pad,
		$team->total_trades,
		$team->total_comments,
		$team->total_value,
		$team->total_average,
	);

	$sort->order[] = $team->total_average;

	// print "$team->name_pad $team->total_trades \t $team->total_comments \t $team->total_value \t $team->total_average \n";


}


arsort($sort->order);

// print_r($sort->array);


foreach ($sort->order as $k => $v){

//	print "$k\n";

	$name = $sort->array[$k];

	


	print "$name[0] $name[1] \t $name[2] \t $name[3] \t $name[4] \n";


}


?>