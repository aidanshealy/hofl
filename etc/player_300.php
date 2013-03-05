<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$play = array();

$query = ("
	SELECT 
		pid,
		SUM(bpa) as pa,
		year,
		ROUND((SUM(bH)+SUM(bBB)+SUM(bHBP))/(SUM(bAB)+SUM(bBB)+SUM(bHBP)+SUM(bSF))+((SUM(bH)-(SUM(bDB)+SUM(bTR)+SUM(bHR)))+(SUM(bDB)*2)+(SUM(bTR)*3)+(SUM(bHR)*4))/SUM(bAB),3) as ops
	FROM 
		statistics_vintages
	WHERE
		year >= 1901
		AND (year != 1914 || year != 1915)
	GROUP BY pid, year
	HAVING SUM(bpa) >= 300
	ORDER BY ops DESC
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);

	if (!in_array($player->pid, $play)){
		$play[] = $player->pid;

		if (!$player->franchise_id && $x->ops >= .750 && $x->pa < 350){

			$position = position_find($player->id,$x->year);

			print "$player->name_pad $x->year \t $x->pa \t $x->ops \t $position\n";
			
			$count++;
			unset($position);
		}



	}

	








}


print $count;


?>