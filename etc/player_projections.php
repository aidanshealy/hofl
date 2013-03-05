<?
$skip = True;
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");

$season = SEASON;
$org = 1;
$id = $_REQUEST['id'];

// Store Results & Reload
if ($_REQUEST['player_store']){

	$data = array($o->id,$_REQUEST['pid'],$_REQUEST['id']);

	$query = "UPDATE statistics_projection SET oid = ? , pid = ? WHERE id = ? LIMIT 1";
	$result = db::Q()->prepare($query);
	$result->execute($data);
	
	header("Location: /etc/player_projections.php");
}


// Pull back all empty PIDS
$query = ("
	SELECT 
		*
	FROM 
		statistics_projection
	WHERE pid = 0
	ORDER BY raw_last, raw_first
");
$result = db::Q()->prepare($query);
$result->execute();


while ($x = $result->fetch(PDO::FETCH_OBJ)){

	
	if (!$x->pid){
		$form = ("
			<td><input type='text' name='pid' maxlength='5' size='8' /></td>
			<input type='hidden' name='id' value='$x->id'/>
			<td><input type='submit' name='player_store' value='Submit'/>
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
			<td>$x->raw_first $x->raw_last</td>
			<td>$x->team</td>
			$form
		</tr>
		</form>
	");

	unset($form);
}



print <<< CONTENT

<p>Search for the players five-digit ID via hofl.com site.</p>
<p>If no ID exists, just type in "99999"</p>


<table cellpadding='4' cellspacing='1'>
<thead>
<tr>
	<th>Name</th>
	<th>Team</th>
	<th>PID</th>
</tr>
</thead>
$results
</table>

CONTENT;


		
?>