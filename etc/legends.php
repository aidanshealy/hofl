<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");




// Most Days
$query = "SELECT user_id, COUNT(user_id) as total FROM core WHERE type = 24 GROUP BY user_id ORDER BY user_id";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$owner = new Owner;
	$owner->Information($x->user_id);

	$team = new Team;
	$team->Code($owner->franchise_id);
	$team->Information($team->id);

	$user[] = $x->user_id;

	$count++;
	$total++;
	$class = $count % 2 ? "a": "b";

	print "$count. $owner->name_pad $team->name_pad \n";



}
unset($count);


print "\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "\n";


$query = "SELECT owner_id FROM owner WHERE franchise_id < 50 ORDER BY owner_id";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	if (!in_array($x->owner_id, $user)){

		$owner = new Owner;
		$owner->Information($x->owner_id);
	
		$team = new Team;
		$team->Code($owner->franchise_id);
		$team->Information($team->id);
	
		$user[] = $x->user_id;
	
		$count++;
		$total++;
		$class = $count % 2 ? "a": "b";
	
		print "$count. $owner->name_pad $team->name_pad \n";
		
	
	}
	
	
}
unset($count);



print "\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "\n";



// Most Days
$query = "SELECT COUNT(id) as votes, connect_player FROM core WHERE type = 24 GROUP BY connect_player ORDER BY votes DESC";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->connect_player);

	$count++;

	$chance = round(($x->votes/48 * 100),1);

	print "$count. $player->name_pad $x->votes ($chance)\n";

}
unset($count);


/*

*/


?>