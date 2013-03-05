<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");


$order = $_REQUEST['order'] ? $_REQUEST['order'] : NULL;

if ($order){
	$statistics = new Statistics;
	$statistics->Equations($order);

	$order_stats = $statistics->eq_stats;
	$order_sort = "total $statistics->order";
}
else {
	$order_sort = "total DESC";
	$order_stats = "SUM(bhr)";
}


// $eq = new Statistics;
// $eq->Equations("bops");


$query = ("
	SELECT
	 statistics_neutral.pid,
	 year,
	 $order_stats as total
	 FROM statistics_neutral
	 LEFT JOIN team_players ON team_players.pid = statistics_neutral.pid
	 WHERE 
	   year BETWEEN 1955 AND 1959 
	   AND bpa >= 200
	   AND team_players.pid IS NULL
	 GROUP BY statistics_neutral.pid,year
	 ORDER BY $order_sort
	 
");

$result = db::Q()->query($query);

while ($x = $result->fetch(PDO::FETCH_OBJ)){

  $player = new Player;
  $player->Information($x->pid);
  $player->Neutral($x->year);
  
  $rank++;
  
  $player->age = $player->neutral_year - $player->birth_year;
  
  
  $results .= ("
    <tr class='a-line'>
      <td>$rank.</td>
      <td>$player->link</td>
      <td>$player->neutral_year</td>
      <td>$player->age</td>
      <td class='r'>$player->neutral_g</td>
      <td class='r'>$player->neutral_bpa</td>
      <td class='r'>$player->neutral_bab</td>
      <td class='r'>$player->neutral_bh</td>
      <td class='r'>$player->neutral_bdb</td>
      <td class='r'>$player->neutral_btr</td>
      <td class='r'>$player->neutral_bhr</td>
      <td class='r'>$player->neutral_bbbr</td>
      <td class='r'>$player->neutral_bkr</td>
      <td class='r'>$player->neutral_bsb</td>
      <td class='r'>$player->neutral_bavg</td>
      <td class='r'>$player->neutral_bobp</td>
      <td class='r'>$player->neutral_bslg</td>
      <td class='r'>$player->neutral_bops</td>
      <td class='r'>$player->neutral_brc</td>
    </tr>
  ");



}

print <<< CONTENT
<html>

<head>
	<link rel='stylesheet' href='/objects/css/global.php' type='text/css' media='screen' />

	<script language='javascript' src='/objects/third/jquery-1.7.1.min.js' type='text/javascript'></script>
	<script language='javascript' src='/objects/third/jquery-ui-1.8.17.custom.min.js' type='text/javascript'></script>
</head>

<body style='text-align: left; padding: 20px;'>
<div id='content'>

  <div class='cf c14'>
    <div class='b4'>
      <table>
      <thead>
      <tr>
        <th>Rk</th>
        <th>Batter</th>
        <th>Year</th>
        <th>Age</th>
        <th class='r'>G</th>
        <th class='r'>PA</th>
        <th class='r'>ab</th>
        <th class='r'>h</th>
        <th class='r'>2b</th>
        <th class='r'>3b</th>
        <th class='r'>hr</th>
        <th class='r'>bb%</th>
        <th class='r'>k%</th>
        <th class='r'>sb</th>
        <th class='r f4'><a href='?section=batting&order=bavg'>AVG</a></th>
        <th class='r f4'><a href='?section=batting&order=bobp'>OBP</a></th>
        <th class='r f4'><a href='?section=batting&order=bslg'>SLG</a></th>
        <th class='r f4'><a href='?section=batting&order=bops'>OPS</a></th>
        <th class='r f4'><a href='?section=batting&order=brc'>RC</a></th>
      </tr>
      </thead>
      $results
      </table>
    </div>
  </div>


</div>

</body>

</html>

CONTENT;


?>