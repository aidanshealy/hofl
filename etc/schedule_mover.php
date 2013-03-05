<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$date_base = date("U",mktime(0,0,0,3,31,2013));

// This script moves games based on skipped days from the building of the schedule.


// Offical skip dates for the calendar. 
$date_skip = array(
	"4/10/2013",
	"4/20/2013",
	"4/30/2013",
	"5/13/2013",
	"5/23/2013",
	"5/30/2013",
	"6/9/2013",
	"6/16/2013",
	"6/26/2013",
	"7/9/2013",
	"7/22/2013",
	"8/4/2013",
	"8/14/2013",
	"8/27/2013",
	"9/3/2013",
	"9/10/2013",
	"9/23/2013",
);




for ($i = 1; $i <= 185; $i++) {

	$date_current = $date_base + (86400 * $i);
	$date_display = date("n/j/Y",$date_current);

	if ($date_display != $date_old){
		$add++;
	}
	$date_old = $date_display;

	if ($add == 4){
		unset($add);
		$add = 1;
	}

	$days_since++;


//	$days = (!in_array($date_display,$date_skip) ? 24 : 12);
	if (in_array($date_display,$date_skip)){
		$date_jump = $date_current + (86400 * 3);
		$date_jump_display = date("n/j/Y",$date_jump);

		// Now, pull out three days from now 12 teams and change those dates to current date.
		$query = ("
			SELECT 
				*
			FROM scores_temp
			WHERE date = '$date_jump_display'
			ORDER BY RAND()
			LIMIT 12
		");
		$result = db::Q()->query($query);
		
		while ($x = $result->fetch(PDO::FETCH_OBJ)){
		
			$team = new Team;
			$team->Code($x->home_id);
			$team->Information($team->id);

			$zquery = "UPDATE scores_temp SET date = '$date_display' WHERE id = '$x->id' LIMIT 1";
			$zresult = db::Q()->prepare($zquery);
			$zresult->execute();

		
			print "   - $x->date ($date_display) $team->city ($x->id) $zquery \n";
		
			
		
		
			unset($position);
		}

		$skip = "**** SKIP DAY ($date_jump_display)";
	}

	print "$date_display $skip\n";









	unset($skip);




/*
	for ($j = 1; $j <= $days; $j++){
//		print "<div>$date_display [$j] - $add - $days_since</div>";
			print "$date_display\n";
	}
*/



/*
	if (!in_array($date_display,$date_skip)){

		$count++;
	
		for ($j = 1; $j <= 24; $j++){
			print "<div>$date_display - $add - $days_since</div>";
//			print "<div>$date_display</div>";
		}
	}
	else {
		$skip++;

		print "<div style='color: #F00; font-weight: bold'>SKIP DATE ($skip)</div>";

		$days_since = 0;
		$add = 0;
	
	}
*/
	

}


?>