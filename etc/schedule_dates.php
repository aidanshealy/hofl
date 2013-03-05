<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

$date_base = date("U",mktime(0,0,0,3,31,2013));


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
	$days = (!in_array($date_display,$date_skip) ? 24 : 0);


	for ($j = 1; $j <= $days; $j++){
//		print "<div>$date_display [$j] - $add - $days_since</div>";
			print "$date_display\n";
	}



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