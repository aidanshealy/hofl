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
	$statistics = new Statistics;
	$statistics->Equations("pwhip");

	$order_stats = $statistics->eq_stats;
	$order_sort = "total $statistics->order";
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
	   AND pinn >= 225
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
			<td class='r'>$player->neutral_pw</td>
			<td class='r'>$player->neutral_pl</td>
			<td class='r col'>$player->neutral_g</td>
			<td class='r'>$player->neutral_gs</td>
			<td class='r'>$player->neutral_pcg</td>
			<td class='r'>$player->neutral_psho</td>
			<td class='r'>$player->neutral_phld</td>
			<td class='r'>$player->neutral_ps</td>

			<td class='r col'>$player->neutral_pinn</td>
			<td class='r'>$player->neutral_ph</td>
			<td class='r'>$player->neutral_phr</td>
			<td class='r'>$player->neutral_pr</td>
			<td class='r'>$player->neutral_per</td>

			<td class='r col'>$player->neutral_pbb</td>
			<td class='r'>$player->neutral_pk</td>

			<td class='r col'>$player->neutral_pera</td>
			<td class='r'>$player->neutral_pwhip</td>
			<td class='r'>$player->neutral_pbabip</td>
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
        <th>Pitcher</th>
        <th>Year</th>
        <th>Age</th>
    		<th class='r f4'><a href='?section=pitching&order=pw'>W</a></th>
    		<th class='r f4'><a href='?section=pitching&order=pl'>L</a></th>
    		<th class='r f4'><a href='?section=pitching&order=g'>G</a></th>
    		<th class='r f4'><a href='?section=pitching&order=gs'>GS</a></th>
    		<th class='r f4'><a href='?section=pitching&order=pcg'>CG</a></th>
    		<th class='r f4'><a href='?section=pitching&order=psh'>SH</a></th>
    		<th class='r f4'><a href='?section=pitching&order=phld'>HLD</a></th>
    		<th class='r f4'><a href='?section=pitching&order=ps'>SV</a></th>
    
    		<th class='r'><a href='?section=pitching&order=pinn'>INN</a></th>
    		<th class='r'><a href='?section=pitching&order=ph'>H</a></th>
    		<th class='r'><a href='?section=pitching&order=phr'>HR</a></th>
    		<th class='r'><a href='?section=pitching&order=pr'>R</a></th>
    		<th class='r'><a href='?section=pitching&order=per'>ER</a></th>
    		<th class='r'><a href='?section=pitching&order=pbb'>BB</a></th>
    		<th class='r'><a href='?section=pitching&order=pk'>K</a></th>
    		<th class='r'><a href='?section=pitching&order=pera'>ERA</a></th>
    		<th class='r'><a href='?section=pitching&order=pwhip'>WHIP</a></th>
    		<th class='r'><a href='?section=pitching&order=pbabip'>BABIP</a></th>
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