<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$date_base = date("U",mktime(0,0,0,3,31,2013));

// This script moves games based on skipped days from the building of the schedule.



// Now, pull out three days from now 12 teams and change those dates to current date.
$query = ("
	SELECT 
		*
	FROM scores_temp
	ORDER BY date, id
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	print "$x->id,$x->date,$x->number,$x->away_id,$x->home_id,$x->type,$x->played\n";
}


?>