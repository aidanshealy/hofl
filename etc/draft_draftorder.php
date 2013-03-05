<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
// header("Content-Type: text/plain");

$draft_id = DRAFT;


// Process
if ($_REQUEST['build_draftorder']){

	$query = "DELETE FROM draft_order WHERE draft_id = $draft_id";
	$result = db::Q()->prepare($query);
	$result->execute();

	for ($i = 1; $i <= 48; $i++) {
		$order = $_REQUEST['order'][$i];
		$team_id = $_REQUEST['team'][$i];

		$team = new Team;
		$team->Code($team_id);
		$team->Information($team->id);
		
		
		print "$order - $team_id ($team->name)<br/>";


		$query = "INSERT INTO draft_order (draft_id,draft_order,franchise_id) VALUES ($draft_id,$order,$team_id)";
		$result = db::Q()->prepare($query);
		$result->execute();


	}

}




$team = new Team;
$team->All();

for ($i = 1; $i <= 48; $i++) {

	$content .= ("
		<input type='hidden' name='order[$i]' value='$i'/>
		<tr>
			<td>$i.</td>
			<td>
				<select name='team[$i]'>
					<option value='NULL'>Select Team</option>
					$team->list_option_id
				</select>
			</td>
		</tr>
	");


}



print <<< CONTENT

<form method='post'>

<table>
$content
</table>

<input type='submit' name='build_draftorder' value='Build Draft Order'/>
</form>

CONTENT;





?>