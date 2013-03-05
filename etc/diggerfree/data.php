<?
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

parse_str($_REQUEST['data']);

$franchise = FID;
$season = 18;
$type = TYPE;
$org = 1;
$count = 0;

$prev->total = 1000;

$statistic = $equation;

// How many games have been played this season?
// This is primarly for undervalue checkbox
// Minimu Totals
$query = "SELECT DISTINCT(game_seconds) FROM scores WHERE season='$season' AND organization='$org' AND type='$type' AND played='1' AND (home_id = $franchise OR away_id = $franchise)";
$result = db::Q()->prepare($query);
$result->execute();
$games = $result->rowCount();

$batting_total = $games * 3.1;
$low = floor($batting_total * .25);


// Start Building the Query

// Statistical Equation based on what we are looking for.
if ($statistic == "hitter"){
	$equation = "(a.bh + ((a.bsi * 1) + (a.bdb * 2) + (a.btr * 3) + (a.bhr * 4)) + (1.5 * (a.bbb + a.bsb)))/(a.bpa + a.bsb)";
}
else if ($statistic == "hofl"){
	$equation = "(((a.bh + ((a.bsi * 1) + (a.bdb * 2) + (a.btr * 3) + (a.bhr * 4)) + (1.5 * (a.bbb + a.bsb)))/(a.bpa + a.bsb)) * .40) + (((SUM(h.bh) + ((SUM(h.bsi) * 1) + (SUM(h.bdb) * 2) + (SUM(h.btr) * 3) + (SUM(h.bhr) * 4)) + (1.5 * (SUM(h.bbb) + SUM(h.bsb))))/(SUM(h.bpa) + SUM(h.bsb))) * .60)";
	$having_pa = "(SUM(h.bpa) > 250)";
}
else if ($statistic == "extra"){
	$equation = "(a.bh + ((a.bsi * 1) + (a.bdb * 3.5) + (a.btr * 3.5) + (a.bhr * 3)) + (1.5 * (a.bbb + a.bsb)))/(a.bpa + a.bsb)";
}
else if ($statistic == "onbase"){
	$equation = "(a.bh + ((a.bsi * 1) + (a.bdb * 1) + (a.btr * 1) + (a.bhr * 1)) + (2 * (a.bbb + a.bsb)))/(a.bpa + a.bsb)";
}
else if ($statistic == "overall"){
	$equation = "(a.bh + ((a.bsi * 1) + (a.bdb * 2) + (a.btr * 3) + (a.bhr * 4)) + (1.5 * (a.bbb + a.bsb)))/(a.bpa + a.bsb)";
	$modifier = "on";
}
else if ($statistic == "battingruns"){
	// BR = 0.47 x 1B + 0.77 x 2B + 1.04 x 3B + 1.40 x HR + 0.31 x BB + 0.34 x HBP � 0.28 x outs
	// wOBA = (0.71 x (BB � IBB) + 0.74 x HBP + 0.89 x 1B + 1.26 x 2B + 1.58 x 3B + 2.02 x HR + 0.24 x SB � 0.51 x CS)/ (PA � IBB)
	$equation = "(a.bsi * .47) + (a.bdb * .77) + (a.btr * 1.04) + (a.bhr * 1.40) + (a.bbb * .31) + (a.bsb * .42) - (.28 * (a.bab - a.bh))";
	$equation = "(((a.bbb * .71) + (a.bsi * .89) + (a.bdb * 1.26) + (a.btr * 1.58) + (a.bhr * 2.02) + (a.bsb * .42)) / a.bpa)";
}

$position = array($position);

// Position
if ($position){
	$rows = count($position);
	foreach ($position as $v) {
		$set_position .= "a.position = '$v' OR ";
		$query_position = $v;		
	}
	$set_position = rtrim($set_position, " OR ");
	$where_position = $set_position ? "AND ($set_position)" : NULL;
}

// Defense

