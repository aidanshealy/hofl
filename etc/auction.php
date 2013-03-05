<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$current = date("U") - 86400;
$season = SEASON;






// Last Bid Made
$yquery = "SELECT date_create FROM core WHERE season = $season AND type = 8 AND open = 1 ORDER BY date_create DESC LIMIT 1";

$yresult = db::Q()->prepare($yquery);
$yresult->execute();

if ($y = $yresult->fetch(PDO::FETCH_OBJ)){

	$last_bid = convert_timestamp($y->date_create,6);
	

}





// Pull Back All Free Agents
$query = ("
	SELECT owner_id FROM owner WHERE franchise_id < 50 AND prime = 1 ORDER BY name_first, name_last
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$owner = new Owner;
	$owner->Information($x->owner_id);
	
	$team = new Team;
	$team->Code($owner->franchise_id);
	$team->Information($team->id);

	$auction = new Auction;
	$auction->Funds($owner->franchise_id);

	// Auctions Won
	$yquery = "SELECT COUNT(rating_one) as total FROM core WHERE season = $season AND type = 8 AND connect_franchise = $owner->franchise_id AND open = 2 AND rating_two = 1 LIMIT 1;";

	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

  if ($y = $yresult->fetch(PDO::FETCH_OBJ)){

		for ($i = 1; $i <= $y->total; $i++) {
			$x->won .= "*";
		}

	}




	// How many winning
  $yquery = "SELECT COUNT(rating_one) as bids, SUM(rating_two) as lead FROM core WHERE season = $season AND type = 8 AND connect_franchise = $owner->franchise_id AND open = 1 AND rating_two = 1 GROUP BY connect_franchise";

	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

  if ($y = $yresult->fetch(PDO::FETCH_OBJ)){

		for ($i = 1; $i <= $y->bids; $i++) {
			$x->leader .= "*";
		}

	}


	// How many bids in the last 24 hours?
  $yquery = "SELECT COUNT(rating_one) as bids, SUM(rating_two) as lead FROM core WHERE season = $season AND type = 8 AND connect_franchise = $owner->franchise_id AND date_create > $current GROUP BY connect_franchise";

	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

  if ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
		for ($i = 1; $i <= $y->bids; $i++) {
			$x->stars .= "*";
		}
	
	}




	$row = "$owner->name_pad $team->name_pad " . str_pad($auction->display_total,12) . str_pad($auction->display_used,12) . str_pad($x->won,10) . str_pad($auction->display_hold,12) . str_pad($x->leader,10) . str_pad($auction->display_current,12) . " $x->stars \n";

	if ($auction->current == 50){
		$all .= $row;
		$count_all++;
	}
	else if ($auction->current > 10){
		$some .= $row;
		$count_some++;
	}
	else {
		$none .= $row;
		$count_none++;
	}

}



print <<< CONTENT
Last Bid: $last_bid

Owners with full cache
---
$all
# $count_all


Owners with something left
---
$some
# $count_some


Owners with nothing left
---
$none
# $count_none


CONTENT


?>