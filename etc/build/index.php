<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

// The Digger
// This application takes parameters, and finds you the best talent per your request.
$year = $_GET['year'] ? $_GET['year'] : 1916;

// !Player Query

$query = ("
	SELECT 
		a.pid,
		a.team
	FROM
		statistics_vintages a
	LEFT JOIN player b ON a.pid = b.pid
	WHERE
		year = $year
	GROUP BY 
		a.pid
	HAVING SUM(a.bpa) > 0
	ORDER BY b.name_last, b.name_first
");
$result = db::Q()->prepare($query);

// @debug QUERY
/*
print ("
<pre>
$query
</pre>
");
*/
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Historicals($year);

	$count++;

	$results .= ("
		<tr class='a-line'>
			<td>$player->pid</td>
			<td><strong>$player->name_first</strong></td>
			<td><strong>$player->name_last</strong></td>
			<td>$player->name_nick</td>
			<td>$x->team</td>
			<td class='col'>$year</td>
			<td>$player->role</td>
			<td>$player->position</td>
			<td>$player->bats</td>
			<td>$player->throws</td>
			<td class='col r'>$player->vintage_g</td>
			<td class='r'>$player->vintage_gs</td>
			<td class='r'>$player->vintage_bab</td>
			<td class='r'>$player->vintage_bh</td>
			<td class='r'>$player->vintage_bdb</td>
			<td class='r'>$player->vintage_btr</td>
			<td class='r'>$player->vintage_bhr</td>
			<td class='r'>$player->vintage_br</td>
			<td class='r'>$player->vintage_brbi</td>
			<td class='r col'>$player->vintage_bhbp</td>
			<td class='r'>$player->vintage_bbb</td>
			<td class='r'>$player->vintage_biw</td>
			<td class='r'>$player->vintage_bk</td>
			<td class='r'>$player->vintage_bsb</td>
			<td class='r'>$player->vintage_bcs</td>
			<td class='r'>$player->vintage_bsh</td>
			<td class='r'>$player->vintage_bsf</td>
			<td class='r'>$player->vintage_bgdp</td>
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

<h1>$year</h1>

	<div class='b4'>

		<table>
		<thead>
		<tr>
			<th colspan='4'>$count Offensive Results</th>
			<th>Team</th>
			<th>Year</th>
			<th>Role</th>
			<th>POS</th>
			<th>B</th>
			<th>T</th>
			<th class='r'>G</th>
			<th class='r'>GS</th>
			<th class='r'>AB</th>
			<th class='r'>H</th>
			<th class='r'>2B</th>
			<th class='r'>3B</th>
			<th class='r'>HR</th>
			<th class='r'>R</th>
			<th class='r'>RBI</th>
			<th class='r'>HBP</th>
			<th class='r'>BB</th>
			<th class='r'>IW</th>
			<th class='r'>K</th>
			<th class='r'>SB</th>
			<th class='r'>CS</th>
			<th class='r'>SH</th>
			<th class='r'>SF</th>
			<th class='r'>GDP</th>
		</tr>	
		</thead>
		$results
		</table>	
	
	</div>


</div>


</body>

</html>

CONTENT;

?>