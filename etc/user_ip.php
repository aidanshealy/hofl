<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");

/*
  "204.101.237.194",
  "204.101.237.195",
  "204.101.237.199",
*/

// Clean up
$id = $_REQUEST['id'];
$skip = array(
  "199.189.250.220",
	"64.102.254.33",
	"71.171.119.186",
  "71.171.119.186", 
  "67.23.229.86",
  "67.69.136.66",
  "64.102.249.6",
  "64.102.249.8",
  "64.102.249.9",
  "50.101.222.187",
  "70.24.113.144",
  "96.247.209.156"
);

$query = ("
	SELECT 
		user_ip
	FROM core
	WHERE user_id = '$id'
	GROUP BY user_ip
");
$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	$ip[] = $x->user_ip;
}


print_r($ip);

foreach ($ip as $v) {
	if (!in_array($v, $skip)){
		$where .= "user_ip = '$v' OR ";
	}
}
$where = rtrim($where,"OR ");

$query = ("
	SELECT 
		user_id
	FROM core
	WHERE $where
	GROUP BY user_id
");

$result = db::Q()->query($query);


while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
	$user = new Owner;
	$user->Information($x->user_id);
	
	print "$user->name \n";


}


?>