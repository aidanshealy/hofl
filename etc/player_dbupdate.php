<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$season = SEASON;
$organization = 1;


// Most Days
$query = "SELECT id FROM player ORDER BY name_last, name_first";

$result = db::Q()->prepare($query);
$result->bindParam(1, $season);
$result->bindParam(2, $organization);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$yquery = "SELECT pid FROM player_ WHERE id = '$x->id' LIMIT 1";
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();
	
	if ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
	
		$zquery = "UPDATE player SET pid = '$y->pid' WHERE id = '$x->id' LIMIT 1";
		$zresult = db::Q()->prepare($zquery);
		$zresult->execute();
	
	
	}

	print "$x->id\n";

}

?>