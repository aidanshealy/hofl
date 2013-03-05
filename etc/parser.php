<?
set_time_limit(1800000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/third/parser/BoxScoreParser.php");
header("Content-Type: text/plain");
// header("Content-Type: application/json");

$boxscore = $_REQUEST['id'] ? $_REQUEST['id'] : 2012042300120;

$score = new Score;
$score->Information($boxscore,1);

$boxscore_file = $_SERVER['DOCUMENT_ROOT'] . $score->file;

print "Boxscore: $boxscore_file\n\n";

print "\n\n\n\n\n";

$content = processFile($boxscore_file);

print $content;

print "\n\n\n\n\n";

$file = file_get_contents($boxscore_file);

print "$file";




?>