if ($defense){
	$rows = count($defense);
	foreach ($defense as $v) {
		if ($position){
			if (in_array("CA",$position)){ 
				$set_defense .= "e.ca_arm = '$v' OR "; 
				$set_defense_modify = "IF(e.ca_arm = 'EX', 1.1, IF(e.ca_arm = 'VG', 1.05, IF(e.ca_arm = 'AV', 1.0, IF(e.ca_arm = 'FR', .95, IF(e.ca_arm = 'PR', 0.9, 1)))))";
			}
			if (in_array("1B",$position)){ 
				$set_defense .= "e.fb_r = '$v' OR "; 
				$set_defense_modify = "IF(e.fb_r = 'EX', 1.1, IF(e.fb_r = 'VG', 1.05, IF(e.fb_r = 'AV', 1.0, IF(e.fb_r = 'FR', .95, IF(e.fb_r = 'PR', 0.9, 1)))))";
			}
			if (in_array("2B",$position)){ 
				$set_defense .= "e.sb_r = '$v' OR "; 
				$set_defense_modify = "IF(e.sb_r = 'EX', 1.15, IF(e.sb_r = 'VG', 1.075, IF(e.sb_r = 'AV', 1.0, IF(e.sb_r = 'FR', .925, IF(e.sb_r = 'PR', 0.85, 1)))))";
			}
			if (in_array("3B",$position)){ 
				$set_defense .= "e.tb_r = '$v' OR "; 
				$set_defense_modify = "IF(e.tb_r = 'EX', 1.15, IF(e.tb_r = 'VG', 1.075, IF(e.tb_r = 'AV', 1.0, IF(e.tb_r = 'FR', .925, IF(e.tb_r = 'PR', 0.85, 1)))))";
			}
			if (in_array("SS",$position)){ 
				$set_defense .= "e.ss_r = '$v' OR "; 
				$set_defense_modify = "IF(e.ss_r = 'EX', 1.15, IF(e.ss_r = 'VG', 1.075, IF(e.ss_r = 'AV', 1.0, IF(e.ss_r = 'FR', .925, IF(e.ss_r = 'PR', 0.85, 1)))))";
			}
			if (in_array("LF",$position)){ 
				$set_defense .= "e.lf_r = '$v' OR "; 
				$set_defense_modify = "IF(e.lf_r = 'EX', 1.05, IF(e.lf_r = 'VG', 1.025, IF(e.lf_r = 'AV', 1.0, IF(e.lf_r = 'FR', .975, IF(e.lf_r = 'PR', 0.95, 1)))))";
			}
			if (in_array("CF",$position)){ 
				$set_defense .= "e.cf_r = '$v' OR "; 
				$set_defense_modify = "IF(e.cf_r = 'EX', 1.05, IF(e.cf_r = 'VG', 1.025, IF(e.cf_r = 'AV', 1.0, IF(e.cf_r = 'FR', .975, IF(e.cf_r = 'PR', 0.95, 1)))))";
			}
			if (in_array("RF",$position)){ 
				$set_defense .= "e.rf_r = '$v' OR "; 
				$set_defense_modify = "IF(e.rf_r = 'EX', 1.05, IF(e.rf_r = 'VG', 1.025, IF(e.rf_r = 'AV', 1.0, IF(e.rf_r = 'FR', .975, IF(e.rf_r = 'FR', 0.95, 1)))))";
			}
		}
	}
	$set_defense = rtrim($set_defense, " OR ");
	$where_defense = $set_defense ? "AND ($set_defense)" : NULL;
}

// Modifier Query
if ($set_defense_modify && $modifier == "on"){
	$where_defense_modify = "* ($set_defense_modify)";
}


// Bats
if ($bats){
	$rows = count($bats);
	foreach ($bats as $v) {
		$set_bats .= "b.bats = '$v' OR ";
	}
	$set_bats = rtrim($set_bats, " OR ");
	$where_bats = $set_bats ? "AND ($set_bats)" : NULL;
}

// Minors
if ($majors == 1){
	$where_level = "AND a.level = 1";
}
else if ($minors == 1){
	$where_level = "AND a.level = 2";
}

// Undervalue
if ($undervalue == 1){
	$having_value = "bpa <= $low ";
}

// Non-Starter
if ($nonstarter == 1){
	$where_starter = "AND (a.starter != 2 AND a.starter != 1)";
}
else if ($starter == 1){
	$where_starter = "AND (a.starter = 2 OR a.starter = 1)";
}

// Reset Having Values
if ($having_value && $having_pa){
	$having = "HAVING $having_value AND $having_pa";
}
else if ($having_pa){
	$having = "HAVING $having_pa";
}
else if ($having_value){
	$having = "HAVING $having_value";
}


// PPS
if ($pps == 3){
	$where_pps = " AND a.points = 3";
}

if ($pps == 2){
	$where_pps = " AND a.points = 2";
}

if ($pps == 1){
	$where_pps = " AND a.points = 1";
}


