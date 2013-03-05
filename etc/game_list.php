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



$boxscore_id = $_REQUEST['box'] ? $_REQUEST['box'] : "2012081900300";
$org = 1;


$query = ("
	SELECT boxscore_id FROM scores WHERE season = 18 AND organization = $org AND type = 2 AND played = 1 ORDER BY boxscore_id LIMIT 100
");		

$result = db::Q()->prepare($query);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$box = new Score;
	$box->Information($x->boxscore_id,$org);
	$box->PlayerGame($x->boxscore_id,$org);


	print "http://hoflm.com/$box->file \n";


}



print <<< CONTENT

Batters: $total->batters
Pitchers: $total->pitchers


CONTENT;





?>