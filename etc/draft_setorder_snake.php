<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
// header("Content-Type: text/plain");


$teams = new Team;
$teams->All();

$draft = new Draft;
$draft->Information(1);

for ($i = 1; $i <= $draft->rounds; $i++) {


	if ($i % 2){

		$query = ("
			SELECT 
				draft_order.draft_order,
				draft_order.franchise_id,
				draft_selections.traded_to,
				draft_selections.selection_id
			FROM draft_order 
			LEFT JOIN draft_selections ON draft_selections.draft_id = $draft->id AND draft_selections.draft_round = $i AND draft_selections.franchise_to = draft_order.franchise_id
			WHERE draft_order.draft_id = $draft->id
			ORDER BY draft_order.draft_order 
		");

	}
	else {

		$query = ("
			SELECT 
				draft_order.draft_order,
				draft_order.franchise_id,
				draft_selections.traded_to,
				draft_selections.selection_id
			FROM draft_order 
			LEFT JOIN draft_selections ON draft_selections.draft_id = $draft->id AND draft_selections.draft_round = $i AND draft_selections.franchise_to = draft_order.franchise_id
			WHERE draft_order.draft_id = $draft->id
			ORDER BY draft_order.draft_order DESC 
		");
	
	}


	$result = db::Q()->prepare($query);
	$result->execute();

	while ($x = $result->fetch(PDO::FETCH_OBJ)){

		if ($x->selection_id){

			$get = new Team;
			$get->Code($x->franchise_id);
			$get->Information($get->id);
			
			$count++;
			$draft_pick++;

			print "$count - $draft_pick - $get->name\n";


	    $yquery = "UPDATE draft_selections SET draft_pick = '$draft_pick' WHERE selection_id = '$x->selection_id' ";
			$yresult = db::Q()->prepare($yquery);
			$yresult->execute();
		
		}
	
	}

}

?>