// !MIN and MAX query
/*
$query = ("
	SELECT 
		a.pid,
		f.vintage,
		ROUND(g.bh/g.bab,3) as avg,
		SUM(d.bpa) as bpa,
		($equation $where_defense_modify) as total
	FROM statistics_neutral a
	LEFT JOIN player b ON a.pid = b.pid
	LEFT JOIN team_players c ON c.pid = a.pid
	LEFT JOIN player_ratings e ON a.pid = e.pid AND e.season = a.year AND e.league = 0
	WHERE 
		c.pid IS NULL
		$where_position
		$where_defense
		$where_bats
		$where_level
		$where_starter
		AND g.bpa >= 350
	GROUP BY a.pid, a.year
	$having
	ORDER BY total DESC
	LIMIT 1
");
$result = db::Q()->prepare($query);
$result->execute();

if ($x = $result->fetch(PDO::FETCH_OBJ)){
	$score_max = $x->total;
}

$query = ("
	SELECT 
		a.pid,
		a.starter,
		f.vintage,
		ROUND(g.bh/g.bab,3) as avg,
		SUM(d.bpa) as bpa,
		($equation $where_defense_modify) as total
	FROM team_players a
	LEFT JOIN player b ON a.pid = b.pid
	LEFT JOIN player_role c ON a.pid = c.pid
	LEFT JOIN statistics d ON a.pid = d.pid AND d.season = $season AND d.type = $type AND d.organization = $org
	LEFT JOIN player_ratings e ON a.pid = e.pid AND e.season = $season AND e.league = 1
	LEFT JOIN player_vintage f ON a.pid = f.pid AND f.season = $season
	LEFT JOIN statistics_neutral g ON a.pid = g.pid AND f.vintage = g.year
	LEFT JOIN statistics h ON a.pid = h.pid AND (h.season = 16 OR h.season = 17 OR h.season = 18) AND h.type = $type AND h.organization = $org
	WHERE
		c.role = 'B'
		AND f.vintage IS NOT NULL
		$where_position
		$where_defense
		$where_bats
		$where_level
		$where_starter
	GROUP BY a.pid
	$having
	ORDER BY total ASC
	LIMIT 1
");
$result = db::Q()->prepare($query);
$result->execute();

if ($x = $result->fetch(PDO::FETCH_OBJ)){
	$score_min = $x->total ? $x->total : 0;
}


*/


////////////////////////////////////
////////////////////////////////////
////////////////////////////////////
////////////////////////////////////


// !Player Query

