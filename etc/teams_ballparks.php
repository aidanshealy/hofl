<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$season = SEASON;
$org = 1;
$id = $_REQUEST['id'];

// Store Results & Reload
if ($_REQUEST['ballpark_store']){

	$data = array($id,$_REQUEST['sid'],$org,$_REQUEST['bid'],$_REQUEST['year']);

	$query = "INSERT INTO team_ballparks (franchise_id,season,organization,ballpark_id,ballpark_year) VALUES (?,?,?,?,?)";
	$result = db::Q()->prepare($query);
	$result->execute($data);
	
	header("Location: /etc/teams_ballparks.php?id=" . $id);
}


if ($_REQUEST['action'] == "delete-ballpark"){

	$data = array($_REQUEST['bid']);

	$query = "DELETE FROM team_ballparks WHERE id = ?";
	$result = db::Q()->prepare($query);
	$result->execute($data);

	header("Location: /etc/teams_ballparks.php?id=" . $id);


}




$all = new Team;
$all->All($id);

// Pull backk all ballparks
$query = "SELECT id,name FROM ballparks ORDER BY name";
$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$ball_select .= "<option value='$x->id'>$x->name</option>";
}




// Pull back all versions of this team historically
$query = ("
	SELECT 
		franchise_id,
		year,
		season
	FROM 
		statistics_team 
	WHERE organization = ? AND franchise_id = ? AND type = 2
	GROUP BY season
	ORDER BY season
");
$result = db::Q()->prepare($query);
$result->bindParam(1, $org);
$result->bindParam(2, $id);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	$team = new Team;
	$team->Code($x->franchise_id,$org,$x->season);
	$team->Information($team->id);
	$team->Ballpark($x->franchise_id,$org,$x->season);
	
	$ballpark = new Ballpark;
	$ballpark->Information($team->ballpark_id);
	
	if (!$ballpark->name){
		$form = ("
			<td><select name='bid'>$ball_select</select></td>
			<td><input type='text' name='year' maxlength='4' size='6' /></td>
			<input type='hidden' name='sid' value='$x->season'/>
			<td><input type='submit' name='ballpark_store' value='Submit'/>
		");
	}
	else {
		$form = ("
			<td><a href='?action=delete-ballpark&bid=$team->row_id&id=$id'>Delete</a></td>
		");
	}
	
	$results .= ("
		<form method='post'>
		<tr>
			<td>$x->season</td>
			<td>$x->year</td>
			<td>$team->name</td>
			<td>$team->ballpark_year</td>
			<td>$ballpark->name</td>
			$form
		</tr>
		</form>
	");

	unset($form);
}



print <<< CONTENT
<div>
<form method='get'>
<select name='id' onchange='this.form.submit();'>
	<option>Select Team</option>
	$all->list_option_id
</select>
</form>
</div>

<table cellpadding='4' cellspacing='1'>
<thead>
<tr>
	<th>Season</th>
	<th>Year</th>
	<th>Team</th>
	<th>BYear</th>
	<th>Ballpark Name</th>
	<th>Select Name</th>
	<th>SYear</th>
</tr>
</thead>
$results
</table>

CONTENT;


		
?>