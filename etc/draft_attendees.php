<?
$noforward = "True";
require($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
// header("Content-Type: text/plain");

$draft_id = DRAFT;


// Process
if ($_REQUEST['build_draftattendee']){

	foreach ($_REQUEST['draft_id'] as $draft){
		$dataset = array($draft, $_REQUEST['owner_id']);
	
		$query = "INSERT INTO draft_attendees (draft_id,owner_id) VALUES (?,?)";
		$result = db::Q()->prepare($query);
		$result->execute($dataset);
	}

}

$query = "SELECT * FROM draft WHERE organization = 1 AND rounds > 4 ORDER BY season, year";

$result = db::Q()->prepare($query);
$result->execute();

while ($x = $result->fetch(PDO::FETCH_OBJ)){
	$checkboxes .= "<div><input type='checkbox' name='draft_id[]' value='$x->draft_id'> $x->name</div>";
}	



$owners = new Owner;
$owners->All(1);



print <<< CONTENT

<form method='post'>

<select name='owner_id'>
<option>Select Owner</option>
$owners->option
</select>

$checkboxes



<input type='submit' name='build_draftattendee' value='Add Attendee'/>
</form>

CONTENT;





?>