$query = ("
	SELECT 
		a.pid,
		a.year,
		ROUND(a.bh/a.bab,3) as avg,
		SUM(a.bpa) as bpa,
		($equation $where_defense_modify) as total
	FROM statistics_neutral a
	LEFT JOIN player b ON a.pid = b.pid
	LEFT JOIN team_players c ON c.pid = a.pid
	LEFT JOIN player_ratings e ON a.pid = e.pid AND e.year = a.year AND e.league = 0
	LEFT JOIN statistics_vintages f ON a.pid = f.pid AND f.year = a.year 
	WHERE 
		c.pid IS NULL
		$where_defense
		$where_bats
		$where_level
		$where_starter
		AND f.bpa >= 200
		AND a.year BETWEEN $start AND $end

	GROUP BY a.pid, a.year
	$having
	ORDER BY total DESC
	LIMIT 500
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

	$count++;

	$player = new Player;
	$player->Information($x->pid);
	$player->Ratings($x->pid,0,$x->year);
	$player->Neutral($x->year);
	$player->Career();

	if ($player->franchise_id){	
		$team = new Team;
		$team->Code($player->franchise_id);
		$team->Information($team->id);
		$team->Statistics();
	}
	else {
		$team->link_short = "<em class='dim'>Free Agent</em>";
	}

	if ($player->array_ratings){
		foreach ($player->array_ratings as $v) {
			$player->rating_show .= "<div>$v</div>";
		}
	}



	
	// Highlighter Class
	$class = $player->franchise_id == FID ? "d" : NULL;

	// Ranks
	$rank = ($total_past != $x->total ? $count . "." : NULL);
	$total_past = $x->total;

	if ($x->starter == 2 && $player->level == 1){
		$player->starter_value = "S";
		$average->start = $x->total + $average->start;
		$average->count++;
	}
	else if ($x->starter == 1 && $player->level == 1){
		$player->starter_value = "P";
		$average->start = $x->total + $average->start;
		$average->count++;
	}
	else {
		$player->starter_value = NULL;
	}

	// Greater than 5% change?
	$player->change = 100 - (round($x->total / $prev->total,2) * 100);
	$prev->total = $x->total;
	
	if ($player->change >= 5){
		$highlight = $player->change >= 5 ? "d" : NULL;
		$player->change = $player->change . "%";
	}
	else {
		unset($player->change);
		unset($highlight);
	}
	
	
	// Get Historical Fielding Games at this position

	$yquery = ("
		SELECT 
			SUM(g) as g,
			SUM(gs) as gs, 
			SUM(inn) as inn, 
			SUM(po) as po, 
			SUM(a) as a, 
			SUM(e) as e, 
			SUM(dp) as dp, 
			SUM(pb) as pb, 
			SUM(sb) as sb, 
			SUM(cs) as cs
		FROM mlb_fielding
		WHERE pos = '$query_position' AND id = '$player->id' AND year = '$x->year'
		GROUP BY id, year 
	");
	$yresult = db::Q()->prepare($yquery);
	$yresult->execute();

//	print $yquery . "<p>";

	if ($y = $yresult->fetch(PDO::FETCH_OBJ)){
	
		$player->fielding_g = $y->g;

	}	

	
	
	
	
	// Count the Results for Team Based Scoring and Math
	$based->start[$player->franchise_id] = $x->total + $based->start[$player->franchise_id];
	$based->count[$player->franchise_id]++;

	$show = ("
		<tr class='a-line $class'>
			<td>$rank</td>
			<td><strong>$player->link</strong></td>
			<td>$team->link_short</td>
			<td class='col'>$player->rating_show</td>
			<td class='r'>$player->fielding_g</td>
			<td class='col c'>$player->bats</td>
			<td class='c'>$player->throws</td>
			<td class='c'>$x->year</td>
			<td class='col r'><strong>$x->total</strong></td>
			<td class='col r'>$player->neutral_bphr</td>
			<td class='r'>$player->neutral_bebr</td>
			<td class='r'>$player->neutral_bhrr</td>
			<td class='r'>$player->neutral_bbbr</td>
			<td class='r'>$player->neutral_bkr</td>
			<td class='col r'>$player->career_g</td>
			<td class='r'>$player->career_bavg</td>
			<td class='r'>$player->career_bops</td>
			<td class='r'><a href='/player/$player->pid/landing/?freeagent_create=true&player_id=$player->pid&vintage=$x->year' target='_blank'>+ Add to Free Agent</a></td>
		</tr>
	");



	$results .= $show;


	if (!$x->total){
		$missing .= "$x->pid,\n";
	}


}

unset($count);

// Average Score for Starters
$average->pct = $average->count ? round($average->start / $average->count,2) : NULL;


// Look at Team Values
if (is_array($based->start)){

	foreach ($based->start as $k => $v) {
		$based->score[$k] = round($v/$based->count[$k],2);
	}

	arsort($based->score);

	foreach ($based->score as $k => $v) {

		$team = new Team;
		$team->Code($k);
		$team->Information($team->id);

		$team->score = number_format($based->score[$k],1);
		$team->count = $based->count[$k];

		$class = $k == FID ? "d" : NULL;

		$count++;
		// Ranks
		$rank = ($total_past != $team->score ? $count . "." : NULL);
		$total_past = $team->score;
		

		$team_results .= ("
			<tr class='a-line $class'>
				<td>$rank</td>
				<td>$team->link_short</td>
				<td class='r'>$team->count</td>
				<td class='r'>$team->score</td>
			</tr>
		");

	}



}



print <<< CONTENT

<div class='fl' style='width: 100%'>
	<div class='b4'>
		<table>
		<thead>
		<tr>
			<th colspan='2'>$position Results</th>
			<th>Team</th>
			<th>POS</th>
			<th class='r'>G</th>
			<th class='c'>B</th>
			<th class='c'>T</th>
			<th class='c'>VIN</th>
			<th class='r'>Score</th>
			<th class='r'>H%</th>
			<th class='r'>EB%</th>
			<th class='r'>HR%</th>
			<th class='r'>BB%</th>
			<th class='r'>K%</th>
			<th class='r'>CG</th>
			<th class='r'>CAVG</th>
			<th class='r'>COPS</th>
		</tr>	
		</thead>
		$results
		<tr class='total'>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td class='r'>$average->pct</td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
			<td></td>
		</tr>
		</table>
	</div>
</div>


CONTENT;

?>