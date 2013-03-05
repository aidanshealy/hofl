<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$lsi = $_REQUEST['lsi'];
$ldb = $_REQUEST['ldb'];
$ltr = $_REQUEST['ltr'];
$lhr = $_REQUEST['lhr'];
$rsi = $_REQUEST['rsi'];
$rdb = $_REQUEST['rdb'];
$rtr = $_REQUEST['rtr'];
$rhr = $_REQUEST['rhr'];



if ($_REQUEST['lsi']){
	$pm = substr($lsi, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($lsi, 1);

	$search .= "AND factor_lh_singles $pd $value ";
}
if ($_REQUEST['ldb']){
	$pm = substr($ldb, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($ldb, 1);

	$search .= "AND factor_lh_doubles $pd $value ";
}
if ($_REQUEST['ltr']){
	$pm = substr($ltr, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($ltr, 1);

	$search .= "AND factor_lh_triples $pd $value ";
}
if ($_REQUEST['lhr']){
	$pm = substr($lhr, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($lhr, 1);

	$search .= "AND factor_lh_homeruns $pd $value ";
}
if ($_REQUEST['rsi']){
	$pm = substr($rsi, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($rsi, 1);

	$search .= "AND factor_rh_singles $pd $value ";
}
if ($_REQUEST['rdb']){
	$pm = substr($rdb, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($rdb, 1);

	$search .= "AND factor_rh_doubles $pd $value ";
}
if ($_REQUEST['rtr']){
	$pm = substr($rtr, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($rtr, 1);

	$search .= "AND factor_rh_triples $pd $value ";
}
if ($_REQUEST['rhr']){
	$pm = substr($rhr, 0, 1);
	$pd = $pm == "+" ? ">=" : "<=";
	$value = substr($rhr, 1);

	$search .= "AND factor_rh_homeruns $pd $value ";
}
if ($_REQUEST['cover'] > 0){
	$value = $_REQUEST['cover'];
	$search .= "AND cover = $value ";
}
if ($_REQUEST['surface'] > 0){
	$value = $_REQUEST['surface'];
	$search .= "AND surface = $value ";
}

// Dropdowns
$covers .= !$_REQUEST['cover'] ? "<option value='0' selected='selected'>All</option>" : "<option value='0'>All</option>";
$covers .= $_REQUEST['cover'] == 1 ? "<option value='1' selected='selected'>Open</option>" : "<option value='1'>Open</option>";
$covers .= $_REQUEST['cover'] == 2 ? "<option value='2' selected='selected'>Dome</option>" : "<option value='2'>Dome</option>";
$covers .= $_REQUEST['cover'] == 3 ? "<option value='3' selected='selected'>Retractable</option>" : "<option value='3'>Retractable</option>";

$surfaces .= !$_REQUEST['surface'] ? "<option value='0' selected='selected'>All</option>" : "<option value='0'>All</option>";
$surfaces .= $_REQUEST['surface'] == 1 ? "<option value='1' selected='selected'>Grass</option>" : "<option value='1'>Grass</option>";
$surfaces .= $_REQUEST['surface'] == 2 ? "<option value='2' selected='selected'>Turf</option>" : "<option value='2'>Turf</option>";







if ($_REQUEST['ballpark_digger']){
	$query = ("
		SELECT 
			*
		FROM ballparks_breakdown 
		WHERE 
			banned != 1
			$search
		ORDER BY name, year
		");
	
//	print $query;
	$result = db::Q()->prepare($query);
	$result->bindParam(1, $id);
	$result->execute();
	
	while ($x = $result->fetch(PDO::FETCH_OBJ)){
	
		$count++;
	
		// Look to see if the ballpark is in use
		$yquery = ("
			SELECT 
				*
			FROM team 
			WHERE 
				division > 0
				AND league = 1
				AND destablished <= 1
				AND ballpark = $x->ballpark_id
			ORDER BY city
			");
		
		$yresult = db::Q()->prepare($yquery);
		$yresult->execute();
		
		while ($y = $yresult->fetch(PDO::FETCH_OBJ)){
			
			$league = $y->division == 1 || $y->division == 2 || $y->division == 3 || $y->division == 4 ? "(A)" : "(N)";
			$teams .= "$y->city $league &nbsp;";
			
		}
	
	
		$results .= ("
			<tr class='a-line'>
				<td>$x->name</td>
				<td>$x->year</td>
				<td>$teams</td>
				<td class='r f5 col'>$x->factor_lh_singles</td>
				<td class='r f5'>$x->factor_lh_doubles</td>
				<td class='r f5'>$x->factor_lh_triples</td>
				<td class='r f5'>$x->factor_lh_homeruns</td>
				<td class='f5'>&nbsp;</div>
				<td class='r f5 col'>$x->factor_rh_singles</td>
				<td class='r f5'>$x->factor_rh_doubles</td>
				<td class='r f5'>$x->factor_rh_triples</td>
				<td class='r f5'>$x->factor_rh_homeruns</td>
			
			</tr>
		");
	
	
		unset($teams);
	}
}

$results = $results ? $results : "<tr><td>There are no results for this search.</td></tr>";
$query = $query ? "<div class='bar'>$search</div>" : NULL;

print <<< CONTENT
<html>

<head>
	<link rel='stylesheet' href='/objects/css/global.css' type='text/css' media='screen' />
	<link rel='stylesheet' href='/objects/css/global.php' type='text/css' media='screen' />

	<script language='javascript' src='/objects/third/jquery-1.7.1.min.js' type='text/javascript'></script>
	<script language='javascript' src='/objects/third/jquery-ui-1.8.17.custom.min.js' type='text/javascript'></script>
</head>

<body style='text-align: left; padding: 20px;'>
<div id='content'>

<div class='bar'>
<form method='post'>
<div class='fl sp'>
	<label>L1B</label>
	<input type='text' name='lsi' value='$lsi' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>L2B</label>
	<input type='text' name='ldb' value='$ldb' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>L3B</label>
	<input type='text' name='ltr' value='$ltr' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>LHR</label>
	<input type='text' name='lhr' value='$lhr' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>R1B</label>
	<input type='text' name='rsi' value='$rsi' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>R2B</label>
	<input type='text' name='rdb' value='$rdb' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>R3B</label>
	<input type='text' name='rtr' value='$rtr' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>RHR</label>
	<input type='text' name='rhr' value='$rhr' class='field' maxlenghth='3' style='width: 60px;'/>
</div>
<div class='fl sp'>
	<label>Cover</label>
	<select name='cover'>
	$covers
	</select>
</div>
<div class='fl sp'>
	<label>Surface</label>
	<select name='surface'>
	$surfaces
	</select>
</div>
<div class='fl sp' style='margin-top: 20px;'>
	<input type='submit' name='ballpark_digger' value='Dig' class='submit-thin'/>
</div>
<div class='cl'>&nbsp;</div>
</form>
</div>

$query
<div class='b4'>
	<table>
	<thead>
		<th>Name</th>
		<th>Year</th>
		<th>Tenants</th>
		<th class='r'>L1B</th>
		<th class='r'>L2B</th>
		<th class='r'>L3B</th>
		<th class='r'>LHR</th>
		<th class='r'></th>
		<th class='r'>R1B</th>
		<th class='r'>R2B</th>
		<th class='r'>R3B</th>
		<th class='r'>RHR</th>
	</thead>
	$results
	</table>

	<div style='margin-bottom: 20px;'>Total Ballparks in this result set: $count</div>

	<div class='footnote'>All factors are NOT required to do searches. Must have +/- in front of numbers when doing searches.</div>


</div>


</div>
</body>
</html>
CONTENT;



?>