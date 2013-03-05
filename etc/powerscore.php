<?
@apache_setenv('no-gzip', 1);
@ini_set('zlib.output_compression', 0);
@ini_set('implicit_flush', 1);
ob_start();
for ($i = 0; $i < ob_get_level(); $i++) { ob_end_flush(); }
ob_implicit_flush(1);

set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

echo str_repeat(' ', 4096);

// $today = date("U",mktime(0,0,0,4,3,2012));
// Just run today
/*
$today = date("U");

$team = new Team;
$team->PowerScore($today,ORG,SEASON,2,1);

print $team->debug;
*/


// Run whole season up to this point

// Figure out days
$start_raw = date("U",mktime(0,0,0,4,1,2012));
$days_raw = date("U") - $start_raw;
$days = round($days_raw/86400);

print $days . "\n\n";

for ($i = 1; $i <= $days; $i++) {
	$current = ($i * 86400) + $start_raw + 4000;

	$team = new Team;
	$team->PowerScore($current,ORG,SEASON,2,1);
	
	echo "$i. " . convert_timestamp($current,1) . " ($current)\n" . $team->debug_top . "\n";

	ob_flush();
	flush();
//	sleep(1);
}






?>