<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = SEASON;
$organization = 1;


// Most Days
$query = "SELECT franchise_id, SUM(duration) as days FROM injuries WHERE season = ? AND organization = ? GROUP BY franchise_id ORDER BY days DESC";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id);
	$team->Information($team->id);

	$count++;
	$class = $count % 2 ? "a": "b";

	print "$count. $team->name_pad ($x->days)\n";

	$games->lost .= ("
		<tr>
			<td>$count.</td>
			<td>$team->link</td>
			<td class='r'>$x->days</td>
		</tr>
	");

}
unset($count);


print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";
print "////////////////////////////////////\n";


// Most Injuries
$query = "SELECT franchise_id, COUNT(injury_id) as total FROM injuries WHERE season = ? AND organization = ? GROUP BY franchise_id ORDER BY total DESC";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id);
	$team->Information($team->id);

	$count++;
	$class = $count % 2 ? "a": "b";

	print "$count. $team->name_pad ($x->total)\n";


	$games->total .= ("
		
		
		<tr>
			<td>$count.</td>
			<td>$team->link</td>
			<td class='r'>$x->total</td>
		</tr>
	");

}
unset($count);

?>