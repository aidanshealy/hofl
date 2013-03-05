<?
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
ob_start();

echo str_repeat(' ', 4096);

$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");


$order = array(1,12,6,15,50,4,51,17,8,9,23,2,10,21,7,52);
$current = date("U");

for ($i = 1; $i <= 32; $i++){
	
	
	$snake = $i % 2 ? "Top" : "Bottom";

	print "Round: $i\n";	
	print "Order: $snake\n";	
	
	if ($i % 2){
		ksort($order);
	}
	else {
		krsort($order);
	}
	
	foreach ($order as $v) {
		$draft_pick++;

		$dataset = array(25,$i,$draft_pick,$v,$current);

		$insert = "INSERT INTO draft_selections (draft_id,draft_round,draft_pick,franchise_to,timestamp) VALUES (?,?,?,?,?)";
		$xresult = db::Q()->prepare($insert);
		$xresult->execute($dataset);

	}	

	
	print "\n\n";
	ob_flush(); flush();

}






?>