<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");


$stats = new Statistics;
$stats->Equations("pwhip");


// Find Free Agent Batters
$query = ("
	SELECT
		a.pid,
		a.year,
		a.pinn,
		$stats->eq_stats as total
	FROM statistics_neutral a
	LEFT JOIN team_players b ON b.pid = a.pid
	WHERE 
		b.pid IS NULL
		AND pinn >= 300
		AND year BETWEEN 1946 AND 1990
	GROUP BY pid,year
	ORDER BY total
	LIMIT 300
");

/*
print "<pre>";
print $query;
print "</pre>";
*/

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$player = new Player;
	$player->Information($x->pid);
	$player->Ratings($x->pid,0,$x->year);

	foreach ($player->array_ratings as $v){
		$player->pos .= "$v<br>";
	}

	$results .= ("
		<tr>
			<form method='post'>
			<td>$player->link</td>
			<td>$x->year</td>
			<td>$x->total</td>
			<td>$player->pos</td>
			<td>$x->pinn</td>
			<td width='25%'>
				<input type='hidden' name='player_id' value='$player->pid'/>
				<input type='text' name='vintage' placeholder='Year' value='$x->year' class='field' style='width: 50px;' />
				<input type='submit' name='freeagent_create' value='Add' class='submit-thin'/>
			</td>
			</form>
		</tr>
	");

}


print <<< HTML

<table width='100%' border=1>
$results
</table>

HTML;



?>