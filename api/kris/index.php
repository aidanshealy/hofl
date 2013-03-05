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

print "LAH-ID,NAME FIRST,NAME LAST,VINTAGE,POSITION,FRANCHISE NAME,PROTECTED\n";
	
$query = ("
	SELECT 
		team_players.pid 
	FROM team_players 
	LEFT JOIN player ON player.pid = team_players.pid
	WHERE team_players.level != 8
	ORDER BY player.name_last, player.name_first
");
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Vintage($x->pid);
	$player->Protect($x->pid);
	
	$player->yes = $player->protected ? "Yes":"No";
	
	if ($player->franchise_id){
		$team = new Team;
		$team->Code($player->franchise_id);
		$team->Information($team->id);
	}
	else {
		$team->name = "Free Agent";
		$player->level = 0;
	}

	print "$player->id,$player->name_first,$player->name_last,$player->vintage,$player->position,$team->name,$player->yes\n";
}	





?>