<?
$noforward = "True";
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
//header("Content-type: text/plain");
header("Content-Type: text/csv");

function echocsv($fields, $upper = 0){
  $separator = '';
  foreach ($fields as $field){
    if ( preg_match( '/\\r|\\n|,|"/', $field )){
      $field = '"' . str_replace( '"', '""', $field ) . '"';
    }

		if ($upper){
  	  echo $separator . strtoupper($field);
    }
    else {
	    echo $separator . $field;
    }
    $separator = ',';
  }
  echo "\r\n";
}




// Pretty basic API type system. Export CSV for whatever data.
$load = $_REQUEST['load'];
$section = $_REQUEST['section'];

	$section_check = array(
										"statistics",
										"mlb_batting",
										"mlb_pitching",
										"statistics_fielding",
										"player_ratings_batters_dmb",
										"player_ratings_batters_league",
										"player_ratings_pitchers_dmb",
										"player_ratings_pitchers_league"
										);
	


	if (in_array($section, $section_check)){
		$query = sprintf("SELECT player.name_first, player.name_last, $section.* FROM $section LEFT JOIN player ON player.pid = $section.pid");
		$result = db::Q()->prepare($query);
		$result->execute();

		$row = $result->fetch(PDO::FETCH_ASSOC);
	
//	  $row = mysql_fetch_assoc($result);

	  if ($row){
	    echocsv(array_keys($row),1);
	  }
	
	  while ($row){
	    echocsv($row);
//	    $row = $result->fetch(PDO::FETCH_ASSOC));
	    $row = $result->fetch(PDO::FETCH_ASSOC);
	  }

	}
	else {
		print "There is no API for the data you have selected.";
	}







?>